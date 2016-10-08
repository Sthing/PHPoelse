<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Standard\Game;

/**
 * Description of GameValidator
 *
 * @author thing
 */
class GameValidator {

	/**
	 * @var PDO
	 */
	private $dbh;

	public function __construct(PDO $dbh) {
		$this->dbh = $dbh;
	}

	/**
	 * Checks whether the tiles placed for game $gameID forms a valid route from the elephant to the wurst.
	 *
	 * @param int $gameId
	 * @return boolean
	 * @throws Exception
	 */
	public function isDone(int $gameId): bool {
		$maxX = $maxY = 7;

		class tileLocation {
			public $x;
			public $y;
			public $type;
		}

		try {
			$tileLocations = [];
			$x = $y = null;
			$sql = "SELECT		tile.`x`,
								tile.`y`,
								tile.`type`
					FROM		game_2_tile
					JOIN		tile ON tile.id = game_2_tile.tile_id
					WHERE		game_2_tile.game_id = :gameId";
			$stmt = $dbh->prepare($sql);
			$stmt->bindParam(':gameId', $gameId);
			foreach ($dbh->query($sql, PDO::FETCH_CLASS, 'tileLocation') AS $tileLocation) {
				$tileLocations[$tileLocation->x][$tileLocation->y] = $tileLocation->type;
				if ($tileLocation->type == 'elephant') {
					$x = $tileLocation->x;
					$y = $tileLocation->y;
				}
			}

			if (is_null($x)) {
				throw new Exception("No 'elephant' found :-(");
			}

			$allowedMoves = [ // direction, type => new direction
				'N' => [
					'NS' => 'N',
					'ES' => 'E',
					'SW' => 'W',
				],
				'E' => [
					'WE' => 'E',
					'WN' => 'N',
					'SW' => 'S',
				],
				'S' => [
					'NS' => 'S',
					'NE' => 'E',
					'WN' => 'W',
				],
				'W' => [
					'WE' => 'W',
					'NE' => 'N',
					'ES' => 'S',
				],
			];
			$direction = null;
			while ($tileLocations[$x][$y] !== 'wurst') {
				$type = $tileLocations[$x][$y];
				// find new direction
				if (is_null($direction)) {
					if ($type == 'elephant') {
						// First move
						$direction = 'E';
					}
				}
				else {
					if ( ! isset($allowedMoves[$direction][$type])) {
						throw new Exception("Tile $type not allowed when moving $direction");
					}
					$direction = $allowedMoves[$direction][$type];
				}
				// Move
				switch ($direction) {
					case 'N':
						if ($y >= $maxY) {
							throw new Exception("Northern border reached.");
						}
						$y++;
						break;

					case 'E':
						if ($x >= $maxX) {
							throw new Exception("Eastern border reached.");
						}
						$x++;
						break;

					case 'S':
						if ($y <= 0) {
							throw new Exception("Southern border reached.");
						}
						$y--;
						break;

					case 'W':
						if ($x <= 0) {
							throw new Exception("Western border reached.");
						}
						$x--;
						break;

					default:
						throw new Exception("Unknown direction '$direction'");
				}
				//echo "$direction ($x,$y)\n";
			}
			return true;
		}
		catch (Exception $e) {
			trigger_error($e->getMessage());
		}
		return false;
	}

}
