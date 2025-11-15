function Native() {
	
	this.stacks = {};
	this.stackIndex = 0;
	
}

Native.prototype = {

	constructor: Native,
	
	isWebView : function() {
		
		return !!window.ReactNativeWebView;
		
	},
	
	response : function( index, params ) {
		
		var callback = this.stacks[ index ];
		delete this.stacks[ index ];
		
		console.log("NATIVE RESPONSE ->", params);
		
		if (callback) callback.apply( null, params );
		
	},
	
	alert : function( message ) {
		
		this.post({
			query: "alert",
			message: message
		} );
		
	},
	
	post : function( data, callback ) {
		
		const index = this.stackIndex++;
		const postData = Object.assign({ __index: index }, data );
		
		this.stacks[ index ] = callback;
		
		console.log("NATIVE POST ->", data);
		
		if ( this.isWebView() ) {
			
			const json = JSON.stringify( postData );
			
			window.ReactNativeWebView.postMessage( json );
			
		} else {
			
			setTimeout(function() {
				
				this.response( index, [ {error: "No WebView Detected."} ] );
				
			}.bind( this ), 1000 );
			
		}

	}
	
}

const native = new Native();

window.native = native;