<?php

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

session_start();

// We want everything to be relative to index.php
chdir(__DIR__);

require '../vendor/autoload.php';

// Build our container
$containerBuilder = new DI\ContainerBuilder;
$container = $containerBuilder
		->addDefinitions(require_once __DIR__ . '/../app/config/config_web.php')
		->useAnnotations(true)
		->build();

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
	$uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeList = require '../app/routes.php';
/** @var Dispatcher $dispatcher */
$dispatcher = FastRoute\simpleDispatcher(
		function (RouteCollector $r) use ($routeList) {
	foreach ($routeList as $routeDef) {
		$r->addRoute($routeDef[0], $routeDef[1], $routeDef[2]);
	}
}
);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
	case Dispatcher::NOT_FOUND:
		echo $container->get(Twig_Environment::class)->render('404.twig');
		break;
	case Dispatcher::METHOD_NOT_ALLOWED:
		$allowedMethods = $routeInfo[1];
		echo $container->get(Twig_Environment::class)->render('404.twig');
		break;
	case Dispatcher::FOUND:
		$controller = $routeInfo[1];
		$parameters = $routeInfo[2];

		// We found a route - dispatch
		$handler = $routeInfo[1]; // A fully namespaced classname and a method name separated by a space: '\\some\\class someFunction'
		$ctrl = (is_array($controller)) ? $container->get($controller[0]) : $container->get($controller);
		$method = (is_array($controller)) ? $controller[1] : '__invoke';
		$container->call($controller, $parameters);
		break;
}