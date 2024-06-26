<?php

namespace App\Model;

use Nette\Database\Explorer;

class TagModel {

	private Explorer $database;

	function __construct(
		Explorer $database
	){
		$this->database = $database;
	}

	function getTagsBySaleId(int $saleId) : array {
		$tags = [];
		foreach ($this->database->table('sale_tag')->where('sale_id', $saleId) as $sale_tag) {
			$tags[] = $this->database->table('tag')->get($sale_tag->tag_id)->name;
		}
		return $tags;
	}

	function getTagsIdBySaleId(int $saleId) : array {
		return $this->database->table('sale_tag')->where('sale_id', $saleId)->fetchPairs('tag_id', 'tag_id');
	}

	function getPossibleTags() : array {
		return $this->database->table('tag')->fetchPairs('id', 'name');
	}


}