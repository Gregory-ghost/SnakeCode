
$(document).ready(async function () {
		const result = await $.get('api', {method: 'changeDirection', id: 12, direction: 'left' });
		
		console.log(result);
});


var request = $.ajax({
  url: "api",
  method: "GET",
  data: { method: 'changeDirection', id: 12, direction: 'left' },
  dataType: "json"
});
 
request.done(function( msg ) {
  $( "#log" ).html( msg );
});
 
request.fail(function( jqXHR, textStatus ) {
  alert( "Request failed: " + textStatus );
});

$.get('api', {method: 'changeDirection', id: 12, direction: 'left' });