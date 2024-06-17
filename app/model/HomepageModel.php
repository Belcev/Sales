<?php

namespace App\Model;

use App\Entity\Sale;
use Nette\Database\Explorer;

class HomepageModel {

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
		$rows = $this
			->database
			->table('sale')
			->where('active_from <= NOW() AND active_to >= NOW()')
			->fetchAll();

		return array_map([$this, 'mapRowToSale'], $rows);
	}

	function getNextSale() : ?Sale {
		$row = $this
			->database
			->table('sale')
			->where('active_from >= NOW()')
			->order('active_from ASC')
			->limit(1)
			->fetch();

		return $row ? $this->mapRowToSale($row) : null;
	}

	private function mapRowToSale($row): Sale
	{
		$sale = new Sale(
			$row->id,
			$row->name,
			new \DateTime($row->created_at),
			$row->created_by_id,
			new \DateTime($row->updated_at),
			$row->updated_by_id,
			new \DateTime($row->active_from),
			new \DateTime($row->active_to),
			$row->color
		);

		return $this->fillTags($row, $sale);
	}


	private function fillTags($row, $sale) {
		// získej tagy z tabuky tag podle m:n tabulky sale_tag
		$tags = $this->database
			->table('sale_tag')
			->where('sale_id', $row->id)
			->fetchAll();

		$possibleTags = $this->database->table('tag')->fetchPairs('id', 'name');;
		$tags = array_map(function($tag) use ($possibleTags) {
			return $possibleTags[$tag->tag_id];
		}, $tags);
		$sale->setTags($tags);

		return $sale;
	}


}