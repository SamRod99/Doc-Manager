<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();

$id = $_GET['id'] ?? 0;

$sql = "SELECT p.nombre, p.apellido, p.fecha_nacimiento,
               pim.tipo_sangre, pim.alergias, pim.enfermedades_cronicas,
               h.fecha, h.diagnostico, h.observaciones,
               c.motivo,
               m.nombre AS medico_nombre, m.apellido AS medico_apellido
        FROM historial_medico h
        JOIN pacientes p ON h.id_paciente = p.id_paciente
        LEFT JOIN paciente_info_medica pim ON p.id_paciente = pim.id_paciente
        LEFT JOIN citas c ON h.id_cita = c.id_cita
        LEFT JOIN medicos m ON c.id_medico = m.id_medico
        WHERE h.id_historial = :id AND h.id_usuario = :id_usuario";

$stmt = $db->prepare($sql);
$stmt->execute([
    ':id' => $id,
    ':id_usuario' => $id_usuario
]);

$h = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$h) {
    die("Historial no encontrado");
}
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
    <title>Detalle Historial</title>
</head>

<body>
    <aside id="sidebar-container"></aside>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <div class="historial-card">
                <div class="historial-campos">
                    <h2><?php echo $h['nombre'] . " " . $h['apellido']; ?></h2>

                    <div class="historial-row">
                        <div class="historial-label">Fecha</div>
                        <div class="historial-value">
                            <?php echo date('d/m/Y', strtotime($h['fecha'])); ?>
                        </div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Diagnóstico</div>
                        <div class="historial-value"><?php echo $h['diagnostico']; ?></div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Observaciones</div>
                        <div class="historial-value"><?php echo $h['observaciones']; ?></div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Motivo</div>
                        <div class="historial-value"><?php echo $h['motivo']; ?></div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Médico</div>
                        <div class="historial-value">
                            <?php echo $h['medico_nombre'] . " " . $h['medico_apellido']; ?>
                        </div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Tipo Sangre</div>
                        <div class="historial-value"><?php echo $h['tipo_sangre']; ?></div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Alergias</div>
                        <div class="historial-value"><?php echo $h['alergias']; ?></div>
                    </div>

                    <div class="historial-row">
                        <div class="historial-label">Enfermedades Crónicas</div>
                        <div class="historial-value"><?php echo $h['enfermedades_cronicas']; ?></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="js/header.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>