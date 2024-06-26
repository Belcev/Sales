<?php

namespace App\Entity;

use Nette\Database\Row;

class Sale {

	private int $id;
	private string $name;
	private \DateTime $createdAt;
	private int $createdById;
	private \DateTime $updatedAt;
	private int $updatedById;
	private \DateTime $activeFrom;
	private \DateTime $activeTo;
	private string $color;
	private array $tags = [];

	function __construct( Row $data ) {
		$this->id = $data['id'];
		$this->name = $data['name'];
		$this->createdAt = $data['created_at'];
		$this->createdById = $data['created_by_id'];
		$this->updatedAt = $data['updated_at'];
		$this->updatedById = $data['updated_by_id'];
		$this->activeFrom = $data['active_from'];
		$this->activeTo = $data['active_to'];
		$this->color = $data['color'];
		$this->tags = explode(',', $data['tags']);
	}

	function getId(): int {
		return $this->id;
	}

	function getName(): string {
		return $this->name;
	}

	function getCreatedAt(): \DateTime {
		return $this->createdAt;
	}

	function getCreatedById(): int {
		return $this->createdById;
	}


	function getUpdatedAt(): \DateTime {
		return $this->updatedAt;
	}


	function getUpdatedById(): int {
		return $this->updatedById;
	}


	function getActiveFrom(): \DateTime {
		return $this->activeFrom;
	}


	function getActiveTo(): \DateTime {
		return $this->activeTo;
	}


	function getColor(): string {
		return $this->color;
	}


	function getTags(): array {
		return $this->tags;
	}




	function isActive(): bool {
		return $this->getActiveFrom() < new \Nette\Utils\DateTime() && $this->getActiveTo() > new \Nette\Utils\DateTime();

	}


}
