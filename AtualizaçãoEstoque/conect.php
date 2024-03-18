<?php
class Conexao {
    
    private static $pdo;

    public static function Connect() {
        try {
            self::$pdo = new PDO('mysql:host=localhost;dbname=estoque', 'user', 'senha');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return self::$pdo;
        } catch(PDOException $e) {
            throw new Exception("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }
}
?>
