<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the SQL query with filters
$sql = "SELECT r.* 
        FROM receitas r 
        WHERE r.id_usuario = ?";
$params = [$id_usuario];

if (!empty($data_inicio)) {
    $sql .= " AND r.data_prescricao >= ?";
    $params[] = $data_inicio;
}
if (!empty($data_fim)) {
    $sql .= " AND r.data_prescricao <= ?";
    $params[] = $data_fim;
}
if (!empty($status)) {
    $sql .= " AND r.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY r.data_prescricao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/historico.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Histórico de Receitas</title>
    <style>
        
    </style>
</head>
<body class="fade-in">

<?php include 'navbar.php'; ?>

<main>
    <!-- Modal para Respostas -->
    <div id="respostasModal" class="modal-overlay">
        <div class="modal-container">
            <button class="close-modal" onclick="closeModal()">&times;</button>
            <iframe id="respostasIframe" class="modal-iframe" src="" frameborder="0"></iframe>
        </div>
    </div>

    <!-- Modal para Imagens -->
    <div id="imageModal" class="image-modal-overlay">
        <div class="image-modal-container">
            <button class="close-image-modal" onclick="closeImageModal()">&times;</button>
            <img id="modalImage" class="image-modal-content" src="" alt="Imagem ampliada">
        </div>
    </div>

    <div class="filters-container">
        <form class="filters-form" method="GET">
            <div class="filter-group">
                <label for="data_inicio">Data Inicial:</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>">
            </div>
            
            <div class="filter-group">
                <label for="data_fim">Data Final:</label>
                <input type="date" id="data_fim" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>">
            </div>
            
            <div class="filter-group">
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="">Todos</option>
                    <option value="pendente" <?php echo $status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="respondido" <?php echo $status === 'respondido' ? 'selected' : ''; ?>>Respondido</option>
                </select>
            </div>
            
            <div class="filter-buttons">
                <button type="submit" class="filter-button apply-filter">
                    <i class="fas fa-filter"></i> Aplicar Filtros
                </button>
                <button type="button" class="filter-button clear-filter" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Limpar Filtros
                </button>
            </div>
        </form>
    </div>

    <?php if (count($receitas) > 0) { ?>
        <div class="receitas-container">
            <?php foreach ($receitas as $receita) { ?>
                <div class="receita">
                    <p><strong>Paciente:</strong> <?php echo htmlspecialchars($receita['nome_paciente']); ?></p>
                    <p><strong>Medicamento:</strong> <?php echo htmlspecialchars($receita['nome_medicamento']); ?></p>
                    <p><strong>Quantidade:</strong> <?php echo htmlspecialchars($receita['quantidade_medicamento']); ?></p>
                    <p><strong>Data:</strong> <?php echo htmlspecialchars($receita['data_prescricao']); ?></p>
                    
                    <div class="status-badge <?php echo $receita['status'] === 'pendente' ? 'status-pendente' : 'status-concluido'; ?>">
                        Status: <?php echo ucfirst(htmlspecialchars($receita['status'] ?? 'pendente')); ?>
                    </div>
                    
                    <?php if (!empty($receita['imagem_receita'])) { ?>
                        <div class="receita-imagem" onclick="openImageModal('<?php echo htmlspecialchars($receita['imagem_receita']); ?>')">
                            <img src="<?php echo htmlspecialchars($receita['imagem_receita']); ?>" alt="Imagem da Receita">
                            <div class="zoom-icon">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    <?php } ?>

                    <button onclick="verRespostas(<?php echo htmlspecialchars($receita['id_receita']); ?>)" class="botao-respostas">
                        Ver Respostas
                    </button>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="none">Nenhuma receita encontrada.</p>
    <?php } ?>
</main>

<?php include 'footer.php'; ?>

<script>
// Função para abrir o modal com as respostas
function verRespostas(idReceita) {
    const modal = document.getElementById('respostasModal');
    const iframe = document.getElementById('respostasIframe');
    
    iframe.src = `respostas_receita.php?id_receita=${idReceita}`;
    
    modal.style.display = 'flex';
    modal.classList.add('fade-in');
    
    document.body.style.overflow = 'hidden';
}

// Função para fechar o modal de respostas
function closeModal() {
    const modal = document.getElementById('respostasModal');
    const iframe = document.getElementById('respostasIframe');
    
    modal.classList.add('fade-out');
    
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('fade-out');
        iframe.src = '';
    }, 500);
    
    document.body.style.overflow = 'auto';
}

// Função para abrir o modal de imagem
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    modalImg.src = imageSrc;
    modal.style.display = 'flex';
    modal.classList.add('fade-in');
    
    document.body.style.overflow = 'hidden';
}

// Função para fechar o modal de imagem
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    
    modal.classList.add('fade-out');
    
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('fade-out');
    }, 500);
    
    document.body.style.overflow = 'auto';
}

// Event Listeners para fechar modais ao clicar fora
document.getElementById('respostasModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Prevenir que o clique na imagem dentro do modal feche o modal
document.getElementById('modalImage').addEventListener('click', function(e) {
    e.stopPropagation();
});

// Suporte para tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeImageModal();
    }
});

// Animação de fade para navegação
document.addEventListener("DOMContentLoaded", function() {
    document.body.classList.add('fade-in');

    var links = document.querySelectorAll('a');
    links.forEach(function(link) {
        link.addEventListener('click', function(event) {
            var href = this.getAttribute('href');
            if (href && href !== '#') {
                event.preventDefault();
                document.body.classList.add('fade-out');
                setTimeout(function() {
                    window.location = href;
                }, 500);
            }
        });
    });
});

// Toggle menu para responsividade
function toggleMenu() {
    const navItems = document.querySelector('.nav-items');
    const isMenuOpen = navItems.clientHeight > 0;

    if (!isMenuOpen) {
        navItems.style.display = 'flex';
        navItems.style.height = '0';
        navItems.style.opacity = '0';
        navItems.style.overflow = 'hidden';

        const fullHeight = navItems.scrollHeight + 'px';

        setTimeout(() => {
            navItems.style.transition = 'height 0.5s ease, opacity 0.5s ease';
            navItems.style.height = fullHeight;
            navItems.style.opacity = '1';
        }, 10);
    } else {
        navItems.style.transition = 'height 0.5s ease, opacity 0.5s ease';
        navItems.style.height = '0';
        navItems.style.opacity = '0';

        setTimeout(() => {
            navItems.style.display = 'none';
        }, 500);
    }
}
</script>
<script>
function clearFilters() {
    document.getElementById('data_inicio').value = '';
    document.getElementById('data_fim').value = '';
    document.getElementById('status').value = '';
    document.querySelector('.filters-form').submit();
}
</script>
</body>
</html>