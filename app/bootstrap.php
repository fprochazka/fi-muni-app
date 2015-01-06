<?php

require __DIR__ . '/../vendor/autoload.php';

return call_user_func(function () {
	$configurator = new Nette\Configurator;

	//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
	$configurator->enableDebugger(__DIR__ . '/../log');
	$configurator->setTempDirectory(__DIR__ . '/../temp');

	$loader = $configurator->createRobotLoader()
		->addDirectory(__DIR__)
		->register();

	$configurator->addConfig(__DIR__ . '/config/config.neon');
	$configurator->addConfig(__DIR__ . '/config/config.local.neon');

	$container = $configurator->createContainer();
	$container->addService('robotLoader', $loader);

	return $container;
});
