<?php
session_start();

date_default_timezone_set('America/Mexico_City');

require_once 'BD/conectar.php';

$db = Conexion::ConexionBD();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$busqueda = $_GET['buscar'] ?? '';

/* ================= CRUD ================= */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];

    /* ================= GUARDAR ================= */
    if ($accion == "guardar") {
        $sql = "INSERT INTO pacientes (
                    nombre, 
                    apellido, 
                    fecha_nacimiento, 
                    telefono, 
                    direccion, 
                    id_usuario
                ) VALUES (
                    :nombre, 
                    :apellido, 
                    :fecha, 
                    :telefono, 
                    :direccion, 
                    :id_usuario
                )";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $_POST['nombre'],
            ':apellido'    => $_POST['apellido'],
            ':fecha'       => $_POST['fecha_nacimiento'],
            ':telefono'    => $_POST['telefono'],
            ':direccion'   => $_POST['direccion'],
            ':id_usuario'  => $id_usuario
        ]);
    }

    /* ================= EDITAR ================= */
    elseif ($accion == "editar") {
        $sql = "UPDATE pacientes 
                SET nombre = :nombre, 
                    apellido = :apellido, 
                    fecha_nacimiento = :fecha, 
                    telefono = :telefono, 
                    direccion = :direccion 
                WHERE id_paciente = :id 
                AND id_usuario = :id_usuario";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $_POST['nombre'],
            ':apellido'    => $_POST['apellido'],
            ':fecha'       => $_POST['fecha_nacimiento'],
            ':telefono'    => $_POST['telefono'],
            ':direccion'   => $_POST['direccion'],
            ':id'          => $_POST['id'],
            ':id_usuario'  => $id_usuario
        ]);
    }

    /* ================= ELIMINAR ================= */
    elseif ($accion == "eliminar") {
        $sql = "UPDATE pacientes 
                SET eliminado = 1 
                WHERE id_paciente = :id 
                AND id_usuario = :id_usuario";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id'         => $_POST['id'],
            ':id_usuario' => $id_usuario
        ]);
    }

    header("Location: pacientes.php");
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
    <link rel="stylesheet" href="css/pacientes.css">
    <title>DocManager - Pacientes</title>
</head>
<body data-title="Pacientes">

    <aside id="sidebar-container"></aside>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <!-- ================= LISTA ================= -->
            <div id="vista-lista" class="list-card">
                <div class="list-toolbar">
                    <form method="GET" class="search-wrapper">
                        <input type="search" name="buscar" placeholder="Buscar paciente..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    </form>
                    <button class="btn-nuevo" onclick="nuevo()">Nuevo</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Edad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM pacientes 
                                WHERE id_usuario = :id_usuario 
                                AND COALESCE(eliminado,0) = 0 
                                AND (nombre ILIKE :buscar OR apellido ILIKE :buscar) 
                                ORDER BY id_paciente ASC";

                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            ':buscar'     => "%$busqueda%",
                            ':id_usuario' => $id_usuario
                        ]);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $fechaNacimiento = new DateTime($row['fecha_nacimiento']);
                            $hoy = new DateTime();
                            $edad = $hoy->diff($fechaNacimiento)->y;
                        ?>
                            <tr>
                                <td><?php echo $row['id_paciente']; ?></td>
                                <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($row['apellido']); ?></td>
                                <td><?php echo $edad; ?></td>
                                <td>
                                    <div class="acciones">
                                        <button class="btn-ver" onclick='editar(<?php echo json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                            Editar
                                        </button>

                                        <button class="btn-historial" onclick="window.location.href='editar_historial.php?id=<?php echo $row['id_paciente']; ?>'">
                                            Historial
                                        </button>

                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?php echo $row['id_paciente']; ?>">
                                            <button type="submit" class="btn-eliminar" onclick="return confirm('¿Eliminar paciente?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- ================= FORM ================= -->
            <form id="vista-form" class="form-card" method="POST" style="display:none;">
                <div class="form-header">
                    <button type="button" class="btn-volver" onclick="volver()">Volver</button>
                    <h2 class="form-title">Paciente</h2>
                </div>

                <input type="hidden" name="id" id="id">
                <input type="hidden" name="accion" id="accion" value="guardar">

                <div class="form-grid">
                    <div class="field-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="nombre" required>
                    </div>

                    <div class="field-group">
                        <label>Apellido</label>
                        <input type="text" name="apellido" id="apellido" required>
                    </div>

                    <div class="field-group">
                        <label>Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha" required>
                    </div>

                    <div class="field-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono">
                    </div>

                    <div class="field-group">
                        <label>Dirección</label>
                        <input type="text" name="direccion" id="direccion">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-guardar">Guardar</button>
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
            
            document.getElementById("accion").value = "guardar";
            document.getElementById("id").value = "";
            document.getElementById("nombre").value = "";
            document.getElementById("apellido").value = "";
            document.getElementById("fecha").value = "";
            document.getElementById("telefono").value = "";
            document.getElementById("direccion").value = "";
        }

        function editar(p) {
            document.getElementById("vista-lista").style.display = "none";
            document.getElementById("vista-form").style.display = "block";
            
            document.getElementById("accion").value = "editar";
            document.getElementById("id").value = p.id_paciente;
            document.getElementById("nombre").value = p.nombre;
            document.getElementById("apellido").value = p.apellido;
            document.getElementById("fecha").value = p.fecha_nacimiento;
            document.getElementById("telefono").value = p.telefono ?? "";
            document.getElementById("direccion").value = p.direccion ?? "";
        }

        function volver() {
            location.reload();
        }
    </script>
</body>
</html>
