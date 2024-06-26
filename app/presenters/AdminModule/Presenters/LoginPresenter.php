<?php

namespace App\AdminModule\Presenters;

use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;

class LoginPresenter extends Presenter {


	function renderDefault() {
	}

	protected function createComponentLoginForm(): Form
	{
		$form = new Form;
		$form->addText('login', 'Login:')
			->setHtmlAttribute('class', 'form-control')
			->setRequired('Prosím zadejte své přihlašovací jméno.');

		$form->addPassword('password', 'Heslo:')
			->setHtmlAttribute('class', 'form-control')
			->setRequired('Prosím zadejte své heslo.');

		$form->addSubmit('send', 'Login')
			->setHtmlAttribute('class', 'btn btn-outline-success w-100  ');

		$form->onSuccess[] = function (Form $form, \stdClass $values) {
			try {
				$this->getUser()->login($values->login, $values->password);
				$this->redirect('Homepage:');
			} catch (AuthenticationException $e) {
				$form->addError($e->getMessage());
			}
		};
		return $form;
	}

	function actionLogout(): void {
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.', 'success');
		$this->redirect('Login:');
	}


}
