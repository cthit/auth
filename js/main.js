$('#new_password, #verify_password').on('change', function() {
	if ($('#new_password').val() !== $('#verify_password').val()) {
		$('#verify_password')[0].setCustomValidity('Lösenorden matchar inte');
	} else {
		$('#verify_password')[0].setCustomValidity('');
	}
});
$('#email, #confirm-mail').on('change', function() {
	console.log($('#email').val(), $('#confirm-mail').val());
	if ($('#email').val() !== $('#confirm-mail').val()) {
		$('#confirm-mail')[0].setCustomValidity('Mailadresserna matchar inte');
	} else {
		$('#confirm-mail')[0].setCustomValidity('');
	}
});