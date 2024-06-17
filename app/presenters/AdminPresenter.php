<?php

namespace App\Presenters;

use App\Factory\PassEditFormFactory;
use App\Model\AdminModel;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;


class AdminPresenter extends Presenter {

	private AdminModel $model;
	private PassEditFormFactory $passEditFormFactory;

	function __construct(
		AdminModel $model,
		PassEditFormFactory $passEditFormFactory
	) {
		$this->model = $model;
		$this->passEditFormFactory = $passEditFormFactory;
		parent::__construct();
	}


	protected function startup(): void {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Login:');
		}
	}

	function actionDefault() {
		$this->getTemplate()->canSeeUsers = $this->model->canUserSeeUsers($this->user->id);
		$this->getTemplate()->showModal = false;
	}

	private function checkAdmin() {
		if ($this->user->roles[0] !== 'admin') {
			$this->flashMessage('Nemáte dostatečná oprávnění', 'danger');
			$this->redirect('Homepage:');
		}
	}


	function createComponentSalesGrid(): DataGrid {
		return $this->model->getSalesGrid($this);
	}

	function createComponentUserGrid(): DataGrid {
		return $this->model->getUserGrid($this);
	}

	function handleDeleteSale($id) {
		try {
			$this->model->doDeleteSale($id);
			$this->flashMessage('Smazáno', 'success');
		} catch (\Exception $e) {
			$this->flashMessage('Nepodařilo se smazat Slevu', 'danger');
		}
		$this->redirect('this');
	}

	function handleDeleteUser($id) {
		$this->checkAdmin();
		if ($this->user->id === $id) {
			$this->flashMessage('Nelze smazat sám sebe', 'danger');
			$this->redirect('this');
		}

		try {
			$this->model->doDeleteUser($id);
			$this->flashMessage('Smazáno', 'success');
		} catch (\Exception $e) {
			$this->flashMessage('Nelze smazat uživatele, který je evidován u nějaké Slevy', 'danger');
		}
		$this->redirect('this');
	}

	function handleChangePassword($user_id) {
		$this->checkAdmin();
		$this->getTemplate()->showModal = true;
		$this->redrawControl('editFormSnippet');
	}

	function createComponentPassEditForm() : Form {
		$form =  $this->passEditFormFactory->create(
			$this->getParameter('user_id')
		);

		$form->onSuccess[] = function() : void {
			$this->flashMessage('Heslo změněno', 'success');
			$this->redirect('this');
		};

		return $form;
	}

	function handleActivateUser($id, $active) {
		$this->checkAdmin();
		$this->model->doActivateUser($id, !$active);
		$this->redirect('this');
	}


}
