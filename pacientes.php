<?php
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();

$busqueda = $_GET['buscar'] ?? '';

/* ================= CRUD ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $accion = $_POST['accion'];

    if ($accion == "guardar") {

        $sql = "INSERT INTO pacientes (nombre, apellido, fecha_nacimiento, telefono, direccion)
                VALUES (:nombre, :apellido, :fecha, :telefono, :direccion)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre' => $_POST['nombre'],
            ':apellido' => $_POST['apellido'],
            ':fecha' => $_POST['fecha_nacimiento'],
            ':telefono' => $_POST['telefono'],
            ':direccion' => $_POST['direccion']
        ]);

    } elseif ($accion == "editar") {

        $sql = "UPDATE pacientes 
                SET nombre=:nombre, apellido=:apellido, fecha_nacimiento=:fecha,
                    telefono=:telefono, direccion=:direccion
                WHERE id_paciente=:id";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nombre' => $_POST['nombre'],
            ':apellido' => $_POST['apellido'],
            ':fecha' => $_POST['fecha_nacimiento'],
            ':telefono' => $_POST['telefono'],
            ':direccion' => $_POST['direccion'],
            ':id' => $_POST['id']
        ]);

    } elseif ($accion == "eliminar") {

        $sql = "UPDATE pacientes SET eliminado = 1 WHERE id_paciente = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $_POST['id']]);
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
<input type="search" name="buscar" placeholder="Buscar paciente..." value="<?php echo $busqueda; ?>">
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
<th>Tipo de sangre</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>
<?php
$sql = "
SELECT 
    p.id_paciente,
    p.nombre,
    p.apellido,
    p.fecha_nacimiento,
    p.telefono,
    p.direccion,
    i.tipo_sangre
FROM pacientes p
LEFT JOIN paciente_info_medica i 
ON p.id_paciente = i.id_paciente
WHERE (p.nombre ILIKE :buscar OR p.apellido ILIKE :buscar)
AND COALESCE(p.eliminado,0)=0
ORDER BY p.id_paciente ASC
";

$stmt = $db->prepare($sql);
$stmt->execute([':buscar' => "%$busqueda%"]);

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // edad correcta
    $fechaNacimiento = new DateTime($row['fecha_nacimiento']);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y;
?>
<tr>
<td><?php echo $row['id_paciente']; ?></td>
<td><?php echo $row['nombre']; ?></td>
<td><?php echo $row['apellido']; ?></td>
<td><?php echo $edad; ?></td>
<td><?php echo $row['tipo_sangre'] ?? 'N/A'; ?></td>
<td>
<button class="btn-ver" onclick='editar(<?php echo json_encode($row); ?>)'>Editar</button>
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
<button type="submit">Guardar</button>
<button type="button" onclick="eliminar()">Eliminar</button>
</div>

</form>

</main>
</div>

<script src="js/sidebar.js"></script>
<script src="js/header.js"></script>

<script>
function nuevo(){
document.getElementById("vista-lista").style.display="none";
document.getElementById("vista-form").style.display="block";
document.getElementById("accion").value="guardar";
}

function editar(p){
document.getElementById("vista-lista").style.display="none";
document.getElementById("vista-form").style.display="block";

document.getElementById("accion").value="editar";

document.getElementById("id").value = p.id_paciente;
document.getElementById("nombre").value = p.nombre;
document.getElementById("apellido").value = p.apellido;
document.getElementById("fecha").value = p.fecha_nacimiento;
document.getElementById("telefono").value = p.telefono ?? "";
document.getElementById("direccion").value = p.direccion ?? "";
}

function eliminar(){
if(confirm("¿Eliminar paciente?")){
document.getElementById("accion").value="eliminar";
document.getElementById("vista-form").submit();
}
}

function volver(){
location.reload();
}
</script>

</body>
</html>
