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
                                                    Por favor selecciona un departamento.
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
                                <div class="card-body table-responsive">
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
                                                            <a href="./evaluacion_detalle.php?id=<?= $user['id'] ?>" class="fs-6 badge bg-secondary px-2"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                                                </svg></a>
                                                            <a href="./evaluaciones.php?delete_evaluacion=<?php echo $user['id'] ?>" class="fs-6 badge bg-danger px-2 delete-link" data-user-id="<?php echo $user['id'] ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                                                </svg></a>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer clearfix">
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
    <!-- include summernote css/js-->
    <script>
        $(document).ready(function() {
            $('#table_evaluaciones').DataTable({
                dom: '<"d-flex justify-content-between"Bf><"mb-2"rt><"card-footer"<"d-flex justify-content-between"ip>>',
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i>',
                        titleAttr: 'Exportar a Excel',
                        className: 'btn btn-success',
                        title: 'Título del documento',
                        exportOptions: {
                            columns: [2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="bi bi-file-earmark-pdf"></i>',
                        titleAttr: 'Exportar a PDF',
                        className: 'btn btn-danger ',
                        title: 'Título del documento',
                        exportOptions: {
                            columns: [2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i>',
                        titleAttr: 'Imprimir',
                        className: 'btn btn-info ',
                        exportOptions: {
                            columns: [2, 3, 4, 5]
                        }
                    }
                ],
                language: {
                    decimal: ",",
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json'

                }

            });
        });

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