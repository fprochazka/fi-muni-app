<?php

namespace App\Presenters;

use App\Navigation\INavigationControlFactory;
use Kdyby\Autowired\AutowireComponentFactories;
use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	use AutowireComponentFactories;

	/**
	 * @var \WebLoader\Nette\LoaderFactory
	 * @inject
	 */
	public $webLoader;



	/**
	 * @param INavigationControlFactory $factory
	 * @return \App\Navigation\NavigationControl
	 */
	protected function createComponentNavigation(INavigationControlFactory $factory)
	{
		return $factory->create();
	}



	/**
	 * @return \WebLoader\Nette\JavaScriptLoader
	 */
	protected function createComponentJs()
	{
		return $this->webLoader->createJavaScriptLoader('default');
	}



	/**
	 * @return \WebLoader\Nette\CssLoader
	 */
	protected function createComponentCss()
	{
		return $this->webLoader->createCssLoader('default')
			->setMedia('screen,projection,tv');
	}

}
