<?php
$action = $_GET['action'];

if ($action === 'sales') {
    // Obtener el mes actual y año
    $currentMonth = date('m');
    $currentYear = date('Y');

    // Consulta para obtener las ventas del mes actual
    $query = "SELECT producto, SUM(cantidad) AS ventas FROM ventas WHERE MONTH(fecha) = :month AND YEAR(fecha) = :year GROUP BY producto";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':month', $currentMonth);
    $stmt->bindParam(':year', $currentYear);
    $stmt->execute();

    $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($salesData);
} elseif ($action === 'other_query') {
    // Otra consulta según tu necesidad
    // ...
} else {
    // Acción no reconocida
    echo "Acción no válida";
}
?>