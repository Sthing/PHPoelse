/* global Pusher */
/* exported pusher */

var pusher = {
	pusher: null,

	init: function() {
		if ( ! this.pusher) {
			this.startPusher();
		}
	},

	startPusher: function() {
		this.pusher = new Pusher('88de40d153eadda15eb5', {
			cluster: 'eu',
			encrypted: true
		});
	}
};
