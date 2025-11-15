function Signal() {

	this.events = {};

}

Signal.prototype = {

	constructor: Signal,
	
	on: function( name, listener ) {

		if ( ! this.events[ name ] ) {

			this.events[ name ] = [];

		}

		this.events[ name ].push( listener );

	},

	off: function( name, listenerToRemove ) {

		if ( ! this.events[ name ] ) {

			console.warn( "Can't remove a listener. Event '" + name + "' doesn't exits." );
			
			return;

		}

		const filterListeners = function ( listener ) { return listener !== listenerToRemove };

		this.events[ name ] = this.events[ name ].filter( filterListeners );

	},

	has: function( name ) {

		return !!this.events[ name ];

	},

	emit: function( name, data ) {

		console.log("SIGNAL ->", name, data);

		if ( ! this.has( name ) ) {

			//console.warn( "Can't emit an event. Event '" + name + "' doesn't exits." );
			
			return;

		}
		
		const self = this;

		for(const f of this.events[ name ]) {
			
			if ( f( data ) === false ) {
				
				break;
				
			}
			
		}

	}
	
}

const signal = new Signal();

window.signal = signal;