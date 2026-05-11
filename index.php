<?php

session_start();

require_once 'BD/conectar.php';

$db = Conexion::ConexionBD();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $sql = "

    SELECT *

    FROM usuarios

    WHERE usuario = :usuario
    AND activo = TRUE

    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':usuario' => $usuario
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        if (password_verify($password, $user['password'])) {

            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['rol'];

            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Contraseña incorrecta";
        }

    } else {

        $error = "Usuario no encontrado";
    }
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>

        DocManager – Inicio de Sesión

    </title>

    <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600&display=swap"
    rel="stylesheet">

    <link
    rel="stylesheet"
    href="css/index.css">

</head>

<body>

    <nav>

        <div class="brand">

            <div>

                <span class="brand-name">

                    DocManager

                </span>

                <span class="brand-sub">

                    Consultorio Médico

                </span>

            </div>

        </div>

    </nav>

    <main>

        <div class="card">

            <div class="card-header">

                <div class="card-icon">

                    <svg viewBox="0 0 24 24">

                        <path d="M12 12c2.67 0 8 1.34 8 4v2H4v-2c0-2.66 5.33-4 8-4z"/>

                        <circle cx="12" cy="7" r="4"/>

                    </svg>

                </div>

                <h1>

                    DocManager

                </h1>

                <p>

                    Sistema de gestión médica

                </p>

            </div>

            <div class="card-body">

                <?php if ($error != "") { ?>

                    <div
                    class="error-msg"
                    style="display:block;">

                        ⚠ <?php echo $error; ?>

                    </div>

                <?php } ?>

                <form method="POST">

                    <div class="field">

                        <label for="usuario">

                            Usuario

                        </label>

                        <div class="input-wrap">

                            <input
                            type="text"
                            name="usuario"
                            id="usuario"
                            placeholder="Ingresa un usuario"
                            required>

                            <svg viewBox="0 0 24 24">

                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>

                                <circle cx="12" cy="7" r="4"/>

                            </svg>

                        </div>

                    </div>

                    <div class="field">

                        <label for="password">

                            Contraseña

                        </label>

                        <div class="input-wrap">

                            <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            required>

                            <svg viewBox="0 0 24 24">

                                <rect
                                x="3"
                                y="11"
                                width="18"
                                height="11"
                                rx="2"/>

                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>

                            </svg>

                            <span
                            class="toggle-pw"
                            onclick="togglePassword()">

                                👁

                            </span>

                        </div>

                    </div>

                    <div class="row">

                        <label class="checkbox-label">

                            <input type="checkbox">

                            <span class="custom-check">

                                <svg viewBox="0 0 12 12">

                                    <polyline points="2,6 5,9 10,3"/>

                                </svg>

                            </span>

                            Recordarme

                        </label>

                        <a
                        href="recuperar.php"
                        class="forgot">

                            ¿Olvidaste tu contraseña?

                        </a>

                    </div>

                    <button
                    type="submit"
                    class="btn-login">

                        Iniciar sesión

                    </button>

                </form>

                <div
                style="
                text-align:center;
                margin-top:18px;
                ">

                    <a
                    href="register.php"
                    style="
                    color:var(--teal);
                    text-decoration:none;
                    font-size:0.9rem;
                    font-weight:600;
                    ">

                        Crear usuario

                    </a>

                </div>

            </div>

            <div class="card-footer">

                <span>

                    © 2025 DocManager · Todos los derechos reservados

                </span>

            </div>

        </div>

    </main>

    <script>

    function togglePassword(){

        const input =
        document.getElementById("password");

        if(input.type === "password"){

            input.type = "text";

        }else{

            input.type = "password";
        }
    }

    </script>

</body>

</html>
