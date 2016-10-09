/* global Pusher */
/* exported pusher */

var pusher = {
	/**
	 * An instance of the Pusher API library.
	 *
	 * @type Pusher
	 */
	pusher: null,

	/**
	 * The Pusher App key.
	 *
	 * @type String
	 */
	appKey: '88de40d153eadda15eb5',

	/**
	 * The Pusher App cluster.
	 *
	 * @type String
	 */
	appCluster: 'eu',

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
		this.pusher = new Pusher(this.appKey, {
			cluster: this.appCluser,
			encrypted: true
		});
	}
};
