<div class="page-header">
  <div class="container">
    <h1>Välkommen till IT:s Autentiseringssystem!</h1>
  </div>
</div>
<div class="row">
  <div class="col-lg-4">
    <p></p>
  </div>
  <?php if (!is_signed_in()): ?>
    <div class="col-lg-5 col-lg-offset-3">
     <form role="form" class="form-horizontal" method="post" action="/auth/login.php">
      <?php
	form_control("username", "CID", "input", "user", true);
	form_control("password", "Lösenord", "password", "lock");
      ?>
      <div class="form-group">
	<div class="col-lg-offset-2 col-lg-10">
	  <button type="submit" class="btn btn-primary" name="intent" value="login">Logga in</button>
	</div>
      </div>
     </form>
    </div>
  <?php endif; ?>
</div>