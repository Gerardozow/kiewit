<?php
$title_page = "Restablecer Contraseña | Kiewit";
require_once('includes/load.php');
include_once('layouts/head.php');
$token = ''
?>


<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Acción para el método GET (por ejemplo, mostrar información)
    if (isset($_GET["token"])) {
        $token = $_GET["token"];
        //Validar la existencia del token
        $a = find_token($token);
        if ($a == null) {
            $session->msg("d", "El link propocionado es incorrecto favor de verificarlo.");
            redirect('./index.php', false);
        } else {
            if (strtotime($a['expiration']) > time()) {
            } else {
                $session->msg("w", "La solicitud de restablicimiento de contraseña a expirado, por favor haz una nueva solicitud.");
                redirect('./index.php', false);
            }
        }
    } else {
        redirect('index.php', false);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['resetPassword'])) {

        $token = $_POST['token'];

        $id = find_token($token);

        $req_fields = array('newPassword', 'confirmPassword');
        validate_fields($req_fields);

        if ($_POST['newPassword'] != $_POST['confirmPassword']) {
            $session->msg("d", "No coinciden las contraseñas");
            redirect('./reset_password.php?token=' . $token, false);
        }
        
        $password = remove_junk($db->escape($_POST['newPassword']));

        if (empty($errors)) {

            change_password_with_token($id['user_id'], $password);

            $session->msg("s", "Se ha actualizado la contraseña, vuelve a iniciar sesion");
            redirect('./index.php',false);
        } else {
            redirect('./reset_password.php?token=' . $token, false);
        }
        
    }
}
?>



<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <a href="index.php" class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
                    <h1 class="mb-0">
                        MAUGER
                    </h1>
                </a>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Restablecer Contraseña</p>
                <?php echo display_msg($msg); ?>
                <form action="./reset_password.php" method="post">
                    <div class="input-group mb-1">
                        <div class="form-floating">
                            <input id="newPassword" name="newPassword" type="password" class="form-control" placeholder="">
                            <label for="newPassword">Nueva Contraseña</label>
                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    <div class="input-group mb-1">
                        <div class="form-floating">
                            <input id="confirmPassword" name="confirmPassword" type="password" class="form-control" placeholder="">
                            <label for="confirmPassword">Confirmar Contraseña</label>
                        </div>
                        <div class="input-group-text">
                            <span class="bi bi-lock-fill"></span>
                        </div>
                    </div>
                    <input id="token" name="token" type="text" class="form-control" hidden value="<?= $token ?>">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" name="resetPassword">Iniciar sesión</button>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <?php include_once('./layouts/scripts.php') ?>
</body><!--end::Body-->

</html>