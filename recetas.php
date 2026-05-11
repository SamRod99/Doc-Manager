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

/* ================= GUARDAR ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO recetas (id_cita, medicamento, dosis, duracion, fecha_emision, id_usuario)
            VALUES (:id_cita, :medicamento, :dosis, :duracion, :fecha_emision, :id_usuario)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id_cita'       => $_POST['cita'],
        ':medicamento'   => $_POST['medicamento'],
        ':dosis'         => $_POST['dosis'],
        ':duracion'      => $_POST['duracion'],
        ':fecha_emision' => $_POST['fecha_emision'],
        ':id_usuario'    => $id_usuario
    ]);

    header("Location: recetas.php");
    exit;
}

/* ================= CITAS ================= */
$sqlCitas = "SELECT c.id_cita, p.nombre, p.apellido, m.nombre AS medico_nombre, m.apellido AS medico_apellido
             FROM citas c
             JOIN pacientes p ON c.id_paciente = p.id_paciente
             JOIN medicos m ON c.id_medico = m.id_medico
             WHERE c.id_usuario = :id_usuario
             ORDER BY c.id_cita DESC";

$stmtCitas = $db->prepare($sqlCitas);
$stmtCitas->execute([':id_usuario' => $id_usuario]);

/* ================= RECETAS ================= */
$sqlRecetas = "SELECT r.id_receta, r.medicamento, r.dosis, r.duracion, r.fecha_emision, p.nombre, p.apellido
               FROM recetas r
               LEFT JOIN citas c ON r.id_cita = c.id_cita
               LEFT JOIN pacientes p ON c.id_paciente = p.id_paciente
               WHERE r.id_usuario = :id_usuario
               ORDER BY r.id_receta DESC";

$stmtRecetas = $db->prepare($sqlRecetas);
$stmtRecetas->execute([':id_usuario' => $id_usuario]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/globales.css">
    <link rel="stylesheet" href="css/recetas.css">
    <title>DocManager - Recetas</title>
</head>

<body data-title="Recetas">
    <aside id="sidebar-container"></aside>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <!-- ================= FORMULARIO ================= -->
            <form class="form-card" method="POST">
                <h2 class="form-title">Nueva Receta</h2>
                
                <div class="form-col">
                    <div class="field-group">
                        <label>Cita vinculada</label>
                        <select name="cita" required>
                            <option value="">Seleccionar cita...</option>
                            <?php while ($c = $stmtCitas->fetch(PDO::FETCH_ASSOC)) : ?>
                                <option value="<?php echo $c['id_cita']; ?>">
                                    <?php echo "Cita #" . $c['id_cita'] . " — " . $c['nombre'] . " " . $c['apellido'] . " / Dr. " . $c['medico_nombre']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Medicamento</label>
                        <input type="text" name="medicamento" placeholder="Ej. Losartan" required>
                    </div>

                    <div class="field-group">
                        <label>Dosis</label>
                        <input type="text" name="dosis" placeholder="Ej. 50mg cada 24h" required>
                    </div>

                    <div class="field-group">
                        <label>Duración del Tratamiento</label>
                        <input type="text" name="duracion" placeholder="Ej. 30 días" required>
                    </div>

                    <div class="field-group">
                        <label>Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Emitir Receta</button>
                </div>
            </form>

            <!-- ================= LISTA ================= -->
            <div class="list-card">
                <div class="list-toolbar">
                    <h2 class="form-title">Recetas Emitidas</h2>
                </div>

                <table class="tabla-recetas">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Paciente</th>
                            <th>Medicamento</th>
                            <th>Dosis</th>
                            <th>Duración</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $stmtRecetas->fetch(PDO::FETCH_ASSOC)) : ?>
                            <tr>
                                <td><?php echo $r['id_receta']; ?></td>
                                <td><?php echo ($r['nombre'] ?? 'N/A') . " " . ($r['apellido'] ?? ''); ?></td>
                                <td><?php echo $r['medicamento'] ?? 'N/A'; ?></td>
                                <td><?php echo $r['dosis'] ?? 'N/A'; ?></td>
                                <td><?php echo $r['duracion'] ?? 'N/A'; ?></td>
                                <td>
                                    <?php 
                                        echo $r['fecha_emision'] 
                                            ? date('d/m/Y', strtotime($r['fecha_emision'])) 
                                            : 'N/A'; 
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="js/header.js"></script>
    <script src="js/sidebar.js"></script>
</body>
</html>
</html>
