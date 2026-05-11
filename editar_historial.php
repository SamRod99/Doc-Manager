<?php
date_default_timezone_set('America/Mexico_City');
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();

$id = $_GET['id'] ?? 0;

/* ================= GUARDAR INFORMACIÓN ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "UPDATE paciente_info_medica 
            SET tipo_sangre = :tipo_sangre, 
                peso = :peso, 
                altura = :altura, 
                alergias = :alergias, 
                enfermedades_cronicas = :cronicas, 
                antecedentes_familiares = :antecedentes, 
                fuma = :fuma, 
                consume_alcohol = :alcohol, 
                actividad_fisica = :actividad, 
                cirugias_previas = :cirugias, 
                medicamentos_actuales = :medicamentos, 
                notas = :notas 
            WHERE id_paciente = :id";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':tipo_sangre'  => $_POST['tipo_sangre'],
        ':peso'         => $_POST['peso'],
        ':altura'       => $_POST['altura'],
        ':alergias'     => $_POST['alergias'],
        ':cronicas'     => $_POST['cronicas'],
        ':antecedentes' => $_POST['antecedentes'],
        ':fuma'         => $_POST['fuma'],
        ':alcohol'      => $_POST['alcohol'],
        ':actividad'    => $_POST['actividad'],
        ':cirugias'     => $_POST['cirugias'],
        ':medicamentos' => $_POST['medicamentos'],
        ':notas'        => $_POST['notas'],
        ':id'           => $id
    ]);

    header("Location: pacientes.php");
    exit;
}

/* ================= OBTENER INFORMACIÓN DEL PACIENTE ================= */
$sql = "SELECT p.nombre, p.apellido, pim.* 
        FROM pacientes p 
        LEFT JOIN paciente_info_medica pim ON p.id_paciente = pim.id_paciente 
        WHERE p.id_paciente = :id";

$stmt = $db->prepare($sql);
$stmt->execute([':id' => $id]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Historial - <?php echo $info['nombre']; ?></title>

    <link rel="stylesheet" href="css/globales.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/pacientes.css">
</head>

<body data-title="Editar Historial">

    <aside id="sidebar-container"></aside>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <form class="form-card" method="POST">
                <h2 class="form-title">
                    Historial de: <?php echo $info['nombre'] . " " . $info['apellido']; ?>
                </h2>

                <div class="form-grid">
                    <!-- Datos Físicos -->
                    <div class="field-group">
                        <label>Tipo Sangre</label>
                        <input type="text" name="tipo_sangre" value="<?php echo $info['tipo_sangre']; ?>">
                    </div>

                    <div class="field-group">
                        <label>Peso (kg)</label>
                        <input type="number" step="0.1" name="peso" value="<?php echo $info['peso']; ?>">
                    </div>

                    <div class="field-group">
                        <label>Altura (m)</label>
                        <input type="number" step="0.01" name="altura" value="<?php echo $info['altura']; ?>">
                    </div>

                    <div class="field-group">
                        <label>Alergias</label>
                        <input type="text" name="alergias" value="<?php echo $info['alergias']; ?>">
                    </div>

                    <!-- Condiciones Médicas -->
                    <div class="field-group">
                        <label>Enfermedades Crónicas</label>
                        <input type="text" name="cronicas" value="<?php echo $info['enfermedades_cronicas']; ?>">
                    </div>

                    <div class="field-group">
                        <label>Antecedentes Familiares</label>
                        <input type="text" name="antecedentes" value="<?php echo $info['antecedentes_familiares']; ?>">
                    </div>

                    <!-- Estilo de Vida -->
                    <div class="field-group">
                        <label>¿Fuma?</label>
                        <select name="fuma">
                            <option value="1" <?php if ($info['fuma']) echo 'selected'; ?>>Sí</option>
                            <option value="0" <?php if (!$info['fuma']) echo 'selected'; ?>>No</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label>¿Consume Alcohol?</label>
                        <select name="alcohol">
                            <option value="1" <?php if ($info['consume_alcohol']) echo 'selected'; ?>>Sí</option>
                            <option value="0" <?php if (!$info['consume_alcohol']) echo 'selected'; ?>>No</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Actividad Física</label>
                        <input type="text" name="actividad" value="<?php echo $info['actividad_fisica']; ?>">
                    </div>

                    <div class="field-group">
                        <label>Cirugías Previas</label>
                        <input type="text" name="cirugias" value="<?php echo $info['cirugias_previas']; ?>">
                    </div>

                    <div class="field-group">
                        <label>Medicamentos Actuales</label>
                        <input type="text" name="medicamentos" value="<?php echo $info['medicamentos_actuales']; ?>">
                    </div>

                    <!-- Notas Adicionales -->
                    <div class="field-group">
                        <label>Notas del Especialista</label>
                        <textarea name="notas" style="height:120px; padding:14px;"><?php echo trim($info['notas']); ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Guardar Historial</button>
                </div>
            </form>
        </main>
    </div>

    <script src="js/sidebar.js"></script>
    <script src="js/header.js"></script>

</body>
</html>