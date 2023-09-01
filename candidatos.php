<?php
$title_page = 'Candidatos';
//Menus Sidebar
$separador = 'evaluciones';
$page = 'candidatos';
require_once('includes/load.php');
if (!$session->isUserLoggedIn(true)) {
    redirect('index.php', false);
}

//Obtener todos los departamentos para el select
$departamento = find_all_az('departamentos', 'departamento');

include_once('layouts/head.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registrar_candidato'])) {
        // Validar campos vacíos
        $req_fields = array('nombre', 'apellido', 'email', 'telefono', 'puesto', 'departamento');
        validate_fields($req_fields);

        $nombre = remove_junk($db->escape($_POST['nombre']));
        $apellidos = remove_junk($db->escape($_POST['apellido']));
        $email = remove_junk($db->escape($_POST['email']));
        $telefono = remove_junk($db->escape($_POST['telefono']));
        $puesto = remove_junk($db->escape($_POST['puesto']));
        $departamento = remove_junk($db->escape($_POST['departamento']));
        $password = generarContrasena(8);
        $options = [
            'cost' => 12,
        ];

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);

        // Verificar si el email ya está registrado
        $query = "SELECT id FROM candidatos WHERE email = '$email'";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            // El email ya está registrado, mostrar un mensaje de error
            $session->msg('d', "El email ya está registrado en la base de datos.");
            redirect('candidatos.php', false);
        } else {
            // El email no está registrado, continuar con el proceso de registro

            if (empty($errors)) {
                $query = "INSERT INTO candidatos (nombre, apellidos, email, telefono, puesto, id_departamento, password) VALUES ('$nombre', '$apellidos', '$email', '$telefono', '$puesto', '$departamento', '$hashedPassword')";
                $result = $db->query($query);

                $query = "SELECT id FROM candidatos ORDER BY id DESC LIMIT 1;";
                $result = $db->query($query);
                $id_candidato = $result->fetch_assoc()['id'];
                $query = "INSERT INTO pass_temporal (id, password) VALUES ('$id_candidato', '$password')";
                $result = $db->query($query);

                $session->msg('s', "Candidato registrado exitosamente! ");
                redirect('candidatos.php', false);
            } else {
                $session->msg('d', $errors);
                redirect('candidatos.php', false);
            }
        }
    }
}

