<div class="page-header">
  <div class="container">
    <h1>Välkommen till Chalmers IT:s Autentiseringssystem!</h1>
  </div>
</div>
<div class="row">
  <div class="col-lg-4">
    <p></p>
  </div>
  <div class="col-lg-5 col-lg-offset-3">
   <form role="form" class="form-horizontal" method="post" action="/auth/login.php">
    <div class="form-group">
      <label for="username" class="col-lg-2 control-label">CID</label>
      <div class="col-lg-10">
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
          <input id="username" autofocus name="username" class="form-control" type="text" placeholder="CID"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="password" class="col-lg-2 control-label">Lösenord</label>
      <div class="col-lg-10">
        <div class="input-group">
          <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
          <input id="password" name="password" class="form-control" type="password" placeholder="Lösenord"/>
        </div>
      </div>
    </div>
    <div class="form-group">
      <div class="col-lg-offset-2 col-lg-10">
        <button type="submit" class="btn btn-primary" name="intent" value="login">Logga in</button>
      </div>
    </div>
   </form>
  </div>
</div>