$('#ldap-create-user-form').on('beforesubmit', function() {
	var $this = $(this);
	return $this.find('new_password').val() === $this.find('verify_password').val();
});
