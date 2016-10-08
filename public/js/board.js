var board = {
	init: function() {
		$('.board-cell.droppable').droppable({
			drop: function(event, ui) {
				var toX = parseInt($(this).data('x'));
				var toY = parseInt($(this).data('y'));
				var tileId = ui.draggable.data('id');
				// @todo If move failed we might need to move it back?
				board.moveTile(tileId, toX, toY);
			}
		});
		$('#player-tiles').droppable({
			drop: function(event, ui) {
				var tileId = ui.draggable.data('id');
				board.removeTile(tileId);
			}
		});
		$('.tile.draggable').draggable({
			
		});
	},

	/**
	 * Try to move a tile to a new cell.
	 *
	 * @param {String} tile
	 * @param {Number} toX
	 * @param {Number} toY
	 */
	moveTile: function(tile, toX, toY) {
		// @todo Make server request!
	},

	/**
	 * Remove a tile from the board (back to the players tiles)
	 *
	 * @param {String} tile
	 */
	removeTile: function(tile) {
		
	}
};
