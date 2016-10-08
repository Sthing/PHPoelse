<?php

namespace Standard\Controller;

use Exception;
use PDO;
use Standard\Abstracts\Controller;
use Standard\User\User;
use Twig_Environment;
use function GuzzleHttp\json_encode;

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
	 * @var User
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
		$tileId = $this->get('tileId');
		$toX = $this->get('toX');
		$toY = $this->get('toY');
		$playerId = $this->get('playerId');
		$gameId = $this->get('gameId');

		// @todo Pre-validation
		if ($playerId != $this->user->getId()) {
			throw new Exception("That's not your tile!");
		}

		// Prepare, bind and execute
		$stmt = $this->dbh->prepare("
			update game_2_tile
				set x = :x,
					y = :y
			where
				game_id = :gameId and
				player_id = :playerId and
				tile_id = :tileId");
		$stmt->bindParam('tileId', $tileId);
		$stmt->bindParam('gameId', $gameId);
		$stmt->bindParam('playerId', $playerId);
		$stmt->bindParam('x', $toX);
		$stmt->bindParam('y', $toY);

		$msg = null;
		try {
			$res = $stmt->execute();
			$msg = 'Not your tile';
		} catch (Exception $e) {
			$msg = $e->getMessage();
		}
		if ($stmt->rowCount() == 1) {
			echo json_encode(['result' => 1]);
		} else {
			echo json_encode(['result' => 0, 'msg' => $msg]);
		}
		
		// @todo Handle if move failed!
		$pusher = $this->getPusher();
		$pusher->trigger('board', 'tileRemoved', ['tileId' => $tileId, 'toX' => $toX, 'toY' => $toY]);
	}

	public function removeAction() {
		$tileId = $this->post('tileId');
		$playerId = $this->post('playerId'); // @todo Get from session
		$gameId = $this->post('gameId');
		if ($playerId != $this->user->getId()) {
			throw new Exception("That's not your tile!");
		}
		$stmt = $this->dbh->prepare("insert into game_2_tile (game_id, player_id, tile_id, x, y) values (:gameId, :playerId, :tileId, NULL, NULL)");
		$stmt->bindParam('tileId', $tileId);
		$stmt->bindParam('gameId', $gameId);
		$stmt->bindParam('playerId', $playerId);
		$stmt->execute();
		
		$pusher = $this->getPusher();
		$pusher->trigger('board', 'tileRemoved', ['tileId' => $tileId]);
	}
	
	private function getPusher() {
		$options = array(
			'cluster' => 'eu',
			'encrypted' => true
		);
		return new \Pusher('88de40d153eadda15eb5', '36beef3b4765f673d0da', '257267', $options);
	}

}
