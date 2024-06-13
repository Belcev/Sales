<?php

namespace App\Factory;

use Nette\Application\UI\Presenter;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;
use Ublaboo\DataGrid\DataGrid;

class UserGridFactory
{

	private Explorer $database;

	function __construct(
		Explorer $database
	){
		$this->database = $database;
	}

	function getUserGrid(Presenter $presenter) : DataGrid {
		$grid = new DataGrid();

		$grid->setDataSource($this->database->table('sale'));
		$grid->setItemsPerPageList([20, 50, 100], true);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('name', 'Jméno')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('created_by_id', 'Vložil Uživatel')
			->setRenderer(function ($row) {
				return $this->database->table('user')->get($row->created_by_id)->name;
			});

		$grid->addColumnDateTime('created_at', 'Vložil Datum')
			->setSortable()
			->setFilterDateRange();

		$grid->addColumnDateTime('active_from', 'Aktivní od')
			->setSortable()
			->setFilterDateRange();

		$grid->addColumnDateTime('active_to', 'Aktivní do')
			->setSortable()
			->setFilterDateRange();

		$grid->addColumnDateTime('color', 'barva')
			->setFilterDateRange();

		// vazba Tag a Sale je M:N vyjádřená v tabulce sale_tag
		$grid->addColumnText('tags', 'Tagy')
			->setRenderer(function ($row) {
				$tags = [];
				foreach ($this->database->table('sale_tag')->where('sale_id', $row->id) as $sale_tag) {
					$tags[] = $this->database->table('tag')->get($sale_tag->tag_id)->name;
				}
				return implode(', ', $tags);
			});

		$grid->setDefaultSort(['created_at' => 'ASC']);


		$this->newSaleLine($grid, $presenter);
		$this->editSaleLine($grid, $presenter);
		$this->addActionDelete($grid);


		return $grid;
	}


	private function newSaleLine(DataGrid $grid, Presenter $presenter) {
		$inlineAdd = $grid->addInlineAdd();

		$inlineAdd->setPositionTop()
			->onControlAdd[] = function (Container $container): void {
			$container->addText('name', 'Jméno')
				->setRequired('%label je potřeba vyplnit');

			$container->addDateTime('active_from', 'Aktivní od')
				->setRequired('%label je potřeba vyplnit');

			$container->addDateTime('active_to', 'Aktivní do')
				->setRequired('%label je potřeba vyplnit');

			$container->addColor('color', 'barva')
				->setRequired('%label je potřeba vyplnit');

			$container->addMultiSelect('tags' , 'Tagy', $this->database->table('tag')->fetchPairs('id', 'name'))
				->setHtmlAttribute('class', 'form-select')
				->setRequired('Je potřeba vybrat alespoň jeden Tag');

		};

		$inlineAdd->onSubmit[] = function ($values) use ($presenter): void {

			$newRow = $this->database->table('sale')->insert(
				[
					'name' => $values['name'],
					'active_from' => $values['active_from'],
					'active_to' => $values['active_to'],
					'color' => str_replace('#', '', $values['color']),
					'created_by_id' => $presenter->getUser()->getId(),
					'created_at' => new \DateTime(),
					'updated_by_id' => $presenter->getUser()->getId(),
					'updated_at' => new \DateTime(),
				]
			);


			if ($values['tags']) {
				$this->database->table('sale_tag')->insert(
					array_map(function ($tag_id) use ($newRow) {
						return [
							'sale_id' => $newRow->id,
							'tag_id' => $tag_id,
						];
					}, $values['tags']));
			}

			$presenter->flashMessage('přidáno', 'success');
			$presenter->redrawControl('flashes');
		};
	}

	private function editSaleLine(DataGrid $grid, Presenter $presenter) {
		$inlineEdit = $grid->addInlineEdit();

		$inlineEdit->onControlAdd[] = function (Container $container): void {
			$container->addText('name', 'Jméno')
				->setRequired('%label je potřeba vyplnit');

			$container->addDateTime('active_from', 'Aktivní od')
				->setRequired('%label je potřeba vyplnit');

			$container->addDateTime('active_to', 'Aktivní do')
				->setRequired('%label je potřeba vyplnit');

			$container->addColor('color', 'barva')
				->setRequired('%label je potřeba vyplnit');

			$container->addMultiSelect('tags' , 'Tagy', $this->database->table('tag')->fetchPairs('id', 'name'))
				->setHtmlAttribute('class', 'form-select')
				->setRequired('Je potřeba vybrat alespoň jeden Tag');
		};

		$inlineEdit->onSetDefaults[] = function (Container $container, ActiveRow $values): void {
			$container->setDefaults([
				'name' => $values['name'],
				'active_from' => $values['active_from'],
				'active_to' => $values['active_to'],
				'color' => str_replace('#', '', $values['color']),
				'tags' => $this->database->table('sale_tag')->where('sale_id', $values->id)->fetchPairs('tag_id', 'tag_id'),
			]);
		};

		$inlineEdit->onSubmit[] = function ($id, $values) use ($presenter): void {

			$this->database->table('sale')
				->where('id', $id)
				->update(
					[
						'id' => $id, // id je potřeba pro where, jinak by se mohlo stát, že by se změnilo id na 'null
						'name' => $values['name'],
						'active_from' => $values['active_from'],
						'active_to' => $values['active_to'],
						'color' => str_replace('#', '', $values['color']),
						'updated_by_id' => $presenter->getUser()->getId(),
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


			$presenter->flashMessage('Record was updated!', 'success');
			$presenter->redrawControl('flashes');
		};

		$inlineEdit->setShowNonEditingColumns();

	}

	private function addActionDelete(DataGrid $grid) {
		$grid->addAction('delete', '', 'DeleteSale!', ['id' => 'id'])
			->setIcon('trash')
			->setTitle('Delete')
			->setClass('btn btn-xs btn-danger ajax');
	}
}