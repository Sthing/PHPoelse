<?php

namespace Standard\Controller;

use DI\Container;
use Exception;
use PDO;
use Standard\Abstracts\Controller;
use Standard\Game\Game;
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
	 * Lobby overview
	 */
	public function indexAction() {
		$stmtGames = $this->dbh->prepare("select * from game");
		$stmtGames->execute();
		$games = $stmtGames->fetchAll();
		
		$playerId = $this->user->getId();
		$playerGamesStmt = $this->dbh->prepare("select * from game_2_player where player_id = :playerId");
		$playerGamesStmt->bindParam('playerId', $playerId);
		$playerGamesStmt->execute();
		$playerGames = $playerGamesStmt->fetchAll();
		$playerGamesList = [];
		foreach($playerGames as $playerGame) {
			$playerGamesList[$playerGame['game_id']] = true;
		}
		echo $this->twig->render('lobby.twig' , ['games' => $games, 'playerGames' => $playerGamesList]);
	}

	/**
	 * Joins a given game. Requres gameId to be present
	 */
	public function joinGameAction($gameId) {
		// User required
		if (is_null($this->user)) {
			$this->redirect('/');
		}
		$playerId = $this->user->getId();
		
		$stmt = $this->dbh->prepare("select * from game where id = :gameId");
		$stmt->bindParam('gameId', $gameId);
		$stmt->execute();
		$game = $stmt->fetchObject("Standard\Game\Game"); /* @var $game Game */

		// Game already started
		if (!is_null($game->level_id)) {
			throw new Exception("Game already started!");
		}

		// If not; then join it
		$stmtJoin = $this->dbh->prepare("insert ignore into game_2_player (game_id, player_id) values (:gameId, :playerId)");
		$stmtJoin->bindParam('gameId', $game->id);
		$stmtJoin->bindParam('playerId', $playerId);
		$stmtJoin->execute();

		// Go to the lobby
		$this->redirect('/lobby');
	}

	/**
	 * Starts a game
	 */
	public function startGameAction($gameId) {
		// User required
		if (is_null($this->user)) {
			$this->redirect('/');
		}
		$playerId = $this->user->getId();
		
		$stmt = $this->dbh->prepare("select * from game where id = :gameId");
		$stmt->bindParam('gameId', $gameId);
		$stmt->execute();
		$game = $stmt->fetchObject("Standard\Game\Game"); /* @var $game Game */

		// Game already started? If so; just redirect to it and spectate/continue
		if (!is_null($game->level_id)) {
			// Only players can start the game
			$stmtPlayers = $this->dbh->prepare("select * from game_2_player where game_id = :gameId and player_id = :playerId");
			$stmtPlayers->bindParam('gameId', $gameId);
			$stmtPlayers->bindParam('playerId', $playerId);
			$stmtPlayers->execute();
			$playerInGame = $stmtPlayers->fetchObject();
			if (!$playerInGame) {
				throw new Exception("You cannot start a game you are not a part of");
			}
			
			// Lay down the game board
			$this->container->call('Standard\Game\Initializer', $gameId);
	
			// We are in the game, and it's not started; gogogo
			$stmtStartGame = $this->dbh->prepare("update game set level_id = 1 where id = :gameId");
			$stmtStartGame->bindParam('gameId', $game->id);
			$stmtStartGame->execute();
		}

		// Go to the lobby
		$this->redirect('/board');
	}

}
