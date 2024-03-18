<?php
require_once 'conect.php'; 
Class EstoqueDAO {

    function atualizarEstoqueFromJson(string $jsonString): bool
    {
        $produtos = json_decode($jsonString, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Erro ao decodificar o JSON: ' . json_last_error_msg());
        }

        try {
            $pdo = Conexao::connect(); 

            $pdo->beginTransaction();

            foreach ($produtos as $produto) {
                $sql = "SELECT COUNT(0) AS count FROM estoque 
                        WHERE produto = :produto";

                $stmt_select = $pdo->prepare($sql);
                $stmt_select->execute([
                    ':produto' => $produto['produto']
                ]);

                $count = $stmt_select->fetchColumn();

                if ($count > 0) {
                    $sql = "UPDATE estoque
                            SET quantidade = :quantidade,
                                tamanho = :tamanho,
                                cor = :cor,
                                deposito = :deposito,
                                data_disponibilidade = :data_disponibilidade
                            WHERE produto = :produto";

                    $stmt_update = $pdo->prepare($sql);
                    $stmt_update->execute([
                        ':quantidade' => $produto['quantidade'],
                        ':produto' => $produto['produto'],
                        ':cor' => $produto['cor'],
                        ':tamanho' => $produto['tamanho'],
                        ':deposito' => $produto['deposito'],
                        ':data_disponibilidade' => $produto['data_disponibilidade']
                    ]);
                } else {
                    $sql = "INSERT INTO estoque (produto, cor, tamanho, deposito, data_disponibilidade, quantidade)
                            VALUES (:produto, :cor, :tamanho, :deposito, :data_disponibilidade, :quantidade)";

                    $stmt_insert = $pdo->prepare($sql);
                    $stmt_insert->execute([
                        ':produto' => $produto['produto'],
                        ':cor' => $produto['cor'],
                        ':tamanho' => $produto['tamanho'],
                        ':deposito' => $produto['deposito'],
                        ':data_disponibilidade' => $produto['data_disponibilidade'],
                        ':quantidade' => $produto['quantidade']
                    ]);
                }
            }

            $pdo->commit();
            $pdo = null; 
            return true;
        } catch (Exception $e) {
            if ($pdo !== null) {
                $pdo->rollBack();
                $pdo = null;
            }
            return false;
        }
    }
}
?>
