<?php

namespace App\Navigation;

use App\BaseControl;
use Kdyby;
use Latte\Runtime\Filters;
use Nette;



class NavigationControl extends BaseControl
{

	/**
	 * @var array
	 */
	private $structure = [];

	/**
	 * @var Nette\Loaders\RobotLoader
	 */
	private $loader;

	/**
	 * @var Nette\Caching\Cache
	 */
	private $cache;

	/**
	 * @var Nette\Application\PresenterFactory
	 */
	private $presenterFactory;



	public function __construct(Nette\Loaders\RobotLoader $loader, Nette\Application\PresenterFactory $presenterFactory, Nette\Caching\IStorage $cache)
	{
		parent::__construct();

		$this->loader = $loader;
		$this->cache = new Nette\Caching\Cache($cache, get_class($this));
		$this->presenterFactory = $presenterFactory;

		$this->onAttached[] = function () {
			$this->structure = $this->cache->load('presenters', function (&$dp) {
				return $this->buildPresenterTree();
			});
		};
	}



	public function render()
	{
		$this->template->branch = $this->structure;
		$this->template->render(__DIR__ . '/tree.latte');
	}



	public function renderBreadcrumbs()
	{
		$this->template->crumbs = $this->buildBreadcrumbs();
		$this->template->render(__DIR__ . '/breadcrumbs.latte');
	}



	public function renderHeadTitle()
	{
		$list = [];
		foreach ($this->buildBreadcrumbs() as $title) {
			$list[] = $this->getTranslator()->translate('front.navigation.' . $title . '.title');
		}

		array_shift($list); // homepage
		echo Filters::escapeHtml(implode(' | ', array_reverse($list)));
	}



	public function renderTitle()
	{
		echo Filters::escapeHtml($this->getTranslator()->translate('front.navigation.' . implode('.', $this->getPath()) . '.title'));
	}



	protected function buildBreadcrumbs()
	{
		$crumbs = ['Homepage' => 'Homepage'];
		array_reduce($this->getPath(), function ($list, $item) use (&$crumbs) {
			$list[] = $item;
			$crumbs[implode(':', $list)] = implode('.', $list);

			return $list;
		}, []);

		return $crumbs;
	}



	/**
	 * @return array
	 */
	protected function getPath()
	{
		return explode(':', trim($this->getPresenter()->getName(), ':'));
	}



	private function buildPresenterTree()
	{
		$tree = [];

		$this->loader->rebuild();
		foreach ($this->loader->getIndexedClasses() as $class => $file) {
			try {
				$refl = new \ReflectionClass($class);
			} catch (\ReflectionException $e) {
				continue;
			}

			if (!$refl->implementsInterface(Nette\Application\IPresenter::class) || !$refl->isInstantiable()) {
				continue;
			}

			$name = $this->presenterFactory->unformatPresenterClass($refl->getName());
			$path = explode(':', trim($name, ':'));

			if (count($path) <= 1) {
				continue; // ignore root module
			}

			unset($ref);
			$ref =& Nette\Utils\Arrays::getRef($tree, $path);
			$ref = ':' . $name . ':';
		}

		$sort = function ($arr) use (&$sort) {
			if (!is_array($arr)) {
				return $arr;
			}

			asort($arr);
			foreach ($arr as $k => $v) {
				$arr[$k] = $sort($v);
			}

			return $arr;
		};

		return $sort($tree);
	}

}



interface INavigationControlFactory
{

	/** @return NavigationControl */
	function create();
}
