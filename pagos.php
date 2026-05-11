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

/* ================= REGISTRAR PAGO ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "INSERT INTO pagos (id_cita, subtotal, iva, total, metodo_pago, estado_pago, id_usuario)
            VALUES (:cita, :subtotal, 0, :total, :metodo, 'pagado', :id_usuario)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':cita'       => $_POST['cita'],
        ':subtotal'   => $_POST['subtotal'],
        ':total'      => $_POST['total'],
        ':metodo'     => $_POST['metodo'],
        ':id_usuario' => $id_usuario
    ]);

    header("Location: pagos.php");
    exit;
}

/* ================= RESUMEN ================= */
$totalIngresos = $db->prepare("SELECT COALESCE(SUM(total),0) FROM pagos WHERE id_usuario = :id_usuario");
$totalIngresos->execute([':id_usuario' => $id_usuario]);
$totalIngresos = $totalIngresos->fetchColumn();

/* ================= EFECTIVO ================= */
$totalEfectivo = $db->prepare("SELECT COALESCE(SUM(total),0) FROM pagos WHERE metodo_pago = 'Efectivo' AND id_usuario = :id_usuario");
$totalEfectivo->execute([':id_usuario' => $id_usuario]);
$totalEfectivo = $totalEfectivo->fetchColumn();

/* ================= TARJETA ================= */
$totalTarjeta = $db->prepare("SELECT COALESCE(SUM(total),0) FROM pagos WHERE metodo_pago = 'Tarjeta' AND id_usuario = :id_usuario");
$totalTarjeta->execute([':id_usuario' => $id_usuario]);
$totalTarjeta = $totalTarjeta->fetchColumn();

/* ================= TRANSFERENCIA ================= */
$totalTransferencia = $db->prepare("SELECT COALESCE(SUM(total),0) FROM pagos WHERE metodo_pago = 'Transferencia' AND id_usuario = :id_usuario");
$totalTransferencia->execute([':id_usuario' => $id_usuario]);
$totalTransferencia = $totalTransferencia->fetchColumn();

/* ================= TABLA PAGOS ================= */
$sqlPagos = "SELECT pg.id_pago, pg.id_cita, pg.subtotal, pg.total, pg.metodo_pago, p.nombre, p.apellido
             FROM pagos pg
             JOIN citas c ON pg.id_cita = c.id_cita
             JOIN pacientes p ON c.id_paciente = p.id_paciente
             WHERE pg.id_usuario = :id_usuario
             ORDER BY pg.id_pago DESC";

$stmtPagos = $db->prepare($sqlPagos);
$stmtPagos->execute([':id_usuario' => $id_usuario]);

/* ================= CITAS ================= */
$sqlCitas = "SELECT c.id_cita, p.nombre, p.apellido
             FROM citas c
             JOIN pacientes p ON c.id_paciente = p.id_paciente
             WHERE c.id_usuario = :id_usuario
             ORDER BY c.id_cita DESC";

$stmtCitas = $db->prepare($sqlCitas);
$stmtCitas->execute([':id_usuario' => $id_usuario]);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/globales.css">
    <link rel="stylesheet" href="css/pagos.css">
    <title>DocManager - Pagos</title>
