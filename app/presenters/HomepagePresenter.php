<?php

namespace App\Presenters;

use App\Model\HomepageModel;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter
{

	private HomepageModel $model;

	function __construct(
		HomepageModel $model
	) {
		$this->model = $model;
		parent::__construct();
	}


	function renderDefault() {
		$this->getTemplate()->currentSales = $this->model->getCurrentSales();
		$this->getTemplate()->nextSale = $this->model->getNextSale();;
	}


}
