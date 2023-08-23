<?php
$title_page = "Recuperar Contraseña | MAUGER";
ob_start();
require_once('includes/load.php');
if ($session->isUserLoggedIn(true)) {
  redirect('home.php', false);
}
include_once('layouts/head.php');
?>

<body class="login-page bg-body-secondary">
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="./index.php" class="h1">MAUGER</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">¿Ha olvidado su contraseña?.</p>
        <form action="recover-password.php" method="post">
          <div class="input-group mb-1">
            <div class="form-floating">
              <input id="forgotPassword" name="forgotPassword" type="email" class="form-control" placeholder="">
              <label for="forgotPassword">Email</label>
            </div>
            <div class="input-group-text">
              <span class="bi bi-envelope"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary w-100" name="reset_password">Solicitar nueva contraseña</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <p class="mt-3 mb-1">
          <a href="./index.php">Iniciar Sesion</a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
  <?php include_once('./layouts/scripts.php') ?>
</body><!--end::Body-->

</html>