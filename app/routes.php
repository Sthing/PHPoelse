<?php

/**
 * Poelse application routes
 * Routes can use optional segments and regular expressions. See nikic/fastroute
 */
return [
	// Basic example routes. When controller is used without method (as string),
	// it needs to have a magic __invoke method defined
		['GET', '/', 'Standard\Controllers\IndexController']
];
