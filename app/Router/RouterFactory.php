<?php

namespace App\Router;

use Nette\Application\Routers\RouteList;

class RouterFactory {

	static function createRouter(): RouteList {
		$router = new RouteList;
		$router->addRoute('', 'Homepage:default');
		$router->addRoute('<module>/<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
