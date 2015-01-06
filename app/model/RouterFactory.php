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



	public function __construct($productionMode)
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

		$router[] = new Route('[!<path .*?>/][<action>]', [
			'action' => 'default',
			NULL => [
				Route::FILTER_IN => function (array $params) {
					if (strpos($path = $params['path'], '/') === FALSE) {
						return NULL;
					}

					$params[Route::PRESENTER_KEY] = self::path2presenter($path);
					unset($params['path']);

					return $params;
				},
				Route::FILTER_OUT => function (array $params) {
					if (strpos($presenter = $params[Route::PRESENTER_KEY], ':') === FALSE) {
						return NULL;
					}

					$params['path'] =  self::presenter2path($presenter);
					unset($params[Route::PRESENTER_KEY]);

					return $params;
				},
			]
		], $flags);

		$router[] = new Route('<presenter>/<action>', 'Homepage:default', $flags);

		return $router;
	}



	private static function presenter2path($s)
	{
		$s = preg_replace('~([^\\/])(?=[A-Z])~', '$1-', strtr($s, ':', '/'));
		return str_replace('%2F', '/', rawurlencode(strtolower($s)));
	}



	private static function path2presenter($s)
	{
		$s = preg_replace('#([\\/-])(?=[a-z])#', '$1 ', strtolower($s));
		return strtr(ucwords($s), ['/ ' => ':', '- ' => '']);
	}

}
