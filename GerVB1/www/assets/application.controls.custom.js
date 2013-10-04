if (typeof console == "undefined")
	var console = { log: function() {} }; 
else if (typeof console.log == "undefined")
	console.log = function() {};

//
// Custom Select for Business Unit for ING Insurance, FWD, NN
//
function initSelectCustomFwd(){
$('select.select-custom-fwd').each(function() {
	// -- useful variables
	var delimiter = ' -- ';                       // don't use backslashes, because this messes with the string split
	var val          = $(this).val();                // this value
	var input        = $(this).prev();               // the input which value is to be set
	var dataDiv      = $(this).next();               // the placeholder which holds data
	var target       = undefined;
	var targetSelect = undefined;

	// -- if the value is set, otherwise just empty
	if(input.val().length > 0) {
		var explosion = input.val().split( delimiter );
		$(this).val( $.trim(explosion[0]) );
		
		target       = $(this).find(':selected').data('target'); // the target value this select item influences
		targetSelect = dataDiv.find('select[data-value="'+target+'"]');

		if( explosion.length > 1 )
			targetSelect.val( $.trim(explosion[1]) );
		else
			targetSelect.val( $.trim(explosion[0]) );
	}

	// -- add on change listener
	$(this).on('change', function(){
		val       = $(this).val();                // this value
		target    = $(this).find(':selected').data('target'); // the target value this select item influences
		targetSelect = dataDiv.find('select[data-value="'+target+'"]');

		dataDiv.find('select').hide().off('change');
		targetSelect.show()
			.on('change', function(){
				var value = $(this).val();
				if(val==value || value.length <= 0)
					input.val(value);
				else
					input.val( val + delimiter + value );
				console.log( 'Setting "' + val + '" and "' + value + '" -> ['+ input.val() +'].');
			}).trigger('change');

		if( targetSelect.find('option').length <= 1 ) // hide if only one choice
			targetSelect.attr('disabled', true);

	}).trigger('change');
});
}