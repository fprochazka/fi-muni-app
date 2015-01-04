<?php

namespace App\Presenters;

use Nette,
	App\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * @var \WebLoader\Nette\LoaderFactory
	 * @inject
	 */
	public $webLoader;



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
