<?php
session_start();
include 'config.php';

if (!isset($_SESSION['id_farmacia'])) {
    header("Location: login.php");
    exit;
}

$id_farmacia = $_SESSION['id_farmacia'];
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_receita = filter_input(INPUT_POST, 'id_receita', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $resposta = filter_input(INPUT_POST, 'resposta', FILTER_SANITIZE_STRING);
    $disponibilidade = filter_input(INPUT_POST, 'disponibilidade', FILTER_SANITIZE_STRING);

    if (!$id_receita || !in_array($status, ['pendente', 'respondido']) || 
        !in_array($disponibilidade, ['disponivel', 'indisponivel']) || 
        empty($resposta)) {
        $mensagem = '<div class="alert alert-danger">Por favor, preencha todos os campos corretamente.</div>';
    } else {
        try {
            $pdo->beginTransaction();

            // Update the prescription's status in `receitas`
            $stmt = $pdo->prepare("UPDATE receitas SET status = ? WHERE id_receita = ?");
            $stmt->execute([$status, $id_receita]);

            // Check if a response already exists
            $stmt = $pdo->prepare("
                SELECT id_resposta FROM respostas_farmacias 
                WHERE id_receita = ? AND id_farmacia = ?
            ");
            $stmt->execute([$id_receita, $id_farmacia]);
            $existingResponse = $stmt->fetch();

            if ($existingResponse) {
                // Update the existing response
                $stmt = $pdo->prepare("
                    UPDATE respostas_farmacias 
                    SET resposta = ?, disponibilidade = ?, data_resposta = CURRENT_TIMESTAMP 
                    WHERE id_resposta = ?
                ");
                $stmt->execute([$resposta, $disponibilidade, $existingResponse['id_resposta']]);
            } else {
                // Insert a new response
                $stmt = $pdo->prepare("
                    INSERT INTO respostas_farmacias (id_receita, id_farmacia, resposta, disponibilidade) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$id_receita, $id_farmacia, $resposta, $disponibilidade]);
            }

            $pdo->commit();
            $mensagem = '<div class="alert alert-success">Receita atualizada com sucesso!</div>';
            header("refresh:2;url=farmacia.php");
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensagem = '<div class="alert alert-danger">Erro ao processar a receita: ' . $e->getMessage() . '</div>';
        }
    }
}

// Busca dados da farmácia
$stmt_farmacia = $pdo->prepare("SELECT nome_farmacia, latitude, longitude FROM farmacias WHERE id_farmacia = :id_farmacia");
$stmt_farmacia->bindParam(':id_farmacia', $id_farmacia);
$stmt_farmacia->execute();
$farmacia = $stmt_farmacia->fetch(PDO::FETCH_ASSOC);

if (!$farmacia) {
    echo "Erro: Farmácia não encontrada!";
    exit;
}

$nome_farmacia = htmlspecialchars($farmacia['nome_farmacia']);
$latitude = $farmacia['latitude'];
$longitude = $farmacia['longitude'];

// Busca todas as receitas
$filter = $_GET['filter'] ?? 'todas';
$sql_receitas = "
    SELECT u.email, r.nome_paciente, r.nome_medicamento, 
           r.quantidade_medicamento, r.data_prescricao, 
           r.imagem_receita, r.status, r.id_receita, 
           rf.resposta, rf.data_resposta, rf.disponibilidade
    FROM receitas r
    JOIN usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN respostas_farmacias rf ON r.id_receita = rf.id_receita 
        AND rf.id_farmacia = :id_farmacia";

if ($filter === 'pendentes') {
    $sql_receitas .= " WHERE r.status = 'pendente'";
} elseif ($filter === 'respondidas') {
    $sql_receitas .= " WHERE r.status = 'respondido'";
}

$stmt_receitas = $pdo->prepare($sql_receitas);
$stmt_receitas->bindParam(':id_farmacia', $id_farmacia, PDO::PARAM_INT);
$stmt_receitas->execute();
$receitas = $stmt_receitas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/farmacia.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Dashboard Farmácia</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPo6v3wr-8pj6Z28WuHNbxk5LOivG6I3o"></script>
    <style>
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .action-btn.available {
            background-color: #4CAF50;
            color: white;
        }

        .action-btn.available:hover {
            background-color: #45a049;
        }

        .action-btn.unavailable {
            background-color: #f44336;
            color: white;
        }

        .action-btn.unavailable:hover {
            background-color: #da190b;
        }

        .action-btn i {
            font-size: 1rem;
        }

        .availability-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .availability-status.available {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .availability-status.unavailable {
            background-color: #ffebee;
            color: #c62828;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        /* Cores de status */
        .status-indicator.pendente {
            background-color: #FFDE59;
        }

        .status-indicator.respondido {
            background-color: #7CE552;
        }

    </style>
</head>
<body>
<header>
    <div class="logo">
        <a href="index.php">
            <img src="img/gka.png" alt="Logo" width="105px" height="">
        </a>
    </div>

    <nav class="nav-items">
        <a href="logout.php">Sair</a>
    </nav>

    <button class="toggle-btn" aria-label="Toggle Navigation">&#9776;</button>
</header>

<?php if (!empty($mensagem)): ?>
    <div class="message-container">
        <?php echo $mensagem; ?>
    </div>
<?php endif; ?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Bem-vindo, <?php echo $nome_farmacia; ?></h1>
        <p>Gerencie suas receitas e atenda seus clientes</p>
        <div class="stats-container">
            <div class="stat-card">
                <i class="fas fa-file-medical"></i>
                <h3>Receitas Pendentes</h3>
                <p><?php
                    $receitas_pendentes = array_filter($receitas, function($receita) {
                        return $receita['status'] === 'pendente';
                    });
                    echo count($receitas_pendentes);
                ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-clock"></i>
                <h3>Tempo Médio</h3>
                <p>15 min</p>
            </div>
            <div class="stat-card">
                <i class="fas fa-check-circle"></i>
                <h3>Atendimentos</h3>
                <p>98%</p>
            </div>
        </div>
    </div>
</div>

<!-- Container Principal -->
<div class="main-container">
    <!-- Seção de Receitas -->
    <section class="prescriptions-section">
        <div class="section-header">
            <h2>Receitas Pendentes</h2>
            <div class="filters">
                <a href="?filter=pendentes" class="filter-btn <?php echo ($filter === 'pendentes' || !$filter) ? 'active' : ''; ?>">Pendentes</a>
                <a href="?filter=respondidas" class="filter-btn <?php echo $filter === 'respondidas' ? 'active' : ''; ?>">Respondidas</a>
            </div>

        </div>

        <div class="prescriptions-grid">
            <?php foreach ($receitas as $receita): ?>
                <div class="prescription-card">
                    <div class="card-header">
                        <span class="status-indicator <?php echo $receita['status']; ?>"></span>
                        <span class="date"><?php echo date('d/m/Y', strtotime($receita['data_prescricao'])); ?></span>
                        <?php if ($receita['disponibilidade']): ?>
                            <span class="availability-status <?php echo $receita['disponibilidade']; ?>">
                                <?php echo ucfirst($receita['disponibilidade']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="prescription-content">
                        <div class="prescription-info">
                            <div class="user-info">
                                <i class="fas fa-user-circle"></i>
                                <span><?php echo htmlspecialchars($receita['email']); ?></span>
                            </div>
                            <h4><?php echo htmlspecialchars($receita['nome_paciente']); ?></h4>
                            <h3><?php echo htmlspecialchars($receita['nome_medicamento']); ?></h3>
                            <p class="quantity">
                                <i class="fas fa-pills"></i>
                                Quantidade: <?php echo htmlspecialchars($receita['quantidade_medicamento']); ?>
                            </p>
                        </div>
                        
                        <div class="prescription-image-container">
                            <img src="<?php echo htmlspecialchars($receita['imagem_receita']); ?>" 
                                 alt="Receita" 
                                 class="prescription-image"
                                 onclick="window.open(this.src)">
                            <span class="zoom-hint"><i class="fas fa-search-plus"></i></span>
                        </div>
                    </div>

                    <form method="POST" action="" class="prescription-actions">
                        <input type="hidden" name="id_receita" value="<?php echo $receita['id_receita']; ?>">
                        <input type="hidden" name="status" value="respondido">

                        <textarea name="resposta" class="response-input" 
                                placeholder="Envie sua resposta..." required><?php 
                            echo htmlspecialchars($receita['resposta'] ?? ''); 
                        ?></textarea>

                        <div class="action-buttons">
                            <button type="submit" name="disponibilidade" value="disponivel" class="action-btn available">
                                <i class="fas fa-check"></i> Disponível
                            </button>
                            <button type="submit" name="disponibilidade" value="indisponivel" class="action-btn unavailable">
                                <i class="fas fa-times"></i> Indisponível
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Seção do Mapa -->
    <section class="map-section">
        <h2>Localização da Farmácia</h2>
        <div id="map"></div>
    </section>
</div>

<script>
function initMap() {
    const farmaciaLocation = { 
        lat: <?php echo $latitude; ?>, 
        lng: <?php echo $longitude; ?> 
    };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: farmaciaLocation,
    });
    const marker = new google.maps.Marker({
        position: farmaciaLocation,
        map: map,
        title: "<?php echo $nome_farmacia; ?>",
    });
}

window.onload = initMap;
</script>

</body>
</html>