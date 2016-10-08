<?php

namespace Standard\Abstracts;

/**
 * The abstract contoller providing implementations with twig and a request object
 */
abstract class Controller {

	/**
	 * @Inject("site-config")
	 * @var array
	 */
	protected $site;

	/**
	 * Redirects the app to a given URL, absolute or relative, remote or local.
	 *
	 * @param string $url
	 * @return void
	 */
	protected function redirect(string $url) {
		header('Location: ' . $url);
		die();
	}
}
