<?php

namespace Standard\Controller;

use DI\Container;
use Exception;
use PDO;
use Standard\Abstracts\Controller;
use Standard\Game\GameValidator;
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
	 * @var Container 
	 */
	private $container;

	/**
	 * 
	 * @param \Standard\Controller\Twig_Environment $twig
	 */
	public function __construct(Twig_Environment $twig, PDO $dbh, Container $container) {
		$this->twig = $twig;
		$this->dbh = $dbh;
		$this->container = $container;
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
		
		$this->container->call(GameValidator::class, [$gameId]);
		if ($stmt->rowCount() == 1) {
			$result = ['result' => 1];
			
			// Check done
			
			
		} else {
			echo json_encode(['result' => 0, 'msg' => $msg]);
		}
	}

}
