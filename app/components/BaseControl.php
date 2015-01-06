<?php

namespace App;

use Kdyby;
use Nette;



/**
 * @method onAttached(BaseControl $self)
 */
abstract class BaseControl extends Nette\Application\UI\Control
{

	/**
	 * @var array
	 */
	public $onAttached = [];

	/**
	 * @var Kdyby\Translation\Translator
	 */
	private $translator;



	/**
	 * @return Kdyby\Translation\Translator
	 */
	public function getTranslator()
	{
		if ($this->translator === NULL) {
			$this->translator = $this->presenter->getContext()->getByType(Kdyby\Translation\Translator::class);
		}

		return $this->translator;
	}



	/**
	 * @param Kdyby\Translation\Translator $translator
	 * @return BaseControl
	 */
	public function setTranslator(Kdyby\Translation\Translator $translator)
	{
		$this->translator = $translator;
		return $this;
	}



	/**
	 * @param \Nette\ComponentModel\Container $obj
	 */
	protected function attached($obj)
	{
		parent::attached($obj);

		if (!$obj instanceof \Nette\Application\UI\Presenter) {
			return;
		}

		$this->onAttached($this);
	}

}
