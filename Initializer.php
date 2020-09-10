<?php

define('ROOT', str_replace('vendor/nomess/cli', '', __DIR__));
define('NOMESS_CONTEXT', 'DEV');

require ROOT . 'vendor/autoload.php';
$container = \Nomess\Container\Container::getInstance();

($container->get(\Nomess\Component\Cli\CliHandler::class))->listen();

