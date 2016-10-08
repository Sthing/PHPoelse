<?php

namespace Standard\Controller;

use Standard\Abstracts\Controller;
use Twig_Environment;

class IndexController extends Controller {

	/**
	 * @var Twig_Environment
	 */
	private $twig;

	/**
	 * 
	 * @param \Standard\Controller\Twig_Environment $twig
	 */
	public function __construct(Twig_Environment $twig) {
		$this->twig = $twig;
	}

	/**
	 * The default action
	 */
	public function __invoke() {
		$message = 'Hello from Home, invoked';
		echo $this->twig->render('index.twig', [
			'message' => $message,
		]);
	}

}
