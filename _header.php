<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="/auth/">Chalmers IT Autentisering</a>
    </div>
    <div class="navbar-collapse collapse">
      <ul class="nav navbar-nav">
        <li><a href="/auth/">Hem</a></li>
        <li><a href="#profile">Profil</a></li>
        <li><a href="userInfo.php">UserInfo</a></li>
      </ul>
      <div class="navbar-form navbar-right">
        <?php if (is_signed_in()):
          if (is_admin()): ?>
        <a href="#admin" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-wrench"></span> Admin</a>
          <?php endif; ?>
          
        <a href="logout.php" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span> Logga ut</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>