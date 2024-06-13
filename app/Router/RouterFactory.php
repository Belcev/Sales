<?php

namespace App\Router;

use Nette\Application\Routers\RouteList;

class RouterFactory {

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
//		$router->addRoute('login/login', 'login:login');
//		$router->addRoute('login/logout', 'login:logout');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