</head>
<body data-title="Pagos">
    <div id="sidebar-container"></div>

    <div class="layout">
        <div id="header-container"></div>

        <main>
            <!-- ================= REPORTE ================= -->
            <div id="vista-reporte" class="list-card">
                <div class="list-toolbar">
                    <h2 class="form-title">Reporte de Ingresos</h2>
                    <button class="btn-nuevo" id="btn-abrir-form">+ Registrar Pago</button>
                </div>

                <!-- ================= CARDS ================= -->
                <div class="pagos-grid">
                    <div class="pago-card" style="background: var(--teal-mint);">
                        <span>Total Ingresos</span>
                        <strong>$<?php echo number_format($totalIngresos, 2); ?></strong>
                    </div>
                    <div class="pago-card" style="background: var(--teal-dark);">
                        <span>Efectivo</span>
                        <strong>$<?php echo number_format($totalEfectivo, 2); ?></strong>
                    </div>
                    <div class="pago-card" style="background:#1a2628;">
                        <span>Tarjeta</span>
                        <strong>$<?php echo number_format($totalTarjeta, 2); ?></strong>
                    </div>
                    <div class="pago-card" style="background: var(--orange);">
                        <span>Transferencia</span>
                        <strong>$<?php echo number_format($totalTransferencia, 2); ?></strong>
                    </div>
                </div>

                <!-- ================= TABLA ================= -->
                <table>
                    <thead>
                        <tr>
                            <th>ID Pago</th>
                            <th>Paciente</th>
                            <th>Cita</th>
                            <th>Método</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $stmtPagos->fetch(PDO::FETCH_ASSOC)) : ?>
                            <tr>
                                <td><?php echo $row['id_pago']; ?></td>
                                <td><?php echo $row['nombre'] . " " . $row['apellido']; ?></td>
                                <td>#<?php echo $row['id_cita']; ?></td>
                                <td><?php echo $row['metodo_pago']; ?></td>
                                <td>$<?php echo number_format($row['subtotal'], 2); ?></td>
                                <td>$<?php echo number_format($row['total'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- ================= FORM ================= -->
            <form id="vista-form" class="form-card" method="POST" style="display:none;">
                <div class="form-header">
                    <button type="button" class="btn-volver" id="btn-volver">
                        <svg viewBox="0 0 24 24">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        Volver
                    </button>
                    <h2 class="form-title">Registrar Pago</h2>
                </div>

                <div class="form-col">
                    <!-- ================= METODO ================= -->
                    <div class="field-group">
                        <label>Método de Pago</label>
                        <div class="metodo-tabs">
                            <button type="button" class="metodo-tab" data-metodo="Efectivo">Efectivo</button>
                            <button type="button" class="metodo-tab active" data-metodo="Tarjeta">Tarjeta</button>
                            <button type="button" class="metodo-tab" data-metodo="Transferencia">Transferencia</button>
                        </div>
                        <input type="hidden" name="metodo" id="metodo" value="Tarjeta">
                    </div>

                    <!-- ================= CITA ================= -->
                    <div class="field-group">
                        <label for="cita">Cita vinculada</label>
                        <select name="cita" id="cita" required>
                            <option value="">Seleccionar cita...</option>
                            <?php while ($cita = $stmtCitas->fetch(PDO::FETCH_ASSOC)) : ?>
                                <option value="<?php echo $cita['id_cita']; ?>">
                                    <?php echo "Cita #" . $cita['id_cita'] . " — " . $cita['nombre'] . " " . $cita['apellido']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- ================= SUBTOTAL ================= -->
                    <div class="field-group">
                        <label for="subtotal">Subtotal</label>
                        <input type="number" step="0.01" name="subtotal" id="subtotal" placeholder="$0.00" required>
                    </div>

                    <!-- ================= TOTAL ================= -->
                    <div class="field-group">
                        <label for="total">Total</label>
                        <input type="number" step="0.01" name="total" id="total" placeholder="$0.00" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Registrar Pago</button>
                </div>
            </form>
        </main>
    </div>

    <script src="js/sidebar.js"></script>
    <script src="js/header.js"></script>
    <script>
        const btnAbrir = document.getElementById("btn-abrir-form");
        const btnVolver = document.getElementById("btn-volver");
        const vistaReporte = document.getElementById("vista-reporte");
        const vistaForm = document.getElementById("vista-form");

        btnAbrir.addEventListener("click", () => {
            vistaReporte.style.display = "none";
            vistaForm.style.display = "block";
        });

        btnVolver.addEventListener("click", () => {
            vistaReporte.style.display = "block";
            vistaForm.style.display = "none";
        });

        /* ================= TABS ================= */
        const tabs = document.querySelectorAll(".metodo-tab");
        const metodoInput = document.getElementById("metodo");

        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                tabs.forEach(t => t.classList.remove("active"));
                tab.classList.add("active");
                metodoInput.value = tab.dataset.metodo;
            });
        });
    </script>
</body>
</html>
