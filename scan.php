<?php
include 'config.php';
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id_usuario']; 
    $nome_paciente = $_POST['nome_paciente'];
    $nome_medicamento = $_POST['nome_medicamento'];
    $quantidade_medicamento = $_POST['quantidade_medicamento'];
    $data_prescricao = $_POST['data_prescricao'];

    $imagem_receita = '';
    if (isset($_FILES['imagem_receita']) && $_FILES['imagem_receita']['error'] == 0) {
        $upload_dir = 'receitas/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $imagem_receita = $upload_dir . basename($_FILES['imagem_receita']['name']);
        move_uploaded_file($_FILES['imagem_receita']['tmp_name'], $imagem_receita);
    }

    $sql = "INSERT INTO receitas (id_usuario, nome_paciente, imagem_receita, nome_medicamento, quantidade_medicamento, data_prescricao) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$id_usuario, $nome_paciente, $imagem_receita, $nome_medicamento, $quantidade_medicamento, $data_prescricao])) {
        $mensagem = "Receita enviada com sucesso!";
        $mensagem_tipo = "success";
    } else {
        $mensagem = "Erro ao enviar a receita: " . $stmt->errorInfo()[2];
        $mensagem_tipo = "error";
    }
}

// Parte do autocomplete via GET com distância
if (isset($_GET['searchTerm']) && isset($_GET['latitude']) && isset($_GET['longitude'])) {
    $searchTerm = $_GET['searchTerm'] ?? '';
    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];

    $sql = "SELECT id_farmacia, nome_farmacia, endereco, latitude, longitude FROM farmacias WHERE nome_farmacia LIKE ? OR endereco LIKE ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
    $farmacias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $apiKey = 'AIzaSyBZSXjo8PwAc6QtPpHyH7WeKfIJs5tpsk4';

    foreach ($farmacias as &$farmacia) {
        $destino = $farmacia['latitude'] . ',' . $farmacia['longitude'];
        $origem = $latitude . ',' . $longitude;

        $distanceUrl = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origem&destinations=$destino&key=$apiKey";
        $distanceResponse = file_get_contents($distanceUrl);
        $distanceData = json_decode($distanceResponse, true);

        if (!empty($distanceData['rows'][0]['elements'][0]['distance'])) {
            $farmacia['distancia'] = $distanceData['rows'][0]['elements'][0]['distance']['text'];
        } else {
            $farmacia['distancia'] = "Distância não disponível";
        }
    }

    echo json_encode($farmacias);
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/scan.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Escanear Receita</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body class="fade-in">

<?php include 'navbar.php'; ?>

<main>
    <div class="form-container">
        <form action="scan.php" method="post" enctype="multipart/form-data">
            <h2>Escanear Receita</h2>
            
            <?php if (isset($mensagem)) { ?>
                <div class="mensagem <?php echo $mensagem_tipo; ?>">
                    <i class="fas fa-<?php echo $mensagem_tipo === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $mensagem; ?>
                </div>
            <?php } ?>

            <div class="form-group">
                <label for="nome_paciente">Nome do Paciente</label>
                <input type="text" id="nome_paciente" name="nome_paciente" required>
            </div>

            <div class="form-group">
                <label for="nome_medicamento">Nome do Medicamento</label>
                <input type="text" id="nome_medicamento" name="nome_medicamento" required>
            </div>

            <div class="form-group">
                <label for="quantidade_medicamento">Quantidade do Medicamento</label>
                <input type="number" id="quantidade_medicamento" name="quantidade_medicamento" required>
            </div>

            <div class="form-group">
                <label for="data_prescricao">Data da Prescrição</label>
                <input type="date" id="data_prescricao" name="data_prescricao" required>
            </div>

            <div class="form-group">
                <label for="imagem_receita" class="custom-file-upload">
                    <i class="fas fa-upload"></i>
                    Fazer upload da receita
                    <input type="file" id="imagem_receita" name="imagem_receita" accept="image/*" required>
                </label>
            </div>

            <button type="submit">
                <i class="fas fa-paper-plane"></i>
                Enviar Receita
            </button>
        </form>
    </div>
</main>

<?php include 'footer.php'; ?>

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