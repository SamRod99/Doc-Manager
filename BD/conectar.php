<?php
class Conexion {
    public static function ConexionBD() {
        $host = "localhost";
        $dbname = "DocManager";
        $username = "postgres";
        $password = "Angelcam78"; 

        try {
            $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $exp) {
            echo ("Error al conectar a la base de datos: $exp");
            return null;
        }
    }
}
?>