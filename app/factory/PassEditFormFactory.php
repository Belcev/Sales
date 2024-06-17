<?php

namespace App\Factory;

use Nette\Application\UI\Form;
use Nette\Database\Explorer;
use Nette\Security\Passwords;

class PassEditFormFactory
{

	private Explorer $database;
	private Passwords $passwords;

	function __construct(
		Explorer $database,
		Passwords $passwords
	){
		$this->database = $database;
		$this->passwords = $passwords;
	}


	function create($userId) : Form {
		$form = new Form();

		$form->addHidden('userId', $userId);

		$form->addPassword('password', 'Heslo')
			->setHtmlAttribute('class', 'form-control')
			->setRequired()
			->addRule($form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaků', 6);

		$form->addPassword('password2', 'Heslo znovu')
			->setHtmlAttribute('class', 'form-control')
			->setRequired()
			->addRule($form::EQUAL, 'Hesla se neshodují', $form['password']);

		$form->addSubmit('send', 'Uložit')
			->setHtmlAttribute('class', 'btn btn-success');

		$form->onSuccess[] = function(Form $form, $values) {
			$this->database->table('user')
				->wherePrimary($values->userId)
				->update([
					'password' => $this->passwords->hash($values->password)
				]);
		};
		return $form;
	}


}