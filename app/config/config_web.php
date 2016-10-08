<?php

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

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

$shared ['site'] = [
	'name' => getenv('APP_NAME') ?: 'Skeleton app',
	'url' => getenv('APP_URL') ?: 'http://test.app',
	'sender' => getenv('APP_SENDER') ?: 'skeleton@example.app',
	'replyto' => getenv('APP_REPLYTO') ?: 'skeleton@example.app',
	'debug' => getenv('DEBUG') === 'true',
	'env' => getenv('APPLICATION_ENV'),
	'logFolder' => __DIR__ . '/../../../logs',
	'viewsFolders' => [__DIR__ . '/../../../src/Standard/Views']
];

// Return the global config array
return [
	'site-config' => $shared['site'],
	// Prepare twig for DI
	Twig_Environment::class => function () use ($shared) {
		$loader = new Twig_Loader_Filesystem(realpath('../templates'));
		$twigEnvironment = new Twig_Environment($loader, array('cache' => realpath('../cache')));

		if ($shared['user']) {
			$twigEnvironment->addGlobal('alias', $shared['user']->getAlias());
		}
		return $twigEnvironment;
	},
	ClientInterface::class => function () {
		$client = new Client();
		return $client;
	},
	'User' => function () use ($shared) {
		return $shared['user'];
	},
];
