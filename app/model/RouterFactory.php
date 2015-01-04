<?php

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route,
	Nette\Application\Routers\SimpleRouter;


/**
 * Router factory.
 */
class RouterFactory
{

	/**
	 * @var bool
	 */
	private $productionMode;



	public function __construct($productionMode, Nette\Http\Request $httpRequest)
	{
		$this->productionMode = (bool) $productionMode;
	}



	/**
	 * @return Nette\Application\Routers\RouteList
	 */
	public function create()
	{
		$router = new RouteList();
		$flags = $this->productionMode ? Route::SECURED : 0;

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default', $flags);

		return $router;
	}

}
