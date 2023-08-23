<?php
$title_page = 'Panel de Usuarios | Kiewit';
//Menus Sidebar
$separador = 'evaluciones';
$page = 'departamentos';

require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}
page_require_level(1);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $limit = 10;
    $offset = ($pagina - 1) * $limit;

    $totalUsers = count_by_id('departamentos');
    $totalPages = ceil($totalUsers['total'] / $limit);

    $users = find_departaments_with_pagination($limit, $offset); // Agrega una función para obtener usuarios paginados



    if (isset($_GET['delete_departameto'])) {
        $delete_id = delete_by_id('departamentos', (int)$_GET['delete_departameto']);
        if ($delete_id) {
            $session->msg("s", "Departamento Eliminado.");
            redirect('departamentos.php');
        } else {
            $session->msg("d", "Algo fallo.");
            redirect('departamentos.php');
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_departamento'])) {
        $req_fields = array('departamento');
        validate_fields($req_fields);
        $depa = remove_junk($db->escape($_POST['departamento']));

        $results = find_by_sql("SELECT * FROM departamentos WHERE departamento='{$depa}'");

        if (empty($results)) {
            if (empty($errors)) {
                echo 'Sin errores';
                $query = "INSERT INTO departamentos (departamento) VALUES ('{$depa}')";
                if ($db->query($query)) {
                    //sucess
                    $session->msg('s', "¡Departamento creado correctamente!");
                    redirect('departamentos.php', false);
                } else {
                    //failed
                    $session->msg('d', 'Lo sentimos, ¡no se ha podido crear el departamento!');
                    redirect('departamentos.php', false);
                }
            } else {
                $session->msg("d", $errors);
                redirect('departamentos.php', false);
            }
        } else {
            $session->msg('d', "Departamento ya registrado.");
            redirect('departamentos.php', false);
        }
    }
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
                            <h3 class="mb-0">Configuracion Departamentos</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Evaluaciones</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Departamentos
                                </li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->
            <?php echo display_msg($msg); ?>
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Departamentos</h3>
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
                                                <th style="width: 20px;">#</th>
                                                <th>Departamento</th>
                                                <th style="width: 40px;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = $offset + 1;
                                            foreach ($users as $user) : ?>
                                                <tr>
                                                    <td><?php echo $i;
                                                        $i++; ?></td>
                                                    <td><?php echo $user['departamento']; ?></td>
                                                    <td>
                                                        <ul class="list-group list-group-horizontal justify-content-center">
                                                            <!-- <a href="./departamentos.php?edit_departameto=<?php echo $user['id'] ?>" class="badge bg-secondary px-2"><i class="bi bi-pencil"></i></a> -->
                                                            <a href="./departamentos.php?delete_departameto=<?php echo $user['id'] ?>" class="badge bg-danger px-2"><i class="bi bi-trash"></i></a>
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
                                            <p class="m-0">Total de Departamentos <?= $totalUsers['total'] ?></p>
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
                        <!-- Empieza el /.col del formulario -->
                        <div class="col-md-8">
                            <!--begin::Form Validation-->
                            <div class="card card-info card-outline">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <div class="card-title">Registrar nuevo Departamento</div>
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
                                <form class="needs-validation" action="./departamentos.php" method="POST" novalidate>
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <!--begin::Row-->
                                        <div class="row g-3">
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="departamento" class="form-label">Departamento</label>
                                                <input type="text" class="form-control" id="departamento" name="departamento" autofocus required>
                                                <div class="valid-feedback">¡Se ve bien!.</div>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Body-->
                                    <!--begin::Footer-->
                                    <div class="card-footer">
                                        <div class="float-end">
                                            <button class="btn btn-success" type="submit" name="add_departamento">Registrar</button>
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
                        <!-- Termina el /.col del formulario -->
                    </div>
                    <!--end::Row-->
                    <div class="row mb-2">


                    </div>
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