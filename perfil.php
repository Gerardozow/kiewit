<?php
$title_page = 'Mi Perfil';
//Menus Sidebar
$separador = 'dashboard';
$page = 'perfil';
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}

include_once('layouts/head.php');

?>

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <?php include_once('layouts/header.php'); ?>
        <?php include_once('layouts/sidebar.php'); //sidebar 
        ?>
        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Mi Perfil</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Perfil
                                </li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <?php echo display_msg($msg); ?>
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-md-3">

                            <!-- Profile Image -->
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <div style="display: inline-block; overflow: hidden; border-radius: 0%; width: 200px; height: 200px;">
                                            <?php
                                            $imagePath = "./uploads/users/" . $user['image'];
                                            if (file_exists($imagePath)) {
                                                echo '<img class="user-image" src="' . $imagePath . '" alt="User profile picture" style="display: block; max-width: 100%; max-height: 100%; margin: 0 auto;">';
                                            } else {
                                                echo '<img class="user-image" src="./uploads/user_default.png" alt="Default profile picture" style="display: block; max-width: 100%; max-height: 100%; margin: 0 auto;">';
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <h3 class="profile-username text-center"><?= $user['name'] . " " . $user['last_name'] ?></h3>

                                    <p class="text-muted text-center"><span class="fw-bold" style="color: var(--bs-blue);">@</span><?= $user['username'] ?></p>

                                    <form action="upload.php" method="POST" enctype="multipart/form-data">
                                        <div class="input-group mb-3">
                                            <input type="file" class="form-control" id="inputGroupFile02" name="profileImage">
                                        </div>
                                        <button type="submit" class="btn btn-primary d-block w-100" name="upload_user">Subir imagen</button>
                                    </form>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link tabu active" href="#settings" data-toggle="tab">Configuracion</a></li>
                                        <li class="nav-item"><a class="nav-link tabu " href="#cambiar_password" data-toggle="tab">Cambiar Contraseña</a></li>
                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="settings">
                                            <form class="">
                                                <div class="form-group row mb-2">
                                                    <label for="inputName" class="col-sm-2 col-form-label">Usuario</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="inputName" placeholder="Username" value="<?= $user['username'] ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="inputEmail" value="<?= $user['email'] ?>" placeholder="Email" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                    <label for="inputName2" class="col-sm-2 col-form-label">Nombre</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputName2" value="<?= $user['name'] ?>" placeholder="Name" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                    <label for="inputName2" class="col-sm-2 col-form-label">Apellidos</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputName2" value="<?= $user['last_name'] ?>" placeholder="Name" disabled>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.tab-pane -->
                                        <div class="tab-pane" id="cambiar_password">
                                            <form method="post" action="./change_password.php" class="clearfix">
                                                <div class="form-group row mb-2">
                                                    <label for="newPassword" class="col-sm-2 col-form-label">Contraseña Nueva</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" class="form-control" name="new-password" placeholder="Contraseña Nueva">
                                                    </div>
                                                </div>
                                                <div class="form-group row mb-2">
                                                    <label for="oldPassword" class="col-sm-2 col-form-label">Contraseña Actual</label>
                                                    <div class="col-sm-10">
                                                        <input type="password" class="form-control" name="old-password" placeholder="Contraseña Actual">
                                                    </div>
                                                </div>
                                                <div class="form-group clearfix float-end">
                                                    <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
                                                    <button type="submit" name="update" class="btn btn-warning">Cambiar</button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- /.tab-pane -->
                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- /.row (main row) -->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <?php include_once('layouts/footer.php'); //footer 
        ?>
    </div>
    <!--end::App Wrapper-->
    <?php include_once('layouts/scripts.php'); //scripts 
    ?>

    <!-- OPTIONAL SCRIPTS -->
</body><!--end::Body-->

</html>