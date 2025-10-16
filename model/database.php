<?php
class Database {
    private static ?PDO $conn = null;

    public static function getConnection(): PDO {
        if (self::$conn === null) {
            $server = 'localhost';
            $username = 'root';
            $password = '';
            $db = 'php-test';
            try {
                self::$conn = new PDO(
                    "mysql:host={$server};dbname={$db};charset=utf8",
                    $username,
                    $password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$conn;
    }
}
?>