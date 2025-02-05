<?php
include 'config.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_exame = $_POST['nome_exame'];
    $data_exame = $_POST['data_exame'];

    try {
        $sql = "INSERT INTO exames (id_usuario, nome_exame, data_exame) VALUES (:id_usuario, :nome_exame, :data_exame)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $id_usuario,
            ':nome_exame' => $nome_exame,
            ':data_exame' => $data_exame
        ]);
        $id_exame = $pdo->lastInsertId();

        foreach ($_FILES['anexos']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['anexos']['name'][$key];
            $file_tmp = $_FILES['anexos']['tmp_name'][$key];
            $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($file_type !== 'pdf' && $file_type !== 'jpg' && $file_type !== 'jpeg' && $file_type !== 'png') {
                echo "Erro: Tipo de arquivo não suportado ($file_name). Apenas PDF, JPG e PNG são permitidos.";
                continue;
            }

            $tipo_anexo = ($file_type === 'pdf') ? 'pdf' : 'imagem';

            $destino = 'exames/' . uniqid() . '_' . basename($file_name);
            move_uploaded_file($file_tmp, $destino);

            $sql_anexo = "INSERT INTO exames_anexos (id_exame, caminho_anexo, tipo_anexo) VALUES (:id_exame, :caminho_anexo, :tipo_anexo)";
            $stmt_anexo = $pdo->prepare($sql_anexo);
            $stmt_anexo->execute([
                ':id_exame' => $id_exame,
                ':caminho_anexo' => $destino,
                ':tipo_anexo' => $tipo_anexo
            ]);
        }

    } catch (PDOException $e) {
        echo "Erro ao enviar o exame: " . $e->getMessage();
    }
}

$sql = "SELECT e.id_exame, e.nome_exame, e.data_exame, 
               (SELECT COUNT(*) FROM exames_anexos ea WHERE ea.id_exame = e.id_exame) AS anexos_count
        FROM exames e
        WHERE e.id_usuario = :id_usuario
        ORDER BY e.data_exame DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario);
$stmt->execute();
$exames = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pesquisarExame = isset($_GET['pesquisarExame']) ? trim($_GET['pesquisarExame']) : '';

$sql = "SELECT e.id_exame, e.nome_exame, e.data_exame, 
               (SELECT COUNT(*) FROM exames_anexos ea WHERE ea.id_exame = e.id_exame) AS anexos_count
        FROM exames e
        WHERE e.id_usuario = :id_usuario";

if ($pesquisarExame !== '') {
    $sql .= " AND e.nome_exame LIKE :pesquisarExame";
}

$sql .= " ORDER BY e.data_exame DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario);

if ($pesquisarExame !== '') {
    $pesquisa = "%$pesquisarExame%";
    $stmt->bindParam(':pesquisarExame', $pesquisa);
}

$stmt->execute();
$exames = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/exames.css">
    <title>Enviar Exame</title>
    <script>
        function closeAnuncio() {
            document.getElementById('anuncioOverlay').style.display = 'none';
        }
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div id="examModal" class="modal-overlay">
        <div class="modal-container">
            <button class="modal-close" onclick="closeExamModal()">×</button>
            <iframe id="examFrame" class="modal-iframe" src=""></iframe>
        </div>
    </div>

    <div id="anuncioOverlay" class="anuncio-overlay">
        <div class="anuncio">
            <span>Atenção! Escolha um plano para ter 100% de nossos benefícios!</span>
            <button onclick="closeAnuncio()">✖</button>
        </div>
    </div>

    <main>
        <div class="page-container">
            <div class="max-w-3xl mx-auto">
                <div class="card">
                    <div class="form-container">
                        <div class="card-header">
                            <h2>Envio de Exames</h2>
                            <p>Faça o upload dos seus exames médicos</p>
                        </div>
                        <form action="exames.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="form-label" for="nome_exame">
                                    Nome do Exame
                                </label>
                                <input 
                                    type="text" 
                                    name="nome_exame" 
                                    id="nome_exame" 
                                    class="form-input"
                                    placeholder="Ex: Hemograma Completo"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="data_exame">
                                    Data do Exame
                                </label>
                                <input 
                                    type="date" 
                                    name="data_exame" 
                                    id="data_exame"
                                    class="form-input"
                                >
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    Anexos (imagens ou PDF)
                                </label>
                                <div class="drop-zone" id="dropZone">
                                    <input class="upload-button"
                                        type="file" 
                                        name="anexos[]" 
                                        id="anexos" 
                                        class="custom-file-input"
                                        multiple 
                                        accept="image/*,application/pdf"
                                    >
                                    <p class="upload-info">ou arraste seus arquivos aqui</p>
                                    <p class="text-xs text-gray-400 mt-2">
                                        Formatos aceitos: PDF, JPG, PNG
                                    </p>
                                </div>
                            </div>

                            <button type="submit" class="submit-button">
                                Enviar Exame
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h2 style="padding: 1rem;">Meus Exames</h2>

        <form method="GET" action="exames.php" class="form-pesquisa">
                <input 
                    type="text" 
                    name="pesquisarExame" 
                    value="<?php echo htmlspecialchars($pesquisarExame); ?>" 
                    class="searchExam" 
                    placeholder="Pesquisar exames pelo nome..."
                >
                <button type="submit" class="search-exam-button">Pesquisar</button>
            </form>

        <div class="exame-cards-container">
            <?php if (count($exames) > 0): ?>
                <?php foreach ($exames as $exame): ?>
                    <div class="exame-card">
                        <div class="header">
                            <h3><?php echo $exame['nome_exame']; ?></h3>
                            <p class="date"><?php echo date('d/m/Y', strtotime($exame['data_exame'])); ?></p>
                        </div>
                        <div class="content">
                            <div class="actions">
                                <?php if ($exame['anexos_count'] > 0): ?>
                                    <span class="anexos"><?php echo $exame['anexos_count']; ?> anexos</span>
                                <?php else: ?>
                                    <span class="no-anexos">Nenhum anexo</span>
                                <?php endif; ?>
                                <a href="javascript:void(0);" onclick="showExamDetails(<?php echo $exame['id_exame']; ?>)">Ver detalhes</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">Você ainda não possui nenhum exame enviado.</p>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
        
    </script>

    <script>
        function showExamDetails(examId) {
            const modal = document.getElementById('examModal');
            const iframe = document.getElementById('examFrame');
            iframe.src = `exame_detalhes.php?id_exame=${examId}`;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeExamModal() {
            const modal = document.getElementById('examModal');
            const iframe = document.getElementById('examFrame');
            modal.style.display = 'none';
            iframe.src = '';
            document.body.style.overflow = 'auto';
        }

        document.getElementById('examModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeExamModal();
            }
        });
    </script>
</body>
<script>
function toggleMenu() {
    const toggleButton = document.querySelector('.menu-toggle');
    const navItems = document.querySelector('.nav-items');

    navItems.classList.toggle('active');
    toggleButton.classList.toggle('active');

    if (navItems.classList.contains('active')) {
        toggleButton.setAttribute('aria-expanded', 'true');
        toggleButton.setAttribute('aria-label', 'Fechar Menu');
    } else {
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.setAttribute('aria-label', 'Abrir Menu');
    }
}

document.querySelector('.menu-toggle').addEventListener('click', toggleMenu);
</script>
</html>