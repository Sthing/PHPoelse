<?php

namespace Standard\Controller;

use Exception;
use PDO;
use Standard\Abstracts\Controller;
use Standard\User\User;
use Twig_Environment;

class IndexController extends Controller {

	/**
	 * @var Twig_Environment
	 */
	private $twig;

	/**
	 *
	 * @var PDO
	 */
	private $dbh;

	/**
	 * 
	 * @param \Standard\Controller\Twig_Environment $twig
	 */
	public function __construct(Twig_Environment $twig, PDO $dbh) {
		$this->twig = $twig;
		$this->dbh = $dbh;
	}

	/**
	 * The default action
	 */
	public function __invoke() {
		$message = 'Hello from Home, invoked';
		echo $this->twig->render('lobby.twig', [
			'message' => $message,
		]);
	}

	/**
	 * 
	 */
	public function createPlayerAction() {
		$alias = $this->post('alias');
		if (empty($alias)) {
			throw new Exception("Alias is required");
		}
		$stmt = $this->dbh->prepare("insert into player (alias) values (:alias)");
		$stmt->bindParam('alias', $alias);
		$stmt->execute();
		
		$user = new User($this->dbh->lastInsertId(), $alias);
		$_SESSION['user'] = $user;
		echo json_encode(['id' => $user->getId()]);
	}

}
