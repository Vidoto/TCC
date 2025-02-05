<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id_receita'])) {
    header("Location: historico.php");
    exit();
}

$id_receita = $_GET['id_receita'];

$sql = "SELECT rf.*, f.nome_farmacia, f.endereco, f.telefone 
        FROM respostas_farmacias rf 
        JOIN farmacias f ON rf.id_farmacia = f.id_farmacia 
        WHERE rf.id_receita = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_receita]);
$respostas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/historico.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Respostas da Farmácia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #1a1a1a;
            line-height: 1.6;
        }

        main {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        h1 {
            color: #1a1a1a;
            margin-bottom: 2rem;
            font-size: 2rem;
            text-align: center;
        }

        .respostas-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .resposta-farmacia {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .resposta-farmacia:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .resposta-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e1e1e1;
        }

        .farmacia-avatar {
            width: 40px;
            height: 40px;
            background-color: #2196f3;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-weight: bold;
        }

        .farmacia-info {
            flex-grow: 1;
        }

        .farmacia-nome {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.2rem;
        }

        .data-resposta {
            font-size: 0.85rem;
            color: #666;
        }

        .resposta-conteudo {
            color: #333;
            line-height: 1.6;
            margin-top: 0.5rem;
        }

        .resposta-footer {
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e1e1e1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
        }

        .none {
            text-align: center;
            color: #666;
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        a{
            text-decoration: none;
            color: #434343;
        }
    </style>
</head>
<body>

<main>
    <h1>Respostas das Farmácias</h1>
    
    <?php if (count($respostas) > 0) { ?>
    <div class="respostas-container">
        <?php foreach ($respostas as $resposta) { ?>
            <div class="resposta-farmacia">
        <div class="resposta-header">
            <div class="farmacia-avatar">
                <?php echo substr(htmlspecialchars($resposta['nome_farmacia']), 0, 2); ?>
            </div>
            <div class="farmacia-info">
                <div class="farmacia-nome"><?php echo htmlspecialchars($resposta['nome_farmacia']); ?></div>
                <div class="data-resposta">
                    <i class="far fa-clock"></i> <?php echo date('d M Y, H:i', strtotime($resposta['data_resposta'])); ?>
                </div>
            </div>
            <span class="status-badge <?php echo $resposta['disponibilidade'] === 'disponivel' ? 'available' : 'unavailable'; ?>">
                <?php echo $resposta['disponibilidade'] === 'disponivel' ? 'Disponível' : 'Indisponível'; ?>
            </span>
        </div>
        <div class="resposta-conteudo">
            <?php echo nl2br(htmlspecialchars($resposta['resposta'])); ?>
        </div>
        <div class="resposta-footer">
            <div>
                <i class="fas fa-map-marker-alt"></i> 
                <?php echo htmlspecialchars($resposta['endereco']); ?>
            </div>
            <div>
            <a href="tel:<?php echo htmlspecialchars($resposta['telefone']); ?>">
                <i class="fas fa-phone"></i> 
                <?php if (isset($resposta['telefone'])): ?>
                        <?php echo htmlspecialchars($resposta['telefone']); ?>
                    </a>
                <?php else: ?>
                    Telefone não disponível
                <?php endif; ?>
            </div>
        </div>
    </div>

        <?php } ?>
    </div>
<?php } else { ?>
    <p class="none">Nenhuma resposta encontrada para esta receita.</p>
<?php } ?>
</main>


</body>
</html>
