<?php
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}
page_require_level(2);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['id'])) {
        $id = remove_junk($_GET['id']);
        $resultado = find_evaluacion($id);
        if (!empty($resultado)) {
            $evaluacion = $resultado['titulo'];
            $id_departamento = $resultado['id_departamento'];
            $departamento = $resultado['descripcion_departamento'];
            $descripcion = $resultado['descripcion'];
            $fecha_inicio = $resultado['fecha_inicio'];
            $fecha_cierre = $resultado['fecha_final'];
            $tiempo = $resultado['tiempo'];
            $estatus = $resultado['estatus'];
            $pregunta_aleatorias = $resultado['preguntas_aleatorias'];
        } else {
            $session->msg('d', 'No existe la evaluacion!');
            redirect('evaluaciones.php', false);
        }
    } else {
        redirect('evaluaciones.php', false);
    }

    if (isset($_GET['delete_pregunta'])) {
        $delete_id = delete_by_id('preguntas', (int)$_GET['delete_pregunta']);
        if ($delete_id) {
            $session->msg("s", "Pregunta Eliminada.");
            redirect('evaluacion_detalle.php?id=' . $_GET['id']);
        } else {
            $session->msg("d", "Algo fallo.");
            redirect('evaluacion_detalle.php?id=' . $_GET['id']);
        }
    }

    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $limit = 10;
    $offset = ($pagina - 1) * $limit;

    $totalPreguntas = count_questions_by_id($id);
    $totalPages = ceil($totalPreguntas['total'] / $limit);

    $preguntas = find_with_pagination('preguntas', 'id_evaluacion', $id, $limit, $offset); //

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_evaluacion'])) {
        $req_fields = array('evaluacion', 'departamento', 'descripcion', 'fecha', 'tiempo', 'id_edit');
        validate_fields($req_fields);
        $evaluacion = remove_junk($db->escape($_POST['evaluacion']));
        $id_edit = remove_junk($db->escape($_POST['id_edit']));
        $departamento = remove_junk($db->escape($_POST['departamento']));
        $descripcion = $_POST['descripcion'];
        $fecha = remove_junk($db->escape($_POST['fecha']));
        $fechaend = remove_junk($db->escape($_POST['fechaend']));
        $tiempo = remove_junk($db->escape($_POST['tiempo']));

        if (empty($errors)) {
            echo 'Sin errores';
            $query = "UPDATE evaluaciones SET titulo = '{$evaluacion}', id_departamento = '{$departamento}', descripcion = '{$descripcion}', fecha_inicio = '{$fecha}',fecha_final = '{$fechaend}', tiempo = '{$tiempo}' WHERE evaluaciones.id = $id_edit";
            if ($db->query($query)) {
                //sucess
                $session->msg('s', "Evaluacion modificada correctamento!");
                redirect('./evaluacion_detalle.php?id=' . $id_edit, false);
            } else {
                //failed
                $session->msg('d', 'Lo sentimos, ¡no se ha podido modificar la evaluacion!');
                redirect('evaluacion_detalle.php?id=' . $id, false);
            }
        } else {
            $session->msg("d", $errors);
            redirect('evaluaciones.php', false);
        }
    }
}

// Verifica si se recibió el estado en la petición POST
if (isset($_POST["estado"])) {
    $estado = intval($_POST["estado"]); // Convierte a un número entero
    $id = $_GET['id'];
    // Actualiza la base de datos con el nuevo estado
    $sql = "UPDATE evaluaciones SET estatus = $estado WHERE id = $id"; // Cambia 'id' según tu necesidad
    if ($db->query($sql) === TRUE) {
        echo "Estado actualizado correctamente";
    } else {
        echo "Error al actualizar el estado: " . $db->error;
    }
}

// Verifica si se recibió el estado en la petición POST para iniciar pregunta ramdom
if (isset($_POST["estado_pregunta"])) {
    $estado = intval($_POST["estado_pregunta"]); // Convierte a un número entero
    $id = $_GET['id'];
    // Actualiza la base de datos con el nuevo estado
    $sql = "UPDATE evaluaciones SET preguntas_aleatorias = $estado WHERE id = $id"; // Cambia 'id' según tu necesidad
    if ($db->query($sql) === TRUE) {
        echo "Preguntas aleatoarias actualizado correctamente";
    } else {
        echo "Error al actualizar el estado: " . $db->error;
    }
}


