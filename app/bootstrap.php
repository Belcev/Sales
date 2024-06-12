<?php

declare(strict_types=1);


umask(0);
require __DIR__ . '/../vendor/autoload.php';

$configurator = new \Nette\Bootstrap\Configurator();
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->setDebugMode(true);

$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->addConfig(__DIR__ . '/../config/common.neon');
$configurator->addConfig(__DIR__ . '/../config/services.neon');
$configurator->addConfig(__DIR__ . '/../config/local.neon');

return $configurator->createContainer();