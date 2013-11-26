$('#new_password, #verify_password').on('change', function() {
	if ($('#new_password').val() !== $('#verify_password').val()) {
		$('#verify_password')[0].setCustomValidity('Lösenorden matchar inte');
	} else {
		$('#verify_password')[0].setCustomValidity('');
	}
});