<?php

namespace App\Model;

use App\Entity\Sale;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

class SaleModel {

	private Explorer $database;

	function __construct(
		Explorer $database
	){
		$this->database = $database;
	}

	/**
	 * @return Sale[]
	 */
	function getCurrentSales() : array {
		$ret = $this->database->query('
		    SELECT sale.*, GROUP_CONCAT(tag.name) AS tags
		    FROM sale
		    LEFT JOIN sale_tag ON sale.id = sale_tag.sale_id
		    LEFT JOIN tag ON sale_tag.tag_id = tag.id
		    WHERE sale.active_from <= NOW() AND sale.active_to >= NOW()
		    GROUP BY sale.id
		')->fetchAll();

		foreach ($ret as $key => $value) {
			$ret[$key] = new Sale($value);
		}

		return $ret;

	}

	function getNextSale(): ?Sale {
		$ret = $this->database->query('
		    SELECT sale.*, GROUP_CONCAT(tag.name) AS tags
		    FROM sale
		    LEFT JOIN sale_tag ON sale.id = sale_tag.sale_id
		    LEFT JOIN tag ON sale_tag.tag_id = tag.id
		    WHERE sale.active_from >= NOW()
		    GROUP BY sale.id
		')->fetchAll();

		return count($ret) > 0 ? new Sale($ret[0]) : null;

	}

	function doDeleteSale(int $id) {
		$this->database->table('sale_tag')->where('sale_id', $id)->delete();
		$this->database->table('sale')->where('id', $id)->delete();
	}

	function getTable(): Selection {
		return $this->database->table('sale');
	}

	function doInsert(ArrayHash $values, int $loggedUserId) : void {
		$newRow = $this->database
			->table('sale')
			->insert(
			[
				'name' => $values['name'],
				'active_from' => $values['active_from'],
				'active_to' => $values['active_to'],
				'color' => str_replace('#', '', $values['color']),
				'created_by_id' => $loggedUserId,
				'created_at' => new \DateTime(),
				'updated_by_id' => $loggedUserId,
				'updated_at' => new \DateTime(),
			]);

		if ($values['tags']) {
			$this->database->table('sale_tag')->insert(
				array_map(function ($tag_id) use ($newRow) {
					return [
						'sale_id' => $newRow->id,
						'tag_id' => $tag_id,
					];
				}, $values['tags']));
		}
	}

	function doEdit(int $id, ArrayHash $values, int $loggedUserId) {
		$this->database->table('sale')
			->where('id', $id)
			->update(
				[
					'id' => $id, // id je potřeba pro where, jinak by se mohlo stát, že by se změnilo id na 'null
					'name' => $values['name'],
					'active_from' => $values['active_from'],
					'active_to' => $values['active_to'],
					'color' => str_replace('#', '', $values['color']),
					'updated_by_id' => $loggedUserId,
					'updated_at' => new \DateTime(),
				]
			);

		$tags = $this->database->table('sale_tag')->where('sale_id', $id);
		if($tags->count() > 0) {
			$tags->delete();
		}

		if ($values['tags']) {
			$this->database->table('sale_tag')->insert(
				array_map(function ($tag_id) use ($id) {
					return [
						'sale_id' => $id,
						'tag_id' => $tag_id,
					];
				}, $values['tags']));
		}
	}

}