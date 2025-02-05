<?php
    $servername = "45.152.44.154";
    $username = "u451416913_2024grupo23";
    $password = "Grupo23@123";
    $dbname = "u451416913_2024grupo23";

    //$servername = "localhost";
    //$username = "root";
    //$password = "";
    //$dbname = "sistema_farmacia";

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo "Erro na conexão com o banco de dados: " . $e->getMessage();
    }
?>
