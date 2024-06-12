<?php

namespace App\Router;

use Nette\Application\Routers\RouteList;

class RouterFactory {

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
