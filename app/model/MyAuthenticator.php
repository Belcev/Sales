<?php

namespace App\Security;

use App\Model\UserModel;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

class MyAuthenticator implements Authenticator {
	private UserModel $userModel;
	private Passwords $passwords;


	function __construct(
		UserModel $userModel,
		Passwords $passwords

	) {
		$this->userModel = $userModel;
		$this->passwords = $passwords;
	}

	function authenticate(string $username, string $password): SimpleIdentity {
		$user = $this->userModel->getActiveUserByUsername($username);
		if (!$user || !$this->passwords->verify($password, $user->password)) {
			throw new AuthenticationException('Kombinace jmena a Hesla není správná nebo není uživatel Aktivován', self::INVALID_CREDENTIAL);
		}

		return new SimpleIdentity(
			$user->id,
			$user->role, // nebo pole více rolí
			['name' => $user->username]
		);
	}
}
