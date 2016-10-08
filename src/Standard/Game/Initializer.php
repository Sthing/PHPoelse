<?php
namespace Standard\Game;

/**
 * @author thing
 */
class Initializer {

	/**
	 * @var PDO
	 */
	private $dbh;

	public function __construct(PDO $dbh) {
		$this->dbh = $dbh;
	}

	/**
	 * Starts the game.
	 *
	 * @param int $gameId
	 * @param int $levelId
	 * @return boolean
	 */
	public function invoke(int $gameId, int $levelId) : bool  {
		// Find game
		$sql = "SELECT		*
				FROM		game
				WHERE		id = :gameId";
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindParam('gameId', $gameId);
		$stmt->execute();
		$game = $stmt->fetchObject('Standard\Game\Game');
		if ( ! $game) {
			throw new Exception("Unknown game #$gameId");
		}
		if ( ! is_null($game->level_id)) {
			throw new Exception("Game #$gameId already started.");
		}

		// Find IDs of players in the game
		$playerIdStmt = $this->dbh->prepare("SELECT player_id FROM game_2_player WHERE game_id = :gameId");
		$playerIdStmt->bindParam('gameId', $gameId);
		$playerIdStmt->execute();
		$playerIds = $playerIdStmt->fetchColumn();

		// Load tiles for level
		$loadTilesStmt = $this->dbh->prepare("SELECT * FROM tile WHERE level_id = :levelId");
		$loadTilesStmt->bindParam('levelId', $levelId);
		$loadTilesStmt->execute();
		$loadTilesStmt->setFetchMode(PDO::FETCH_CLASS, 'Standard\Game\Tile');
		while ($tile = $stmt->fetch()) {
			// @todo Insert into game_2_tile, split across players, x and y as null
		}
		var_dump($playerIds);

		// Update game with level_id and start_time
		// @todo

		return true;
	}

}
