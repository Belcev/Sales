<?php

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Utils\ArrayHash;

class UserModel {

	private Explorer $database;

	function __construct(
		Explorer $database
	){
		$this->database = $database;
	}


	function doEdit(int $id, ArrayHash $values) {
		$this->database->table('user')
			->where('id', $id)
			->update(
				[
					'id' => $id,
					'username' => $values['username'],
					'role' => $values['role'],
					'first_name' => $values['first_name'],
					'last_name' => $values['last_name'],
				]
			);
	}
	function doInsert(ArrayHash $values) :void {
		$this->database->table('user')->insert(
			[
				'username' => $values['username'],
				'role' => $values['role'],
				'first_name' => $values['first_name'],
				'last_name' => $values['last_name'],
				'active' => 0,
			]
		);
	}

	function getActiveUserByUsername(string $username) : ?ActiveRow {
		return $this->database
			->table('user')
			->where('username ', $username)
			->where('active', 1)
			->fetch();
	}

	function getTable() : Selection {
		return $this->database->table('user');
	}

	function canUserSeeUsers(int $userId) : bool {
		$user = $this->database->table('user')->get($userId);
		return $user->role === 'admin';
	}



	function doActivateUser(int $id, bool $active) {
		$this->database
			->table('user')
			->where('id', $id)
			->update(['active' => $active]);
	}


	function doDeleteUser(int $id) {
		$this->database->table('user')->where('id', $id)->delete();
	}


	function getUserName(int $userId) : string {
		return $this->database->table('user')->get($userId)->name;
	}
}