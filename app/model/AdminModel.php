<?php

namespace App\Model;

use App\Factory\SaleGridFactory;
use App\Factory\UserGridFactory;
use Nette\Application\UI\Presenter;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Forms\Container;
use Ublaboo\DataGrid\DataGrid;

class AdminModel {

	private Explorer $database;
	private SaleGridFactory $saleGridFactory;
	private UserGridFactory $userGridFactory;

	function __construct(
		Explorer $database,
		SaleGridFactory $saleGridFactory,
		UserGridFactory $userGridFactory
	){
		$this->database = $database;
		$this->saleGridFactory = $saleGridFactory;
		$this->userGridFactory = $userGridFactory;
	}

	function canUserSeeUsers(int $userId) : bool {
		$user = $this->database->table('user')->get($userId);
		return $user->role === 'admin';
	}


	function getSalesGrid(Presenter $presenter) : DataGrid {
		return $this->saleGridFactory->getSalesGrid($presenter);
	}

	function getUserGrid(Presenter $presenter) : DataGrid {
		return $this->userGridFactory->getUserGrid($presenter);
	}

	function deleteSale($id) {
		$this->database->table('sale_tag')->where('sale_id', $id)->delete();
		$this->database->table('sale')->where('id', $id)->delete();
	}
}