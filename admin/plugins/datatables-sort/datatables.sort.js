jQuery.extend( jQuery.fn.dataTableExt.oSort, {
	"date-pre": function ( a ) {
		
		var dateTime = a.trim().replace('<br>', ' ').split(' ');
		
		var date = dateTime[0].split('-');
		var time = dateTime[1].split(':');
		
		var fullDate = new Date(date[2], date[1], date[0], time[0], time[1], time[2]);
		
		return fullDate.getTime();
		
	},
	"duration-pre": function ( a ) {
		
		var duration = a.trim().split(':').reverse();
		
		var seconds = parseInt( duration[0].replace(/\D/g,'') );
		
		if (duration[1]) seconds = parseInt( duration[1] ) * 60; // minutes
		if (duration[2]) seconds = parseInt( duration[2] ) * 60 * 60; // hours
		
		return seconds;
		
	},
} );