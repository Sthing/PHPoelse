<?php

namespace Standard\Controller;

use Exception;
use PDO;
use Standard\Abstracts\Controller;
use Standard\User\User;
use Twig_Environment;

class LobbyController extends Controller {

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
	 * Lobby overview
	 */
	public function indexAction() {
		$stmtGames = $this->dbh->prepare("select * from game");
		$stmtGames->execute();
		$games = $stmtGames->fetchAll();
		echo $this->twig->render('lobby.twig' , ['games' => $games]);
	}

	/**
	 * Joins a given game. Requres gameId to be present
	 */
	public function joinGameAction() {
		// User required
		if (is_null($this->user)) {
			$this->redirect('/');
		}
		$playerId = $this->user->getId();
		$gameId = $this->get('gameId');

		$stmt = $this->dbh->prepare("select * from game where id = :gameId");
		$stmt->bindParam('gameId', $gameId);
		$game = $stmt->fetchObject("Standard\Game\Game"); /* @var $game \Standard\Game\Game */

		// Game already started
		if (!is_null($game->level_id)) {
			throw new Exception("Game already started!");
		}

		// If not; then join it
		$stmtJoin = $this->dbh->prepare("insert ignore into game_2_player (game_id, player_id) values (:gameId, :playerId)");
		$stmtJoin->bindParam('gameId', $game->id);
		$stmtJoin->bindParam('playerId', $playerId);

		// Go to the lobby
		$this->redirect('/lobby');
	}

	/**
	 * Starts a game
	 */
	public function startGameAction() {
		// User required
		if (is_null($this->user)) {
			$this->redirect('/');
		}
		$playerId = $this->user->getId();
		$gameId = $this->get('gameId');

		$stmt = $this->dbh->prepare("select * from game where id = :gameId");
		$stmt->bindParam('gameId', $gameId);
		$game = $stmt->fetchObject("Standard\Game\Game"); /* @var $game \Standard\Game\Game */

		// Game already started? If so; just redirect to it and spectate/continue
		if (!is_null($game->level_id)) {
			// Only players can start the game
			$stmtPlayers = $this->dbh->prepare("select * from game_2_player where game_id = :gameId and player_id = :playerId");
			$stmtPlayers->bindParam('gameId', $gameId);
			$stmtPlayers->bindParam('playerId', $playerId);
			$playerInGame = $stmtPlayers->fetchObject();
			if (!$playerInGame) {
				throw new Exception("You cannot start a game you are not a part of");
			}
	
			// We are in the game, and it's not started; gogogo
			$stmtStartGame = $this->dbh->prepare("update game set level_id = 1 where id = :gameId");
			$stmtStartGame->bindParam('gameId', $game->id);
			$stmtStartGame->execute();
		}

		// Go to the lobby
		$this->redirect('/board');
	}

}
