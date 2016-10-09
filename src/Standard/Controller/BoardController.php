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
	 * The default action
	 */
	public function indexAction() {
		$gameId = 1; // @todo Get from session
		$stmt = $this->dbh->prepare("
				SELECT	game_2_tile.tile_id,
						game_2_tile.player_id,
						tile.type,
						tile.x,
						tile.y
				FROM	game_2_tile
				JOIN	tile ON (
						tile.id = game_2_tile.tile_id
					AND	tile.x IS NOT NULL
					AND	tile.y IS NOT NULL
				)
				WHERE	game_2_tile.game_id = :gameId
			");
		$stmt->bindParam('gameId', $gameId);
		$stmt->execute();
		$stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
		$tiles = [];
		while ($tile = $stmt->fetch()) {
			$tiles[$tile->x][$tile->y] = $tile;
		}
		
		$stmtPlayerTiles = $this->dbh->prepare("
				SELECT	game_2_tile.tile_id,
						game_2_tile.player_id,
						tile.type,
						tile.x,
						tile.y
				FROM	game_2_tile
				JOIN	tile ON (
						tile.id = game_2_tile.tile_id
					AND	tile.x IS NULL
					AND	tile.y IS NULL
				)
				WHERE	game_2_tile.game_id = :gameId
					AND	game_2_tile.player_id = :playerId
			");
		$playerId = $this->user->getId();
		$stmtPlayerTiles->bindParam('gameId', $gameId);
		$stmtPlayerTiles->bindParam('playerId', $playerId);
		$stmtPlayerTiles->execute();
		$stmtPlayerTiles->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
		$playerTiles = [];
		while ($playerTile = $stmt->fetch()) {
			$playerTiles[] = $playerTile;
		}
		
		echo $this->twig->render('board.twig', [
			'gameId' => $gameId,
			'tiles' => $tiles,
			'playerTiles' => $playerTiles,
		]);
	}
	
	/**
	 * Starts a new game
	 */
	public function startGameAction() {
		
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
			
			
			$pusher = $this->getPusher();
			$pusher->trigger('board', 'tileRemoved', ['tileId' => $tileId, 'toX' => $toX, 'toY' => $toY]);
			echo json_encode(['result' => 1]);
		} else {
			echo json_encode(['result' => 0, 'msg' => $msg]);
		}
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
