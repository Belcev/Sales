<?php

namespace App\Entity;

class Sale
{
	public int $id;
	public string $name;
	public \DateTime $createdAt;
	public int $createdById;
	public \DateTime $updatedAt;
	public int $updatedById;
	public \DateTime $activeFrom;
	public \DateTime $activeTo;
	public string $color;

	public array $tags = [];

	public function __construct(
		int $id,
		string $name,
		\DateTime $createdAt,
		int $createdById,
		\DateTime $updatedAt,
		int $updatedById,
		\DateTime $activeFrom,
		\DateTime $activeTo,
		string $color
	) {
		$this->id = $id;
		$this->name = $name;
		$this->createdAt = $createdAt;
		$this->createdById = $createdById;
		$this->updatedAt = $updatedAt;
		$this->updatedById = $updatedById;
		$this->activeFrom = $activeFrom;
		$this->activeTo = $activeTo;
		$this->color = $color;
	}

	public function setTags(array $tags) {
		$this->tags = $tags;
	}
}
