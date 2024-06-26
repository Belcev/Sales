<?php

namespace App\Presenters;

use App\Model\SaleModel;
use Nette\Application\UI\Presenter;

class HomepagePresenter extends Presenter {

	/** SaleModel @inject */
	public SaleModel $saleModel;


	function renderDefault() {
		$this->getTemplate()->currentSales = $this->saleModel->getCurrentSales();
		$this->getTemplate()->nextSale = $this->saleModel->getNextSale();
	}


}
