<?php

namespace App\Security;

use Nette;
use Nette\Database\Explorer;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class MyAuthenticator implements Nette\Security\Authenticator
{
	private Explorer $database;
	private Passwords $passwords;

	function __construct(
		Explorer $database,
		Passwords $passwords
	) {
		$this->database = $database;
		$this->passwords = $passwords;
	}

	function authenticate(string $username, string $password): SimpleIdentity {

		$row = $this->database->table('user')
			->where('username', $username)
			->fetch();

		if (!$row || !$this->passwords->verify($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('Kombinace jmena a Hesla není správná.', self::INVALID_CREDENTIAL);
		}

		return new SimpleIdentity(
			$row->id,
			$row->role, // nebo pole více rolí
			['name' => $row->username]
		);
	}
}
