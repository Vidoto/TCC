<?php
include 'config.php';

// Get exam ID from URL
$id_exame = isset($_GET['id_exame']) ? intval($_GET['id_exame']) : 0;

// Check if the exam ID is valid
if ($id_exame > 0) {
    // Fetch exam details
    $query_exame = $pdo->prepare("SELECT * FROM exames WHERE id_exame = :id_exame");
    $query_exame->execute([':id_exame' => $id_exame]);
    $exame = $query_exame->fetch();

    // Fetch attachments if the exam exists
    if ($exame) {
        $query_anexos = $pdo->prepare("SELECT * FROM exames_anexos WHERE id_exame = :id_exame");
        $query_anexos->execute([':id_exame' => $id_exame]);
        $anexos = $query_anexos->fetchAll();
    }
} else {
    $exame = null;
    $anexos = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Exame</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .data {
            color: #666;
            margin-bottom: 20px;
        }
        .anexos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .anexo {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        .anexo img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .anexo a {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .anexo a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($exame): ?>
            <h2><?php echo htmlspecialchars($exame['nome_exame']); ?></h2>
            <p class="data">Data: <?php echo date('d/m/Y', strtotime($exame['data_exame'])); ?></p>

            <?php if (count($anexos) > 0): ?>
                <div class="anexos">
                    <?php foreach ($anexos as $anexo): ?>
                        <div class="anexo">
                            <?php if ($anexo['tipo_anexo'] == 'imagem'): ?>
                                <img src="<?php echo htmlspecialchars($anexo['caminho_anexo']); ?>" alt="Imagem do Exame">
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($anexo['caminho_anexo']); ?>" target="_blank">Ver PDF</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Nenhum anexo disponível.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Exame não encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>