<?php
class Conexion {
    public static function ConexionBD() {

       $host = "db.rxkxdwknfhqgjwncqhbu.supabase.co";
$port = "5432";
$dbname = "postgres";
$user = "postgres";
$password = "@DocManager2026";

        try {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
