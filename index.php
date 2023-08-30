<?php 
$title_page = "Login | Kiewit";
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false);}
  include_once('layouts/head.php');
?>

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <a href="index.php" class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
                    <h1 class="text-warning text-uppercase mb-0">
                        Kiewit
                    </h1>
                </a>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Inicia sesión</p>
                <?php echo display_msg($msg); ?>   
                <form action="./auth.php" method="post">
                    <div class="input-group mb-1">
                        <div class="form-floating">
                            <input id="loginUser" name="loginUser" type="text" class="form-control" value="" placeholder="">
                            <label for="loginUser">Usuario</label>
                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-person"></span>
                        </div>
                    </div>
                    <div class="input-group mb-1">
                        <div class="form-floating">
                            <input id="loginPassword" name="loginPassword" type="password" class="form-control" placeholder="">
                            <label for="loginPassword">Contraseña</label>
                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-8 d-inline-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Recuerdame
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Iniciar sesión</button>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->
                </form>

                <p class="mb-1">
                    <a href="./forgot-password.php">Olvidé mi contraseña</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

  <?php include_once('./layouts/scripts.php') ?>
</body><!--end::Body-->
</html>