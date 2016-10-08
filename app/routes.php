<?php

/**
 * Poelse application routes
 * Routes can use optional segments and regular expressions. See nikic/fastroute
 */
return [
	// Basic example routes. When controller is used without method (as string),
	// it needs to have a magic __invoke method defined
		['GET', '/', 'Standard\Controller\IndexController'],
		[['GET', 'POST'], '/player/create', ['Standard\Controller\IndexController', 'createPlayerAction']],
		[['GET', 'POST'], '/move', ['Standard\Controller\BoardController', 'moveAction']],
		[['GET', 'POST'], '/remove', ['Standard\Controller\BoardController', 'removeAction']],
		[['GET'], '/board', ['Standard\Controller\BoardController', 'indexAction']],
];
