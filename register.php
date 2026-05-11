<?php
require_once 'BD/conectar.php';
$db = Conexion::ConexionBD();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $correo = $_POST['correo'];
    $password = password_hash(
        $_POST['password'],
        PASSWORD_DEFAULT
    );

    $sql = "INSERT INTO usuarios (usuario, correo, password, rol)
            VALUES (:usuario, :correo, :password, 'usuario')";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':usuario'  => $usuario,
        ':correo'   => $correo,
        ':password' => $password
    ]);

    $msg = "Usuario creado correctamente";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <main>
        <div class="card">
            <div class="card-header">
                <h1>Crear Usuario</h1>
                <p>DocManager</p>
            </div>

            <div class="card-body">
                <?php if ($msg != "") { ?>
                    <div class="error-msg" style="display:block; background:#effff4; border-color:#9ae6b4; color:#1f7a45;">
                        <?php echo $msg; ?>
                    </div>
                <?php } ?>

                <form method="POST">
                    <!-- Campo Usuario -->
                    <div class="field">
                        <label>Usuario</label>
                        <div class="input-wrap">
                            <input type="text" name="usuario" placeholder="Nombre de usuario" required>
                            <!-- Icono de usuario para que el padding 40px no se vea vacío -->
                            <svg viewBox="0 0 24 24" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; fill: none; stroke: #888; stroke-width: 2;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Campo Correo -->
                    <div class="field">
                        <label>Correo</label>
                        <div class="input-wrap" style="position: relative;">
                            <input type="email" name="correo" placeholder="correo@ejemplo.com" required>
                            <svg viewBox="0 0 24 24" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; fill: none; stroke: #888; stroke-width: 2;">
                                <rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3,7 12,13 21,7"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="field">
                        <label>Contraseña</label>
                        <div class="input-wrap" style="position: relative;">
                            <input type="password" name="password" id="password" placeholder="••••••••" required>
                            <!-- Icono de candado -->
                            <svg viewBox="0 0 24 24" style="position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; fill: none; stroke: #888; stroke-width: 2;">
                                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <span class="toggle-pw" onclick="togglePassword()" style="position: absolute; right: 13px; top: 50%; transform: translateY(-50%); cursor: pointer;">👁</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Crear Usuario
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