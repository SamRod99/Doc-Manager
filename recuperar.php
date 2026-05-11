<?php
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET password = :password WHERE usuario = :usuario";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':password' => $password,
        ':usuario'  => $usuario
    ]);

    $msg = "Contraseña actualizada";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h1>Recuperar Contraseña</h1>
                <p>DocManager</p>
            </div>

            <div class="card-body">
                <?php if ($msg != "") : ?>
                    <div class="error-msg" style="display:block; background:#effff4; border-color:#9ae6b4; color:#1f7a45;">
                        <?php echo $msg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="field">
                        <label>Usuario</label>
                        <div class="input-wrap">
                            <input type="text" name="usuario" required>
                        </div>
                    </div>

                    <div class="field">
                        <label>Nueva Contraseña</label>
                        <div class="input-wrap">
                            <input type="password" name="password" id="password" required>
                            <span class="toggle-pw" onclick="togglePassword()" style="cursor:pointer;">👁</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Actualizar Contraseña
                    </button>
                </form>

                <div style="text-align:center; margin-top:18px;">
                    <a href="index.php" class="forgot">Volver al login</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            input.type = (input.type === "password") ? "text" : "password";
        }
    </script>
</body>
</html>