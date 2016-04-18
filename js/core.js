function errorbox(message){
	$('body').append("<div class='errorbox'>"+message+"</div>");
	setTimeout(function() {
	  $('.errorbox').remove();
	}, 2000);
}

function validate(text){
	
	if(text.length>140)
		return false;
	
	var patt = new RegExp(/\'|\<|\>|\(|\)|\%|\*|(\!\=)/);
	return !patt.test(text);
}