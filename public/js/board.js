/* global pusher */
/* exported board */

var board = {
	/**
	 * The Pusher channel the board is using.
	 *
	 * @type PusherChannel
	 */
	pusherChannel: null,

	/**
	 * Initialize the board!
	 */
	init: function() {
		this.initBoardCells();
		this.initPlayerTiles();
		this.initPusherChannel();
	},

	/**
	 * Initialize the board cells and tiles.
	 */
	initBoardTiles: function() {
		$('.board-cell.droppable').droppable({
			drop: function(event, ui) {
				var toX = parseInt($(this).data('x'));
				var toY = parseInt($(this).data('y'));
				var tileId = ui.draggable.data('id');
				// @todo If move failed we might need to move it back?
				board.movePlayerTile(tileId, toX, toY);
			}
		});
		$('.tile.draggable').draggable({
			
		});
	},

	/**
	 * Initialize the player tiles.
	 */
	initPlayerTiles: function() {
		$('#player-tiles').droppable({
			drop: function(event, ui) {
				var tileId = ui.draggable.data('id');
				board.removePlayerTile(tileId);
			}
		});
	},

	/**
	 * Initialize the Pusher channels.
	 */
	initPusherChannel: function() {
		pusher.init();
		this.pusherChannel = pusher.pusher.subscribe('board');
		this.pusherChannel.bind('tileMoved', function(data) {
			board.moveTile(data.tileId, data.toX, data.toY);
		});
		this.pusherChannel.bind('tileRemoved', function(data) {
			board.removeTile(data.tileId);
		});
		this.pusherChannel.bind('gameOver', function(data) {
			board.showGameOver();
		});
	},

	/**
	 * Try to move a tile to a new cell.
	 *
	 * @param {String} tileId
	 * @param {Number} toX
	 * @param {Number} toY
	 */
	movePlayerTile: function(tileId, toX, toY) {
		$.ajax({
			url: '/move',
			data: {
				tileId: tileId,
				toX: toX,
				toY: toY
			},
			async: true,
			type: 'POST',
			success: function(response) {
				if ( ! response.success) {
					// @todo Move the tile back!
				}
			}
		});
	},

	/**
	 * Remove a player tile from the board (back to the players tiles)
	 *
	 * @param {String} tileId
	 */
	removePlayerTile: function(tileId) {
		$.ajax({
			url: '/remove',
			data: {
				tileId: tileId
			},
			async: true,
			type: 'POST',
			success: function() {
				
			}
		});
	},

	/**
	 * Move a tile on the board.
	 *
	 * @param {String} tileId
	 * @param {Number} toX
	 * @param {Number} toY
	 */
	moveTile: function(tileId, toX, toY, data) {
		var $tile = $('.tile[data-id="' + tileId + '"]').detach();
		if ($tile.length === 0) {
			// Tile does not exists, must be another player adding one - create it
			$tile = $('<div class="tile tile-' + data.type + ' draggable" data-id="' + tileId + '"></div>');
		}
		$('.board-cell[data-x="' + toX + '"][data-y="' + toY + '"]').append($tile);
	},

	/**
	 * Remove a tile from the board (except player tiles).
	 *
	 * @param {String} tileId
	 */
	removeTile: function(tileId) {
		var $tile = $('.tile[data-id="' + tileId + '"]');
		if ($tile.data('is_player_tile')) {
			return; // Do not remove the own players tiles (if he have hacked his own tiles, who cares!)
		}
		$tile.remove();
	},

	/**
	 * Show the level complete!
	 */
	showGameOver: function() {
		$('#gameover_success_modal').modal({
			backdrop: 'static',
			keyboard: false,
			show: true
		});
	}

};