$candidatos = find_by_sql("SELECT candidatos.*,departamentos.departamento,pass_temporal.password FROM candidatos JOIN departamentos ON candidatos.id_departamento = departamentos.id JOIN pass_temporal ON candidatos.id = pass_temporal.id;");
$total_candidatos = count($candidatos);

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
                            <h3 class="mb-0">Candidatos</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Evaluaciones</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Candidatos
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
                    <div class="row">
                        <!--begin::Col-->
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon text-bg-primary shadow-sm">
                                    <i class="bi bi-person-vcard"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text"><?= $total_candidatos ?></span>
                                    <span class="info-box-number">
                                        Candidatos Registrados
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row (main row) -->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <?php echo display_msg($msg); ?>
                    <!--begin::Registro de Candidatos-->
                    <div class="row mb-2">
                        <div class="col-md-12 mb-2">
                            <div class="card card-info card-outline">
                                <form action="" method="POST">
                                    <!--begin::Header-->
                                    <div class="card-header">
                                        <div class="card-title">Registro Candidatos</div>
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
                                    <!--begin::Body-->
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <label for="nombre" class="form-label">Nombre</label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" require>
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <label for="apellido" class="form-label">Apellidos</label>
                                                <input type="text" class="form-control" id="apellido" name="apellido" require>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" require>
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <label for="telefono" class="form-label">Telefono</label>
                                                <input type="phone" class="form-control" id="telefono" name="telefono" require>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6 mb-2">
                                                <label for="puesto" class="form-label">Puesto al que aplica</label>
                                                <input type="text" class="form-control" id="puesto" name="puesto" require>
                                            </div>
                                            <div class="col-sm-6 mb-2">
                                                <label for="departamento" class="form-label">Departamento</label>
                                                <select class="form-select" id="departamento" name="departamento" required>
                                                    <option selected disabled value="">Selecciona un Departamento</option>
                                                    <?php foreach ($departamento as $departamento) : ?>
                                                        <option value="<?php echo $departamento['id']; ?>"><?php echo ucwords($departamento['departamento']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Body-->
                                    <!--begin::Footer-->
                                    <div class="card-footer">
                                        <div class="float-end">
                                            <button type="submit" class="btn btn-success" name="registrar_candidato">Registrar</button>
                                        </div>
                                    </div>
                                    <!--end::Footer-->
                                </form>
                            </div>
                        </div>
                        <!--End Col.-->
                    </div>
                    <!--End Row.-->
                    <!--end::Registro de Candidatos-->
                    <!--begin::Lista de Candidatos-->
                    <div class="row">
                        <div class="col-sm-12 mb-2">
                            <div class="card card-info card-outline">
                                <div class="card-header">
                                    <div class="card-title">Candidatos Registrados</div>
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
                                <div class="card-body table-responsive ">
                                    <table class="table table-hover text-nowrap" id="buscarCandidatosTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 20px;">#</th>
                                                <th class="">Nombre</th>
                                                <th class="">Apellidos</th>
                                                <th class="">Email</th>
                                                <th class="">Telefono</th>
                                                <th class="">Departamento</th>
                                                <th class="">Puesto al que aplica</th>
                                                <th class="">Contraseña</th>
                                                <th class="text-center" style="width: 40px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="search_evaluaciones" id=="search_evaluaciones_table">
                                            <?php $i = 1;
                                            foreach ($candidatos as $candidato) : ?>
                                                <tr class="clickable-row" data-href="#">
                                                    <td><?php echo $i;
                                                        $i++; ?></td>
                                                    <td><?= $candidato['nombre'] ?></td>
                                                    <td><?= $candidato['apellidos'] ?></td>
                                                    <td><a href="mailto:<?= $candidato['email'] ?>" class="link-info"><?= $candidato['email'] ?></a></td>
                                                    <td><a href="tel:+52<?= $candidato['telefono'] ?>" class="link-info"><?= $candidato['telefono'] ?></a></td>
                                                    <td><?= $candidato['departamento'] ?></td>
                                                    <td><?= $candidato['puesto'] ?></td>
                                                    <td><?= $candidato['password'] ?></td>
                                                    <td>
                                                        <ul class="list-group list-group-horizontal justify-content-center gap-2 ">
                                                            <a href="#" class="btn btn-sm btn-secondary " data-bs-toggle="tooltip" data-candidato-id="<?php echo $candidato['id'] ?>"><svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope-at" viewBox="0 0 16 16">
                                                                    <path d="M2 2a2 2 0 0 0-2 2v8.01A2 2 0 0 0 2 14h5.5a.5.5 0 0 0 0-1H2a1 1 0 0 1-.966-.741l5.64-3.471L8 9.583l7-4.2V8.5a.5.5 0 0 0 1 0V4a2 2 0 0 0-2-2H2Zm3.708 6.208L1 11.105V5.383l4.708 2.825ZM1 4.217V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v.217l-7 4.2-7-4.2Z" />
                                                                    <path d="M14.247 14.269c1.01 0 1.587-.857 1.587-2.025v-.21C15.834 10.43 14.64 9 12.52 9h-.035C10.42 9 9 10.36 9 12.432v.214C9 14.82 10.438 16 12.358 16h.044c.594 0 1.018-.074 1.237-.175v-.73c-.245.11-.673.18-1.18.18h-.044c-1.334 0-2.571-.788-2.571-2.655v-.157c0-1.657 1.058-2.724 2.64-2.724h.04c1.535 0 2.484 1.05 2.484 2.326v.118c0 .975-.324 1.39-.639 1.39-.232 0-.41-.148-.41-.42v-2.19h-.906v.569h-.03c-.084-.298-.368-.63-.954-.63-.778 0-1.259.555-1.259 1.4v.528c0 .892.49 1.434 1.26 1.434.471 0 .896-.227 1.014-.643h.043c.118.42.617.648 1.12.648Zm-2.453-1.588v-.227c0-.546.227-.791.573-.791.297 0 .572.192.572.708v.367c0 .573-.253.744-.564.744-.354 0-.581-.215-.581-.8Z" />
                                                                </svg>
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-warning" data-candidato-id="<?php echo $candidato['id'] ?>"><svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z" />
                                                                </svg>
                                                            </a>
                                                            <a href="#" class="btn btn-sm btn-danger delete-link" data-candidato-id="<?php echo $candidato['id'] ?>"><svg xmlns=" http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z" />
                                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z" />
                                                                </svg>
                                                            </a>
                                                        </ul>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Lista de Candidatos-->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $(document).ready(function() {
            $('#buscarCandidatosTable').DataTable({
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

        var tooltipTriggerList = Array.prototype.slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body><!--end::Body-->

</html>