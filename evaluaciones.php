<?php
$title_page = 'Evaluaciones | Kiewit';
//Menus Sidebar
$separador = 'evaluciones';
$page = 'evaluaciones';

require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}
page_require_level(2);

//Obtener todos los departamentos
$departamento = find_all_az('departamentos', 'departamento');
$i = 0;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    $limit = 10;
    $offset = ($pagina - 1) * $limit;

    $totalUsers = count_by_id('evaluaciones');
    $totalPages = ceil($totalUsers['total'] / $limit);

    $users = find_evaluaciones_with_pagination($limit, $offset); // Agrega una función para obtener usuarios paginados



    if (isset($_GET['delete_evaluacion'])) {
        $delete_id = delete_by_id('evaluaciones', (int)$_GET['delete_evaluacion']);
        if ($delete_id) {
            $session->msg("s", "Evaluacion Eliminada.");
            redirect('evaluaciones.php');
        } else {
            $session->msg("d", "Algo fallo.");
            redirect('evaluaciones.php');
        }
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_evaluacion'])) {
        $req_fields = array('evaluacion', 'departamento', 'descripcion', 'fecha', 'tiempo');
        validate_fields($req_fields);
        $evaluacion = remove_junk($db->escape($_POST['evaluacion']));

        $results = find_by_sql("SELECT * FROM evaluaciones WHERE titulo='{$evaluacion}'");

        if (empty($results)) {
            $departamento = remove_junk($db->escape($_POST['departamento']));
            $descripcion = $_POST['descripcion'];
            $fecha = remove_junk($db->escape($_POST['fecha']));
            $fechaend = remove_junk($db->escape($_POST['fechaend']));
            $tiempo = remove_junk($db->escape($_POST['tiempo']));

            if (empty($errors)) {
                echo 'Sin errores';
                $query = "INSERT INTO evaluaciones (titulo,id_departamento,descripcion,fecha_inicio,fecha_final,tiempo,estatus) VALUES ('{$evaluacion}','{$departamento}','{$descripcion}','{$fecha}','{$fechaend}','{$tiempo}','0')";
                if ($db->query($query)) {
                    //sucess
                    $session->msg('s', "Evaluacion creada correctamente!");
                    redirect('evaluaciones.php', false);
                } else {
                    //failed
                    $session->msg('d', 'Lo sentimos, ¡no se ha podido crear la evaluacion!');
                    redirect('evaluaciones.php', false);
                }
            } else {
                $session->msg("d", $errors);
                redirect('evaluaciones.php', false);
            }
        } else {
            $session->msg('d', "Departamento ya registrado.");
            redirect('evaluaciones.php', false);
        }
    }



    if (isset($_POST['query'])) {
        $query = $_POST['query'];

        $sql = "SELECT e.*, d.departamento AS descripcion_departamento FROM evaluaciones e JOIN departamentos d ON e.id_departamento = d.id ORDER BY id_departamento ASC LIMIT $limit OFFSET $offset LIKE '%$query%'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row['id'] . "</td><td>" . $row['nombre'] . "</td><td>" . $row['precio'] . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No se encontraron resultados</td></tr>";
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
                            <h3 class="mb-0">Evaluaciones</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="evaluacion_resumen.php">Resumen</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Evaluaciones
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
                    <div class="row mb-2">
                        <!-- Empieza el /.col del formulario -->
                        <div class="col-md-12">
                            <!--begin::Form Validation-->
                            <div class="card card-info card-outline collapsed-card">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <div class="card-title">Crear Nueva Evaluacion</div>
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
                                <!--begin::Body-->
                                <div class="card-body">
                                    <form class="needs-validation" action="./evaluaciones.php" method="POST" novalidate>
                                        <!--begin::Row-->
                                        <div class="row g-3">
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="evaluacion" class="form-label">Titulo de la Evaluacion</label>
                                                <input type="text" class="form-control" id="evaluacion" name="evaluacion" autofocus required>
                                                <div class="valid-feedback">¡Se ve bien!.</div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6">
                                                <label for="departamento" class="form-label">Departamento</label>
                                                <select class="form-select" id="departamento" name="departamento" required>
                                                    <option selected disabled value="">Selecciona un Departamento</option>
                                                    <?php foreach ($departamento as $departamento) : ?>
                                                        <option value="<?php echo $departamento['id']; ?>"><?php echo ucwords($departamento['departamento']); ?></option>
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
                                                <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-4">
                                                <label for="fecha" class="form-label">Fecha de inicio</label>
                                                <input type="date" class="form-control" id="fecha" name="fecha" min="" required>
                                                <div class="valid-feedback">¡Se ve bien!.</div>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-4">
                                                <label for="fechaend" class="form-label">Fecha de cierre</label>
                                                <input type="date" class="form-control" id="fechaend" name="fechaend">
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
                                                <input type="number" class="form-control" id="tiempo" name="tiempo" min="10" step="5" required>
                                                <span class="fw-bold" style="font-size: .7rem;">*Dejar vacio para no tener liminte de tiempo</span>
                                                <div class="valid-feedback">¡Se ve bien!.</div>
                                            </div>
                                            <!--end::Col-->
                                            <div class="col-md-12">
                                                <div class="float-end">
                                                    <button class="btn btn-success" type="submit" name="add_evaluacion">Registrar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Row-->
                                    </form>
                                    <!--end::Form-->
                                </div>
                                <!--end::Body-->
                                <!--begin::Footer-->
                                <div class="card-footer">
                                    <div class="float-end">
                                    </div>
                                </div>
                                <!--end::Footer-->
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
                    <!-- Termina el /.row del formulario -->
                    <!--begin::Row-->
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">Evaluaciones</h3>
                                    <div class="card-tools d-flex">
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-lte-toggle="card-maximize">
                                            <i data-lte-icon="maximize" class="bi bi-fullscreen"></i>
                                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit"></i>
                                        </button>
                                        <form action="" method="get">
                                            <div class="input-group input-group-sm" style="width: 200px;">
                                                <input type="text" name="table_search" class="input-group-text form-control float-right" id="search_evaluaciones" placeholder="Buscar">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-default">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap" id="table_evaluaciones">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px;">#</th>
                                                <th>Titulo</th>
                                                <th class="text-center text-wrap">Departamento</th>
                                                <th class="text-center text-wrap" style="max-width: 60px;">Fecha de Inicio</th>
                                                <th class="text-center text-wrap" style="max-width: 60px;">Fecha de Cierre</th>
                                                <th class="text-center" style="max-width: 40px;">Tiempo</th>
                                                <th class="text-center text-wrap" style="max-width: 60px;">Estatus</th>
                                                <th class="text-center text-wrap" style="max-width: 40px;">Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="search_evaluaciones" id=="search_evaluaciones_table">
                                            <?php $i = $offset + 1;
                                            foreach ($users as $user) : ?>
                                                <tr class="clickable-row" data-href="./evaluacion_detalle.php?id=<?= $user['id'] ?>">
                                                    <td><?php echo $i;
                                                        $i++; ?></td>
                                                    <td class="text-wrap"><?php echo $user['titulo']; ?></td>
                                                    <td class="text-center text-wrap"><?php echo $user['descripcion_departamento']; ?></td>
                                                    <td class="text-center "><?php echo $user['fecha_inicio']; ?></td>
                                                    <td class="text-center "><?php echo $user['fecha_final']; ?></td>
                                                    <td class="text-center text-wrap"><?php echo $user['tiempo']; ?></td>
                                                    <td class="text-center text-wrap"><?php
                                                                                        if ($user['estatus'] == 0) {
                                                                                            echo '<span class="badge fs-6 text-bg-secondary">Pendiente</span>';
                                                                                        } elseif ($user['estatus'] == 1) {
                                                                                            echo '<span class="badge fs-6 text-bg-success">Activa</span>';
                                                                                        } elseif ($user['estatus'] == 2) {
                                                                                            echo '<span class="badge fs-6 text-bg-danger">Desactivada</span>';
                                                                                        }
                                                                                        ?></td>
                                                    <td>
                                                        <ul class="list-group list-group-horizontal justify-content-center gap-2 ">
                                                            <a href="./evaluacion_detalle.php?id=<?= $user['id'] ?>" class="fs-6 badge bg-secondary px-2"><i class="bi bi-pencil"></i></a>
                                                            <a href="./evaluaciones.php?delete_evaluacion=<?php echo $user['id'] ?>" class="fs-6 badge bg-danger px-2 delete-link" data-user-id="<?php echo $user['id'] ?>"><i class="bi bi-trash"></i></a>
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
                                            <p class="m-0">Total de evaluaciones <?= $totalUsers['total'] ?></p>
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



    <!-- Modal -->
    <div class="modal" id="deleteConfirmationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar esta evaluacion?
                </div>
                <div class="modal-footer">
                    <a id="confirmDeleteButton" href="#" class="btn btn-danger">Eliminar</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::App Wrapper-->
    <?php include_once('layouts/scripts.php'); //scripts 
    ?>


    <!-- OPTIONAL SCRIPTS -->
    <!-- jquey -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <!-- include summernote css/js-->
    <link href="./includes/libs/summernote/summernote-lite.css" rel="stylesheet">
    <script src="./includes/libs/summernote/summernote-lite.js"></script>
    <script>
        $('#descripcion').summernote({
            placeholder: 'Agrega una descripcion de la prueba',
            height: 300,
            callbacks: {
                onImageUpload: function(image, editor, welEditable) {
                    uploadImage(image[0]);
                }
            },
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
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
            // Obtén todos los enlaces con la clase 'delete-link'
            const deleteLinks = document.querySelectorAll(".delete-link");

            // Itera sobre los enlaces y agrega un listener de clic a cada uno
            deleteLinks.forEach(function(link) {
                link.addEventListener("click", function(event) {
                    event.preventDefault(); // Evita que el enlace se abra

                    const userId = link.getAttribute("data-user-id"); // Obtiene el ID del usuario
                    const modal = new bootstrap.Modal(document.getElementById("deleteConfirmationModal")); // Crea una instancia del modal

                    // Actualiza el enlace del botón de confirmación del modal con el ID del usuario
                    const confirmButton = document.getElementById("confirmDeleteButton");
                    confirmButton.setAttribute("href", `./evaluaciones.php?delete_evaluacion=${userId}`);

                    // Muestra el modal
                    modal.show();
                });
            });
        });

        //////////Busqueda de la tabla.

        document.addEventListener("DOMContentLoaded", function() {
            var searchInput = document.getElementById("search_evaluaciones");
            var tableRows = document.querySelectorAll("#table_evaluaciones tbody tr");

            searchInput.addEventListener("keyup", function() {
                var query = searchInput.value.toLowerCase().trim();

                tableRows.forEach(function(row) {
                    var title = row.querySelector(".text-wrap").textContent.toLowerCase();

                    if (title.indexOf(query) !== -1) {
                        row.style.display = "table-row";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        });
    </script>
</body><!--end::Body-->

</html>