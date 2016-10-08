/* global Pusher */
/* exported pusher */

var pusher = {
	pusher: null,

	/**
	 * Initialize Pushser
	 */
	init: function() {
		if ( ! this.pusher) {
			this.startPusher();
		}
	},

	/**
	 * Start an instance of Pusher.
	 * Stores it in the pusher variable.
	 */
	startPusher: function() {
		this.pusher = new Pusher('88de40d153eadda15eb5', {
			cluster: 'eu',
			encrypted: true
		});
	}
};
