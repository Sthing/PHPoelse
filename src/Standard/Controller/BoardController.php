<?php

namespace Standard\Controller;

use PDO;
use Standard\Abstracts\Controller;
use Twig_Environment;

class BoardController extends Controller {

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
	 * @var \Standard\User\User
	 * @Inject("User")
	 */
	private $user;

	/**
	 * 
	 * @param \Standard\Controller\Twig_Environment $twig
	 */
	public function __construct(Twig_Environment $twig, PDO $dbh) {
		$this->twig = $twig;
		$this->dbh = $dbh;
	}
	
	/**
	 * 
	 */
	public function createGameSessionAction() {
		
	}

	/**
	 * Moves a tile on a given game
	 * @todo Some authentication may be good here
	 */
	public function moveAction() {
		// Extract
		$tileId = $this->post('tileId');
		$toX = $this->post('toX');
		$toY = $this->post('toY');
		$playerId = $this->post('playerId');
		$gameId = $this->post('gameId');
		
		// @todo Pre-validation
		if ($playerId != $this->user->getId()) {
			throw new Exception("That's not your tile!");
		}
		
		// Prepare, bind and execute
		$stmt = $this->dbh->prepare("insert into game_2_tile (game_id, player_id, tile_id, x, y) values (:gameId, :playerId, :tileId, :x, :y)");
		$stmt->bindParam('tileId', $tileId);
		$stmt->bindParam('gameId', $gameId);
		$stmt->bindParam('playerId', $playerId);
		$stmt->bindParam('x', $toX);
		$stmt->bindParam('y', $toY);
		$stmt->execute();
	}

}
