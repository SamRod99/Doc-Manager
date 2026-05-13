<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();

/* ================= GUARDAR ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];

    if ($accion == "guardar") {
        $inicio = $_POST['fecha'] . " " . $_POST['hora'] . ":00";
        $fin = date('Y-m-d H:i:s', strtotime($inicio . ' +1 hour'));

        /* Médico fijo temporal o según lógica de negocio */
        $id_medico = 1;

        $sql = "INSERT INTO citas (id_paciente, id_medico, inicio, fin, motivo, estado, id_usuario)
                VALUES (:paciente, :medico, :inicio, :fin, :motivo, :estado, :id_usuario)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':paciente'   => $_POST['paciente'],
            ':medico'     => $id_medico,
            ':inicio'     => $inicio,
            ':fin'        => $fin,
            ':motivo'     => $_POST['motivo'],
            ':estado' => 'pendiente',
            ':id_usuario' => $id_usuario
        ]);
    }

    header("Location: citas.php");
    exit;
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
    <link rel="stylesheet" href="css/citas.css">
    <link rel="stylesheet" href="css/pacientes.css">
    <title>DocManager - Citas</title>
</head>

<body data-title="Citas">
    <aside id="sidebar-container"></aside>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <!-- ================= LISTA ================= -->
            <div class="list-card" id="vista-lista">
                <div class="list-toolbar">
                    <div class="tabs">
    <button class="tab active">Todas</button>
    <button class="tab">Pendientes</button>
    <button class="tab">Atendidas</button>
    <button class="tab">Canceladas</button>
</div>
                    <button class="btn-nuevo" onclick="nuevo()">Nueva cita</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Paciente</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT c.id_cita, c.inicio, c.motivo, c.estado, p.nombre, p.apellido
                                FROM citas c
                                JOIN pacientes p ON p.id_paciente = c.id_paciente
                                WHERE COALESCE(p.eliminado,0) = 0 AND c.id_usuario = :id_usuario
                                ORDER BY c.inicio ASC";

                        $stmt = $db->prepare($sql);
                        $stmt->execute([':id_usuario' => $id_usuario]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) :
                        ?>
                        <tr>
                            <td><?php echo $row['id_cita']; ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['inicio'])); ?></td>
                            <td><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                            <td><?php echo $row['motivo']; ?></td>
                            <td><?php echo ucfirst($row['estado']); ?></td>
                            <td><button class="btn-ver">Ver</button></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- ================= FORMULARIO ================= -->
            <form id="vista-form" class="form-card" method="POST" style="display:none;">
                <div class="form-header">
                    <button type="button" class="btn-volver" onclick="volver()">Volver</button>
                    <h2 class="form-title">Nueva Cita</h2>
                </div>

                <input type="hidden" name="accion" value="guardar">

                <div class="form-col">
                    <div class="field-group">
                        <label>Paciente</label>
                        <select name="paciente" required>
                            <option value="">Seleccione paciente</option>
                            <?php
                            $sqlPacientes = "SELECT id_paciente, nombre, apellido FROM pacientes 
                                             WHERE COALESCE(eliminado,0) = 0 AND id_usuario = :id_usuario 
                                             ORDER BY nombre";
                            $stmtPac = $db->prepare($sqlPacientes);
                            $stmtPac->execute([':id_usuario' => $id_usuario]);

                            while ($pac = $stmtPac->fetch(PDO::FETCH_ASSOC)) :
                            ?>
                                <option value="<?php echo $pac['id_paciente']; ?>">
                                    <?php echo $pac['nombre'] . " " . $pac['apellido']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-grid-2">
                        <div class="field-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" required>
                        </div>
                        <div class="field-group">
                            <label>Hora</label>
                            <input type="time" name="hora" required>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Motivo de consulta</label>
                        <input type="text" name="motivo" placeholder="Describa el motivo" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Confirmar Cita</button>
                </div>
            </form>
        </main>
    </div>

    <script src="js/sidebar.js"></script>
    <script src="js/header.js"></script>
    <script>
        function nuevo() {
            document.getElementById("vista-lista").style.display = "none";
            document.getElementById("vista-form").style.display = "block";
        }

        function volver() {
            location.reload();
        }
    </script>
</body>
</html>
