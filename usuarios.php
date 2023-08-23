<?php
$title_page = 'Panel de Usuarios | Kiewit';
//Menus Sidebar
$separador = 'administracion';
$page = 'usuarios';

require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}
page_require_level(1);
$groups = find_all('user_groups');
$usuarios = find_all('users');


$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$limit = 10;
$offset = ($pagina - 1) * $limit;

$totalUsers = count_by_id('users');
$totalPages = ceil($totalUsers['total'] / $limit);

$users = find_users_with_pagination($limit, $offset); // Agrega una función para obtener usuarios paginados




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
                            <h3 class="mb-0">Panel de Accesos</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Panel de Accesos
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
                    <div class="row mb-2">
                    <?php echo display_msg($msg); ?>
                        <div class="col-md-12">
                            <!--begin::Form Validation-->
                            <div class="card card-info card-outline">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <div class="card-title">Registro Nuevos Usuarios</div>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-maximize">
                                            <i data-lte-icon="maximize" class="bi bi-fullscreen"></i>
                                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Form-->
                                <form class="needs-validation" action="./adduser.php" method="POST" novalidate>
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <!--begin::Row-->
                                        <div class="row g-3">
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="username" class="form-label">Usuario</label>
                                                <div class="input-group has-validation">
                                                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                                                    <input type="text" class="form-control" id="username" aria-describedby="inputGroupPrepend" name="username" required>
                                                    <div class="invalid-feedback">
                                                        Por favor ingresa un usuario valido.
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Nombre</label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                                <div class="valid-feedback">¡Se ve bien!.</div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="lastname" class="form-label">Apellido</label>
                                                <input type="text" class="form-control" id="lastname" name="last_name" required>
                                                <div class="valid-feedback">¡Se ve bien!.</div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="group" class="form-label">Rol y Permisos</label>
                                                <select class="form-select" id="group" name="group" required>
                                                    <option selected disabled value="">Selecciona un Rol</option>
                                                    <?php foreach ($groups as $group) : ?>
                                                        <option value="<?php echo $group['group_level']; ?>"><?php echo ucwords($group['group_name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Por favor selecciona un grupo.
                                                </div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                                <div class="invalid-feedback">
                                                    Ingresa un email valido.
                                                </div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-3">
                                                <label for="password">Password</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                                <div class="invalid-feedback">
                                                    Ingresa una contraseña valida.
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="confirm_password">Confirm Password</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                <div class="invalid-feedback">
                                                    No coinciden las contraseñas
                                                </div>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Body-->
                                    <!--begin::Footer-->
                                    <div class="card-footer">
                                        <div class="float-end">
                                            <button class="btn btn-success" type="submit" name="add_user">Registrar</button>
                                            <button class="btn btn-info" type="reset">Limpiar</button>
                                        </div>
                                    </div>
                                    <!--end::Footer-->
                                </form>
                                <!--end::Form-->
                                <!--begin::JavaScript-->
                                <script>
                                    (() => {
                                        "use strict";

                                        const form = document.querySelector(".needs-validation");

                                        form.addEventListener("submit", (event) => {
                                            if (!form.checkValidity()) {
                                                event.preventDefault();
                                                event.stopPropagation();
                                            }
                                            form.classList.add("was-validated");
                                        }, false);
                                    })();
                                </script>
                                <!--end::JavaScript-->
                            </div>
                            <!--end::Form Validation-->
                        </div>
                    </div>
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Usuarios Registrados</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-maximize">
                                            <i data-lte-icon="maximize" class="bi bi-fullscreen"></i>
                                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Usuario</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th style="width: 40px;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = $offset + 1;
                                        foreach ($users as $user) : ?>
                                            <tr>    
                                                <td><?php echo $i; $i++; ?></td>
                                                <td><?php echo $user['username']; ?></td>
                                                <td><?php echo $user['name']." ".$user['last_name']; ?></td>
                                                <td><?php echo $user['email']; ?></td>
                                                <td>
                                                    <ul class="list-group list-group-horizontal justify-content-between">
                                                        <!--<a href="./edit_user.php?id=<?php echo $user['id'] ?>" class="badge bg-secondary px-2"><i class="bi bi-pencil"></i></a> -->
                                                        <a href="./delete_user.php?id=<?php echo $user['id'] ?>" class="badge bg-danger px-2"><i class="bi bi-trash"></i></a>
                                                    </ul>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="m-0">Total de Usuarios <?= $totalUsers['total'] ?></p>
                                        </div>
                                        <div class="col-6">
                                            <ul class="pagination pagination-sm m-0 float-end">
                                                <?php if ($pagina > 1) : ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?pagina=<?= $pagina - 1 ?>">&laquo;</a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                                    <li class="page-item <?= ($i === $pagina) ? 'active' : '' ?>">
                                                        <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                <?php if ($pagina < $totalPages) : ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?pagina=<?= $pagina + 1 ?>">&raquo;</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!--end::Row-->
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