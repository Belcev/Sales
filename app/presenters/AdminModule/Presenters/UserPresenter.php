<?php

namespace App\AdminModule\Presenters;

use App\Factory\PassEditFormFactory;
use App\Factory\SaleGridFactory;
use App\Factory\UserGridFactory;
use App\Model\SaleModel;
use App\Model\UserModel;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;


class UserPresenter extends Presenter {

	/** UserModel @inject  */
	public UserModel $userModel;

	/** SaleModel @inject  */
	public SaleModel $SaleModel;

	/** SaleGridFactory @inject  */
	public SaleGridFactory $saleGridFactory;

	/** UserGridFactory @inject  */
	public UserGridFactory $userGridFactory;

	/** PassEditFormFactory @inject  */
	public PassEditFormFactory $passEditFormFactory;



	protected function startup(): void {
		parent::startup();
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('Login:');
		}
	}

	function actionDefault() {
		$this->getTemplate()->showModal = false;
	}

	private function checkAdmin() {
		if ($this->user->roles[0] !== 'admin') {
			$this->flashMessage('Nemáte dostatečná oprávnění', 'danger');
			$this->redirect('Homepage:');
		}
	}


	function createComponentSalesGrid(): DataGrid {
		return $this->saleGridFactory->getSalesGrid($this);
	}

	function createComponentUserGrid(): DataGrid {
		return $this->userGridFactory->getUserGrid($this);
	}

	function handleDeleteSale($id) {
		try {
			$this->SaleModel->doDeleteSale($id);
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
			$this->userModel->doDeleteUser($id);
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
		$this->userModel->doActivateUser($id, !$active);
		$this->redirect('this');
	}


}