//Verifica el cambio de estado de una pregunta
if (isset($_POST["questionId"])) {
    $id = $_POST["questionId"];
    $cheked = $_POST["status"];
    // Actualiza la base de datos con el nuevo estado
    $sql = "UPDATE preguntas SET estatus = $cheked WHERE id = $id"; // Cambia 'id' según tu necesidad
    if ($db->query($sql) === TRUE) {
        echo "Preguntas aleatoarias actualizado correctamente";
    } else {
        echo "Error al actualizar el estado: " . $db->error;
    }
}



$title_page = 'Evaluacuion | ' . $evaluacion;
//Menus Sidebar
$separador = 'evaluciones';
$page = 'evaluaciones';

//Obtener todos los departamentos
$departamento = find_all_az('departamentos', 'departamento');
$i = 0;


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
                            <h3 class="mb-0"><?= $evaluacion ?></h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="./evaluaciones.php">Evaluaciones</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?= $evaluacion ?>
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
                    <!--begin::Row-->
                    <div class="row mb-2">
                        <!--begin::Col-->
                        <div class="col-lg-6 col-12 mb-2">
                            <!--begin::Small Box Widget 1-->
                            <div class="card card-success card-outline ">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-title"><i class="bi bi-toggles"></i> Panel de Control</h3>
                                    </div>
                                    <div class="card-tools">

                                    </div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Form-->
                                <!--begin::Body-->
                                <div class="card-body">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-2">
                                            <p class="m-0">Estado de evaluacion:</p>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="estatus_prueba" <?php if ($estatus == 1) echo "checked"; ?> />
                                                <label class="form-check-label" for="estatus_prueba"><?php echo ($estatus == 1) ? 'On' : 'Off'; ?></label>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <p class="m-0">Preguntas Aleatorias:</p>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="random_estatus" <?php if ($pregunta_aleatorias == 1) echo "checked"; ?> />
                                                <label class="form-check-label" for="random_estatus"><?php echo ($pregunta_aleatorias == 1) ? 'On' : 'Off'; ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Body-->
                            </div>
                        </div>
                        <!--begin::Col-->
                        <div class="col-lg-3 col-6">
                            <!--begin::Small Box Widget 1-->
                            <div class="small-box text-bg-primary">
                                <div class="inner">
                                    <h3><?= $totalPreguntas['total'] ?></h3>

                                    <p>Preguntas</p>
                                </div>
                                <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M5.933.87a2.89 2.89 0 0 1 4.134 0l.622.638.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636zM7.002 11a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm1.602-2.027c.04-.534.198-.815.846-1.26.674-.475 1.05-1.09 1.05-1.986 0-1.325-.92-2.227-2.262-2.227-1.02 0-1.792.492-2.1 1.29A1.71 1.71 0 0 0 6 5.48c0 .393.203.64.545.64.272 0 .455-.147.564-.51.158-.592.525-.915 1.074-.915.61 0 1.03.446 1.03 1.084 0 .563-.208.885-.822 1.325-.619.433-.926.914-.926 1.64v.111c0 .428.208.745.585.745.336 0 .504-.24.554-.627z" />
                                    </path>
                                </svg>
                                <a href="#Preguntas" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                    Ver Preguntas <i class="bi bi-link-45deg"></i>
                                </a>
                            </div>
                            <!--end::Small Box Widget 1-->
                        </div>
                        <!--end::Col-->
                        <div class="col-lg-3 col-6">
                            <!--begin::Small Box Widget 2-->
                            <div class="small-box text-bg-success">
                                <div class="inner">
                                    <h3>53<sup class="fs-5">%</sup></h3>

                                    <p>Bounce Rate</p>
                                </div>
                                <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z">
                                </svg>
                                <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                    More info <i class="bi bi-link-45deg"></i>
                                </a>
                            </div>
                            <!--end::Small Box Widget 2-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::row-->

                    <?php echo display_msg($msg); ?>
                    <!-- /.row (main row) -->
                    <div class="row mb-2">
                        <!-- Empieza el /.col del formulario -->
                        <div class="col-12">
                            <!--begin::Form Validation-->
                            <div class="card card-warning card">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <div class="card-title">Datos Generales</div>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool me-2" data-lte-toggle="card-maximize">
                                            <i data-lte-icon="maximize" class="bi bi-fullscreen"></i>
                                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Form-->
                                <!--begin::Body-->
                                <div class="card-body">
                                    <h3><?= $evaluacion ?></h3>
                                    <div class="descripcion">
                                        <?= $descripcion ?>
                                    </div>
                                    <hr>
                                    <div class="datos d-flex gap-2">
                                        <div class="incio">
                                            <p>Fecha de Inicio: <span class="fw-bold"><?= $fecha_inicio ?></span></p>
                                        </div>
                                        <div class="cierre">
                                            <p>Fecha de Cierre: <span class="fw-bold"><?= $fecha_cierre ?></span></p>
                                        </div>
                                        <div class="tiempo">
                                            <p>Tiempo en Minutos: <span class="fw-bold"><?= $tiempo ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Body-->
                                <!--begin::Footer-->
                                <div class="card-footer">
                                    <div class="float-end">
                                        <button type="button" id="editar_evaluacion" class="btn btn-outline-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                            </svg>
                                            <span>Editar Evaluacion</span>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Footer-->
                                <!--begin::JavaScript-->
                                <script>

                                </script>
                                <!--end::JavaScript-->
                            </div>
                            <!--end::Form Validation-->
                        </div>
                        <!-- Termina el /.col del formulario -->
                    </div>
                    <!-- /.row (main row) -->
                    <div class="row mb-2">
                        <!-- Empieza el /.col del formulario -->
                        <div class="col-12">
                            <!--begin:: Card de agregar pregtas-->
                            <div class="card card-primary card-outline">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <div class="card-title">Preguntas</div>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool me-2" data-lte-toggle="card-maximize">
                                            <i data-lte-icon="maximize" class="bi bi-fullscreen"></i>
                                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"></i>
                                        </button>
                                    </div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Body-->
                                <div id="Preguntas" class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="text-wrap">Pregunta</th>
                                                <th class="text-wrap" class="text-wrap">Respuesta A</th>
                                                <th class="text-wrap">Respuesta B</th>
                                                <th class="text-wrap">Respuesta C</th>
                                                <th class="text-wrap">Respuesta D</th>
                                                <th class="text-wrap">Valor</th>
                                                <th class="text-wrap text-center">Respuesta Correcta</th>
                                                <th class="text-wrap text-center" style="width: 40px;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = $offset + 1;
                                            foreach ($preguntas as $pregunta) : ?>
                                                <tr class="clickable-row" data-href="./#.php?id=<?= $pregunta['id'] ?>">
                                                    <td class="align-middle"><?php echo $i;
                                                                                $i++; ?></td>
                                                    <td class="text-wrap">
                                                        <div class="truncate"><?php echo $pregunta['pregunta']; ?></div>
                                                    </td>
                                                    <td class="text-wrap">
                                                        <div class="truncate"><?php echo $pregunta['respuesta_a']; ?></div>
                                                    </td>
                                                    <td class="text-wrap">
                                                        <div class="truncate"><?php echo $pregunta['respuesta_b']; ?></div>
                                                    </td>
                                                    <td class="text-wrap">
                                                        <div class="truncate"><?php echo $pregunta['respuesta_c']; ?></div>
                                                    </td>
                                                    <td class="text-wrap">
                                                        <div class="truncate"><?php echo $pregunta['respuesta_d']; ?></div>
                                                    </td>
                                                    <td class="text-center align-middle">10</td>
                                                    <td class="text-wrap text-center align-middle "><?php echo $pregunta['respuesta_correcta']; ?></td>
                                                    <td class="align-middle">
                                                        <ul class="list-group list-group-horizontal justify-content-center gap-2 ">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input question_id" type="checkbox" role="switch" data-question-href="<?= $pregunta['id'] ?>" <?php if ($pregunta['estatus'] == 1) echo "checked"; ?>/>
                                                            </div>
                                                            <a href="./evaluacion_detalle.php?id=<?= $pregunta['id'] ?>" class="fs-6 btn btn-secondary "><i class="bi bi-pencil"></i></a>
                                                            <a href="./evaluaciones.php?delete_evaluacion=<?php echo $pregunta['id'] ?>" class="fs-6 btn btn-danger px-2 delete-link" data-pregunta-id="<?php echo $pregunta['id'] ?>"><i class="bi bi-trash"></i></a>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!--end::Body-->
                                <!--begin::Footer-->
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="m-0">Total de Preguntas <?= $totalPreguntas['total'] ?></p>
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
                                <!--end::Footer-->
                            </div>
                            <!--end:: Card de agregar pregtas-->
                        </div>
                        <!-- Termina el /.col -->
                    </div>
                    <!-- /.row (main row) -->
                </div>
            </div>
            <!--end::Container-->
        </main>
        <!--end::App Main-->
        <?php include_once('layouts/footer.php'); //footer 
        ?>
    </div>
    <!--end::App Wrapper-->
    <?php include_once('layouts/scripts.php'); //scripts 
    ?>

    <!-- Modal -->
    <div class="modal" id="edit_evaluacion">
        <style>
            @media (max-width: 576px) {
                .modal-dialog {
                    padding: 0 !important;
                    margin-right: auto;
                    margin-left: auto;
                }
            }
        </style>

        <!-- Modal Editar evaluacion -->
        <div class="modal-dialog" style="max-width: 100%;  padding: 0 2rem; ">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar evaluacion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="needs-validation" action="./evaluacion_detalle.php?id=<?= $id ?>" method="POST" novalidate>
                    <div class="modal-body">
                        <!--begin::Row-->
                        <div class="row g-3">
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <label for="evaluacion" class="form-label">Titulo de la Evaluacion</label>
                                <input type="text" class="form-control" id="evaluacion" name="evaluacion" value="<?= $evaluacion ?>" autofocus required>
                                <div class="valid-feedback">¡Se ve bien!.</div>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-6">
                                <label for="departamento" class="form-label">Departamento</label>
                                <select class="form-select" id="departamento" name="departamento" required>
                                    <option disabled value="">Selecciona un Departamento</option>
                                    <?php foreach ($departamento as $departamento) : ?>
                                        <option value="<?php echo $departamento['id']; ?>" <?php echo ($departamento['id'] == $id_departamento) ? "selected" : ""; ?>><?php echo ucwords($departamento['departamento']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor selecciona un grupo.
                                </div>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripcion de la evaluacion</label>
                                <textarea name="descripcion" id="descripcion" class="form-control"><?= $descripcion ?></textarea>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4">
                                <label for="fecha" class="form-label">Fecha de inicio</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" min="" value="<?= $fecha_inicio ?>" required>
                                <div class="valid-feedback">¡Se ve bien!.</div>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4">
                                <label for="fechaend" class="form-label">Fecha de cierre</label>
                                <input type="date" class="form-control" id="fechaend" name="fechaend" value="<?= $fecha_cierre ?>">
                                <span class="fw-bold" style="font-size: .7rem;">*Dejar vacio si no tiene vigencia</span>
                                <div class="valid-feedback">¡Se ve bien!.</div>
                                <div class="invalid-feedback">
                                    La fecha debe ser igual o mayor a la fecha de inicio.
                                </div>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4">
                                <label for="tiempo" class="form-label">Tiempo en minutos</label>
                                <input type="number" class="form-control" id="tiempo" name="tiempo" min="10" step="5" value="<?= $tiempo ?>" required>
                                <span class="fw-bold" style="font-size: .7rem;">*Dejar vacio para no tener liminte de tiempo</span>
                                <div class="valid-feedback">¡Se ve bien!.</div>
                            </div>
                            <!--end::Col-->
                            <input type="text" hidden value="<?= $id ?>" name="id_edit">
                        </div>
                        <!--end::Row-->

                        <!--end::Form-->
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" type="submit" name="edit_evaluacion">Actualizar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Termina Modal Editar evaluacion -->
    <!-- Modal confirmacion de eliminacion-->
    <div class="modal" id="deleteConfirmationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta pregunta?
                </div>
                <div class="modal-footer">
                    <a id="confirmDeleteButton" href="#" class="btn btn-danger">Eliminar</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- OPTIONAL SCRIPTS -->
    <!-- OPTIONAL SCRIPTS -->
    <!-- jquey -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <!-- include summernote css/js-->
    <link href="./includes/libs/summernote/summernote-lite.css" rel="stylesheet">
    <script src="./includes/libs/summernote/summernote-lite.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Obtén todos los enlaces con la clase 'delete-link'
            const deleteLinks = document.querySelectorAll(".delete-link");

            // Itera sobre los enlaces y agrega un listener de clic a cada uno
            deleteLinks.forEach(function(link) {
                link.addEventListener("click", function(event) {
                    event.preventDefault(); // Evita que el enlace se abra

                    const preguntaId = link.getAttribute("data-pregunta-id"); // Obtiene el ID del usuario
                    const modal = new bootstrap.Modal(document.getElementById("deleteConfirmationModal")); // Crea una instancia del modal
                    // Obtiene la URL completa
                    var urlCompleta = window.location.href;
                    console.log("URL completa:", urlCompleta);

                    // Actualiza el enlace del botón de confirmación del modal con el ID del usuario
                    const confirmButton = document.getElementById("confirmDeleteButton");
                    confirmButton.setAttribute("href", `${urlCompleta}&delete_pregunta=${preguntaId}`);

                    // Muestra el modal
                    modal.show();
                });
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const editar_evaluacion = document.getElementById("editar_evaluacion");
            editar_evaluacion.addEventListener("click", function() {
                $('#edit_evaluacion').modal('show');
            });

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
        })


        $('#descripcion').summernote({
            placeholder: 'Agrega una descripcion de la prueba',
            height: 300,
            callbacks: {
                onImageUpload: function(image, editor, welEditable) {
                    uploadImage(image[0]);
                }
            },
            styleTags: [
                'p', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre'
            ],
            fontNames: ['Segoe UI', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New'],
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'hr']],
                ['view', ['fullscreen', 'codeview']],
            ]

        });
        $('.note-editing-area').css('background-color', '#fff');




        function uploadImage(image) {
            var currentURL = window.location.href; // Obtiene la URL completa
            var baseURL = currentURL.substring(0, currentURL.lastIndexOf('/') + 1); // Obtiene la parte de la URL hasta la última barra
            console.log(baseURL); // Muestra la URL base en la consola para verificar
            var data = new FormData();
            data.append("image", image);
            $.ajax({
                url: './includes/upload.php',
                cache: false,
                contentType: false,
                processData: false,
                data: data,
                type: "post",
                success: function(url) {
                    var image = $('<img>').attr('src', baseURL + url);
                    $('#descripcion').summernote("insertNode", image[0]);
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }
        document.addEventListener("DOMContentLoaded", function() {
            const fechaInicioInput = document.getElementById("fecha");
            const fechaCierreInput = document.getElementById("fechaend");

            fechaInicioInput.addEventListener("change", function() {
                validarFechas();
            });

            fechaCierreInput.addEventListener("change", function() {
                validarFechas();
            });

            function validarFechas() {
                const fechaInicio = new Date(fechaInicioInput.value);
                const fechaCierre = new Date(fechaCierreInput.value);

                if (!fechaInicioInput.value) {
                    fechaInicioInput.classList.add("is-invalid");
                    return; // No continuamos con la validación si la fecha de inicio está vacía
                } else {
                    fechaInicioInput.classList.remove("is-invalid");
                }

                if (fechaInicio > fechaCierre) {
                    fechaCierreInput.classList.add("is-invalid");
                } else {
                    fechaCierreInput.classList.remove("is-invalid");
                }
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            const switchPrueba = document.getElementById("estatus_prueba");

            switchPrueba.addEventListener("change", function() {
                const isChecked = switchPrueba.checked;
                const status = isChecked ? 1 : 2;
                const statusText = isChecked ? "On" : "Off";

                // Envía el estado al servidor mediante una petición fetch
                fetch(window.location.href, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: "estado=" + status,
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data); // Respuesta del servidor
                        // Actualiza el texto del label con el nuevo estado
                        const labelElement = document.querySelector("label[for='estatus_prueba']");
                        labelElement.textContent = statusText;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            });
            const switchPregunta = document.getElementById("random_estatus");

            switchPregunta.addEventListener("change", function() {
                const isChecked = switchPregunta.checked;
                const status = isChecked ? 1 : 2;
                const statusText = isChecked ? "On" : "Off";

                // Envía el estado al servidor mediante una petición fetch
                fetch(window.location.href, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: "estado_pregunta=" + status,
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data); // Respuesta del servidor
                        // Actualiza el texto del label con el nuevo estado
                        const labelElement = document.querySelector("label[for='random_estatus']");
                        labelElement.textContent = statusText;
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            });

            //Activar preguntas o desactivar
            var pregunta = document.querySelectorAll('.question_id');

            pregunta.forEach(function(pregunta_id) {
                pregunta_id.addEventListener('change', function() {
                    var questionId = this.getAttribute('data-question-href');
                    var isChecked = this.checked;
                    var status = isChecked ? 1 : 2;
                    var urlCompleta = window.location.href;

                    // Envía el estado al servidor mediante una petición fetch
                    fetch(window.location.href, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded",
                            },
                            body: "questionId=" + questionId +"&status=" + status,
                        })
                        .then(response => response.text())
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });
        });
    </script>

    <script>

    </script>
</body><!--end::Body-->

</html>