<?php

// User config
$user = null;
if (isset($_SESSION['user'])) {
	$user = $_SESSION['user']; /* @var $user Standard\User */

	// Do we have a valid user?
	if (!($user instanceof Standard\User\User)) {
		session_destroy();
		unset($_SESSION['user']);

		// Redirect to the main page
		header('Location: /');
		die();
	}
}
// Shared config (may be moved into a separate file)
$shared = [];
$shared['user'] = $user;

// Return the global config array
return [
	'site-config' => [
	// Global site config goes here
	],
	// Prepare twig for DI
	Twig_Environment::class => function () use ($shared) {
		$loader = new Twig_Loader_Filesystem(realpath('../templates'));
		$twigEnvironment = new Twig_Environment($loader, array('cache' => realpath('../cache')));

		if ($shared['user']) {
			$twigEnvironment->addGlobal('alias', $shared['user']->getAlias());
		}
		return $twigEnvironment;
	},
	'User' => function () use ($shared) {
		return $shared['user'];
	},
];
