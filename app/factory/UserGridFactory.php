<?php

namespace App\Factory;

use App\Model\UserModel;
use Nette\Application\UI\Presenter;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;

class UserGridFactory {

	private UserModel $userModel;

	function __construct(
		UserModel $userModel
	){
		$this->userModel = $userModel;
	}

	function getUserGrid(Presenter $presenter) : DataGrid {
		$grid = new DataGrid();

		$grid->setDataSource($this->userModel->getTable());
		$grid->setItemsPerPageList([5, 10, 30], true);

		$this->getGrid($grid);
		$this->newLine($grid, $presenter);
		$this->editLine($grid, $presenter);
		$this->addActionDelete($grid, $presenter);
		$this->addActionPassword($grid);
		$this->addActionActivate($grid, $presenter);
		$this->setColor($grid, $presenter);
		return $grid;
	}

	private function getGrid(DataGrid $grid) {
		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('username', 'Uživatelské jméno')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('role', 'Role')
			->setFilterSelect(['admin' => 'admin', 'user' => 'user']);

		$grid->addColumnText('first_name', 'Jméno')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('last_name', 'Příjmení')
			->setSortable()
			->setFilterText();

		$grid->addColumnText('active', 'Aktivní')
			->setRenderer(fn(ActiveRow $row) => $row->active ? 'Ano' : 'Ne')
			->setFilterSelect(['1' => 'Ano', '0' => 'Ne']);
	}


	private function newLine(DataGrid $grid, Presenter $presenter) {
		$inlineAdd = $grid->addInlineAdd();

		$inlineAdd->setPositionTop()
			->onControlAdd[] = function (Container $container): void {
			$container->addText('username', 'Uživatelské jméno')
				->setRequired('%label je potřeba vyplnit');

			$container->addSelect('role', 'Role', ['admin' => 'admin', 'user' => 'user']);

			$container->addText('first_name', 'Jméno')
				->setRequired('%label je potřeba vyplnit');

			$container->addText('last_name', 'Příjmení')
				->setRequired('%label je potřeba vyplnit');

		};

		$inlineAdd->onSubmit[] = function ($values) use ($presenter): void {
			$this->userModel->doInsert($values);
			$presenter->flashMessage('přidáno', 'success');
			$presenter->redrawControl('flashes');
		};
	}

	private function editLine(DataGrid $grid, Presenter $presenter) {
		$inlineEdit = $grid->addInlineEdit();

		$inlineEdit->onControlAdd[] = function (Container $container): void {
			$container->addText('username', 'Uživatelské jméno')
				->setRequired('%label je potřeba vyplnit');

			$container->addSelect('role', 'Role', ['admin' => 'admin', 'user' => 'user']);

			$container->addText('first_name', 'Jméno')
				->setRequired('%label je potřeba vyplnit');

			$container->addText('last_name', 'Příjmení')
				->setRequired('%label je potřeba vyplnit');
		};

		$inlineEdit->onSetDefaults[] = function (Container $container, ActiveRow $values): void {
			$container->setDefaults([
				'username' => $values['username'],
				'role' => $values['role'],
				'first_name' => $values['first_name'],
				'last_name' => $values['last_name'],
			]);
		};

		$inlineEdit->onSubmit[] = function ($id, $values) use ($presenter): void {
			$this->userModel->doEdit($id, $values);
			$presenter->flashMessage('Record was updated!', 'success');
			$presenter->redrawControl('flashes');
		};

		$inlineEdit->setShowNonEditingColumns();

	}

	private function addActionDelete(DataGrid $grid, Presenter $presenter) {
		$grid->addAction('delete', '', 'DeleteUser!', ['id' => 'id'])
			->setIcon('trash')
			->setTitle('Delete')
			->setClass('btn btn-xs btn-danger ajax')
			->setRenderCondition(function (ActiveRow $row) use ($presenter): bool {
				return $row->id !== $presenter->user->id;
			});
	}

	private function addActionPassword(DataGrid $grid) {
		$grid->addAction('password', '', 'ChangePassword!', ['user_id' => 'id'])
			->setIcon('key')
			->setTitle('Change password')
			->setClass('btn btn-xs btn-warning ajax');
	}

	private function addActionActivate(DataGrid $grid, Presenter $presenter) {
		$grid->addAction('active', '', 'ActivateUser!', ['id' => 'id', 'active' => 'active'])
			->setIcon('check')
			->setTitle('Aktivovat')
			->setClass('btn btn-xs btn-success ajax')
			->setRenderCondition(function (ActiveRow $row) use ($presenter): bool {
				return $row->id !== $presenter->user->id && $row->active == false;
			});

		$grid->addAction('deactive', '', 'ActivateUser!', ['id' => 'id', 'active' => 'active'])
			->setIcon('xmark')
			->setTitle('Deaktivovat')
			->setClass('btn btn-xs btn-warning ajax')
			->setRenderCondition(function (ActiveRow $row) use ($presenter): bool {
				return $row->id !== $presenter->user->id &&  $row->active == true;
			});
	}

	private function setColor(DataGrid $grid, Presenter $presenter) {
		$grid->setRowCallback(function($item, Html $tr) use ($presenter) {
			switch (true) {
				case $item->id == $presenter->user->id:
					$tr->addClass('table-success');
					break;
				case !$item->active:
					$tr->addClass('table-secondary');
					break;
			}
		});
	}
}