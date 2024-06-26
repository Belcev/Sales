<?php

namespace App\Factory;

use App\Model\SaleModel;
use App\Model\TagModel;
use App\Model\UserModel;
use Nette\Application\UI\Presenter;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;

class SaleGridFactory {

	private SaleModel $saleModel;
	private TagModel $tagModel;
	private UserModel $userModel;

	function __construct(
		SaleModel $saleModel,
		TagModel $tagModel,
		UserModel $userModel
	){
		$this->saleModel = $saleModel;
		$this->tagModel = $tagModel;
		$this->userModel = $userModel;
	}

	function getSalesGrid(Presenter $presenter) : DataGrid {
		$grid = new DataGrid();

		$grid->setDataSource($this->saleModel->getTable());
		$grid->setItemsPerPageList([5, 10, 30], true);


		$this->getGrid($grid);

		$grid->setDefaultSort(['active_from' => 'ASC']);


		$this->newSaleLine($grid, $presenter);
		$this->editSaleLine($grid, $presenter);
		$this->addActionDelete($grid);
		$this->setColor($grid);


		return $grid;
	}

	private function getGrid(DataGrid $grid) {
		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('name', 'Jméno')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('created_by_id', 'Vložil Uživatel')
			->setRenderer(fn($row) => $this->userModel->getUserName($row->created_by_id));

		$grid->addColumnDateTime('created_at', 'Vložil Datum')
			->setSortable()
			->setFilterDateRange();

		$grid->addColumnDateTime('active_from', 'Aktivní od')
			->setSortable()
			->setFilterDateRange();

		$grid->addColumnDateTime('active_to', 'Aktivní do')
			->setSortable()
			->setFilterDateRange();

		$grid->addColumnText('color', 'barva')
			->setRenderer(function ($row) {
				$rgb = str_split($row->color, 2);
				$rgb = array_map('hexdec', $rgb);
				$rgb = implode(', ', $rgb);
				return "#{$row->color} ($rgb)";
			});

		// vazba Tag a Sale je M:N vyjádřená v tabulce sale_tag
		$grid->addColumnText('tags', 'Tagy')
			->setRenderer(fn($row) => implode(', ', $this->tagModel->getTagsBySaleId($row->id)));
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

			$container->addMultiSelect('tags' , 'Tagy', $this->tagModel->getPossibleTags())
				->setHtmlAttribute('class', 'form-select')
				->setRequired('Je potřeba vybrat alespoň jeden Tag');

		};

		$inlineAdd->onSubmit[] = function ($values) use ($presenter): void {
			$this->saleModel->doInsert($values, $presenter->getUser()->getId());
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

			$container->addMultiSelect('tags' , 'Tagy', $this->tagModel->getPossibleTags())
				->setHtmlAttribute('class', 'form-select')
				->setRequired('Je potřeba vybrat alespoň jeden Tag');
		};

		$inlineEdit->onSetDefaults[] = function (Container $container, ActiveRow $values): void {
			$container->setDefaults([
				'name' => $values['name'],
				'active_from' => $values['active_from'],
				'active_to' => $values['active_to'],
				'color' => str_replace('#', '', $values['color']),
				'tags' => $this->tagModel->getTagsIdBySaleId($values->id),
			]);
		};

		$inlineEdit->onSubmit[] = function ($id, $values) use ($presenter): void {
			$this->saleModel->doEdit($id, $values, $presenter->getUser()->getId());
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



	private function setColor(DataGrid $grid) {
		$grid->setRowCallback(function($item, Html $tr) {
			$now = new \DateTime();
			switch (true) {
				case $item->active_from > $item->active_to:
					$tr->addClass('table-danger'); // chybné
					break;
				case $item->active_from >= $now:
					$tr->addClass('table-info'); // budoucí
					break;
				case $item->active_to <= $now:
					$tr->addClass('table-warning'); // minulé
					break;
				default:
					$tr->addClass('table-success'); // aktivní
					break;
			}
		});
	}
}