<?php

namespace App\Presenters;

use App\Model\AdminModel;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;


class AdminPresenter extends Presenter {

	private AdminModel $model;

	function __construct(
		AdminModel $model
	) {
		$this->model = $model;
		parent::__construct();
	}


	protected function startup(): void {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Login:');
		}
	}

	function renderDefault() {
		$this->getTemplate()->canSeeUsers = $this->model->canUserSeeUsers($this->user->id);
	}


	function createComponentSalesGrid(): DataGrid {
		return $this->model->getSalesGrid($this);
	}

	function createComponentUserGrid(): DataGrid {
		return $this->model->getUserGrid($this);
	}

	function handleDeleteSale($id) {
		$this->model->deleteSale($id);
		$this->flashMessage('Smazáno', 'success');
		$this->redirect('this');

	}


}
