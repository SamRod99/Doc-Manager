<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>DocManager – Dashboard</title>

  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/dashboard.css"/>
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/globales.css">
</head>

<body>

<?php
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();
?>

<aside id="sidebar-container"></aside>

<div class="layout">

  <div id="header-container"></div>

  <main>

    <!-- ================= STATS ================= -->
    <div class="stats-grid">

      <!-- Pacientes -->
      <div class="stat-card">
        <div class="stat-header teal-dark">Pacientes</div>
        <div class="stat-body">
          <div>
            <?php
            $res = $db->query("SELECT COUNT(*) FROM pacientes");
            $totalPacientes = $res->fetchColumn();
            ?>
            <div class="stat-value"><?php echo $totalPacientes; ?></div>
          </div>
        </div>
      </div>

      <!-- Citas hoy -->
      <div class="stat-card">
        <div class="stat-header teal-mint">Citas Hoy</div>
        <div class="stat-body">
          <div>
            <?php
            $res = $db->query("SELECT COUNT(*) FROM citas WHERE DATE(inicio)=CURRENT_DATE");
            $citasHoy = $res->fetchColumn();
            ?>
            <div class="stat-value"><?php echo $citasHoy; ?></div>
          </div>
        </div>
      </div>

      <!-- Médicos -->
      <div class="stat-card">
        <div class="stat-header dark">Médicos</div>
        <div class="stat-body">
          <div>
            <?php
            $res = $db->query("SELECT COUNT(*) FROM medicos");
            $totalMedicos = $res->fetchColumn();
            ?>
            <div class="stat-value"><?php echo $totalMedicos; ?></div>
          </div>
        </div>
      </div>

      <!-- Ingresos -->
      <div class="stat-card">
        <div class="stat-header orange">Ingresos</div>
        <div class="stat-body">
          <div>
            <?php
            $res = $db->query("SELECT COALESCE(SUM(total),0) FROM pagos");
            $ingresos = $res->fetchColumn();
            ?>
            <div class="stat-value">$<?php echo $ingresos; ?></div>
          </div>
        </div>
      </div>

    </div>

    <!-- ================= CONTENIDO ================= -->
    <div class="bottom-row">

      <!-- ================= TABLA CITAS ================= -->
      <div class="panel">
        <div class="panel-head">
          <h2>Citas Recientes</h2>
        </div>

        <table>
          <thead>
            <tr>
              <th>Paciente</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Estado</th>
            </tr>
          </thead>

          <tbody>
          <?php
          $sql = "
          SELECT 
              p.nombre,
              p.apellido,
              c.inicio,
              c.estado
          FROM citas c
          JOIN pacientes p ON p.id_paciente = c.id_paciente
          ORDER BY c.inicio DESC
          LIMIT 10
          ";

          $stmt = $db->prepare($sql);
          $stmt->execute();

          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

              $fecha = date("d/m/Y", strtotime($row['inicio']));
              $hora = date("H:i", strtotime($row['inicio']));
          ?>
          <tr>
            <td><?php echo $row['nombre']." ".$row['apellido']; ?></td>
            <td><?php echo $fecha; ?></td>
            <td><?php echo $hora; ?></td>
            <td>
              <?php
              switch ($row['estado']) {
                case "atendida":
                  echo '<span class="status-pill pill-atendida">Atendida</span>';
                  break;
                case "pendiente":
                  echo '<span class="status-pill pill-pendiente">Pendiente</span>';
                  break;
                case "cancelada":
                  echo '<span class="status-pill pill-cancelada">Cancelada</span>';
                  break;
                case "no_asistio":
                  echo '<span class="status-pill pill-noasistio">No asistió</span>';
                  break;
                default:
                  echo '<span class="status-pill">Sin estado</span>';
              }
              ?>
            </td>
          </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>

      <!-- ================= AGENDA ================= -->
      <div class="mini-panel">

        <div class="agenda-card">

          <div class="agenda-head">
            <h2>Agenda de Hoy</h2>
            <span style="font-size:0.75rem;color:var(--text-muted)">
              <?php echo date("d M Y"); ?>
            </span>
          </div>

          <?php
          $sqlAgenda = "
          SELECT 
              c.inicio,
              c.estado,
              c.motivo,
              p.nombre,
              p.apellido,
              m.nombre AS medico_nombre,
              m.apellido AS medico_apellido
          FROM citas c
          JOIN pacientes p ON c.id_paciente = p.id_paciente
          JOIN medicos m ON c.id_medico = m.id_medico
          WHERE DATE(c.inicio)=CURRENT_DATE
          ORDER BY c.inicio ASC
          ";

          $stmtAgenda = $db->prepare($sqlAgenda);
          $stmtAgenda->execute();

          while($row = $stmtAgenda->fetch(PDO::FETCH_ASSOC)) {

              $hora = date("H:i", strtotime($row['inicio']));

              switch ($row['estado']) {
                case "atendida": $color="#1a9975"; break;
                case "pendiente": $color="#e8a020"; break;
                case "cancelada": $color="#d9534f"; break;
                default: $color="#999";
              }
          ?>
          <div class="agenda-item">
            <span class="agenda-time"><?php echo $hora; ?></span>
            <span class="agenda-dot" style="background:<?php echo $color; ?>"></span>
            <div class="agenda-info">
              <strong><?php echo $row['nombre']." ".$row['apellido']; ?></strong>
              <small>
                Dr. <?php echo $row['medico_nombre']." ".$row['medico_apellido']; ?>
                · <?php echo $row['motivo']; ?>
              </small>
            </div>
          </div>
          <?php } ?>

        </div>

      </div>

    </div>

  </main>
</div>

<script src="js/sidebar.js"></script>
<script src="js/header.js"></script>

</body>
</html>
