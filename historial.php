<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
date_default_timezone_set('America/Mexico_City');
require_once 'BD/conectar.php';

$db = Conexion::ConexionBD();

/* ================= BUSQUEDA ================= */
$pacienteSeleccionado = $_GET['paciente'] ?? '';

/* ================= PACIENTES ================= */
$sqlPacientes = "SELECT id_paciente, nombre, apellido 
                 FROM pacientes 
                 WHERE COALESCE(eliminado,0)=0 
                 AND id_usuario = :id_usuario 
                 ORDER BY nombre";

$stmtPacientes = $db->prepare($sqlPacientes);
$stmtPacientes->execute([':id_usuario' => $id_usuario]);

/* ================= HISTORIAL ================= */
$sqlHistorial = "SELECT p.id_paciente, p.nombre, p.apellido, p.fecha_nacimiento,
                        pim.tipo_sangre, pim.alergias, pim.enfermedades_cronicas,
                        h.id_historial, h.fecha, h.diagnostico, h.observaciones,
                        c.motivo,
                        m.nombre AS medico_nombre, m.apellido AS medico_apellido
                 FROM historial_medico h
                 JOIN pacientes p ON h.id_paciente = p.id_paciente
                 LEFT JOIN paciente_info_medica pim ON p.id_paciente = pim.id_paciente
                 LEFT JOIN citas c ON h.id_cita = c.id_cita
                 LEFT JOIN medicos m ON c.id_medico = m.id_medico
                 WHERE h.id_usuario = :id_usuario";

if ($pacienteSeleccionado != '') {
    $sqlHistorial .= " AND p.id_paciente = :id";
}

$sqlHistorial .= " ORDER BY h.fecha DESC";

$stmtHistorial = $db->prepare($sqlHistorial);
$params = [':id_usuario' => $id_usuario];

if ($pacienteSeleccionado != '') {
    $params[':id'] = $pacienteSeleccionado;
}

$stmtHistorial->execute($params);
$historial = $stmtHistorial->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/globales.css">
    <link rel="stylesheet" href="css/historial.css">
    <title>DocManager - Historial</title>
</head>

<body data-title="Historial">
    <aside id="sidebar-container"></aside>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <div class="historial-search">
                <form method="GET" class="search-wrapper">
                    <svg viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <select name="paciente" onchange="this.form.submit()">
                        <option value="">Todos los pacientes</option>
                        <?php while ($pac = $stmtPacientes->fetch(PDO::FETCH_ASSOC)) : ?>
                            <option value="<?php echo $pac['id_paciente']; ?>" <?php echo ($pacienteSeleccionado == $pac['id_paciente']) ? "selected" : ""; ?>>
                                <?php echo $pac['nombre'] . " " . $pac['apellido'] . " — ID: " . $pac['id_paciente']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>

            <div class="historial-card">
                <div class="historial-campos">
                    <table class="tabla-historial">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Diagnóstico</th>
                                <th>Motivo</th>
                                <th>Médico</th>
                                <th>Tipo Sangre</th>
                                <th>Alergias</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $h) : ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($h['fecha'])); ?></td>
                                <td><?php echo $h['nombre'] . " " . $h['apellido']; ?></td>
                                <td><?php echo $h['diagnostico'] ?? 'N/A'; ?></td>
                                <td><?php echo $h['motivo'] ?? 'N/A'; ?></td>
                                <td><?php echo $h['medico_nombre'] . " " . $h['medico_apellido']; ?></td>
                                <td><?php echo $h['tipo_sangre'] ?? 'N/A'; ?></td>
                                <td><?php echo $h['alergias'] ?? 'Ninguna'; ?></td>
                                <td>
                                    <a href="historial_detalle.php?id=<?php echo $h['id_historial']; ?>" class="btn-historial">
                                        Ver completo
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="js/header.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>
