<?php
require_once('includes/load.php');

if (isset($_POST['upload_user'])) {
    $targetDir = "uploads/users/";
    $imageName = basename($_FILES["profileImage"]["name"]);
    $targetFile = $targetDir . $imageName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Verificar si es una imagen real
    $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
    }

    // Verificar tamaño del archivo
    if ($_FILES["profileImage"]["size"] > 5000000) {
        $uploadOk = 0;
        echo "imagen pesada";
    }

    // Permitir solo ciertos formatos de imagen
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $uploadOk = 0;
        echo "No es formato valido";
    }

    // Si el archivo ya existe, agregar un sufijo único al nombre del archivo
    $counter = 1;
    while (file_exists($targetFile)) {
        $imageName = pathinfo($_FILES["profileImage"]["name"], PATHINFO_FILENAME) . '_' . $counter . '.' . $imageFileType;
        $targetFile = $targetDir . $imageName;
        $counter++;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            // Actualizar la base de datos con el nombre del archivo
            $userId = $_SESSION["user_id"];
            $consulta = "UPDATE users SET image = '$imageName' WHERE id = '$userId'";
            replace_image_profile($consulta);
            $imageName;
            redirect('perfil.php', false);
        }
    }
} else {
    echo "error";
}
?>