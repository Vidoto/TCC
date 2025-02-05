<?php
include 'config.php';

session_start();

if (!isset($_SESSION['nome_usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: login.php"); 
    exit();
}

$mensagem = "";
$classe_mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $palavra_passe = $_POST['palavra_passe'];

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto_perfil']['tmp_name'];
        $fileName = $_FILES['foto_perfil']['name'];
        $fileSize = $_FILES['foto_perfil']['size'];
        $fileType = $_FILES['foto_perfil']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploaded_images/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $mensagem = 'Foto de perfil carregada com sucesso.';
                $classe_mensagem = "sucesso";
            } else {
                $mensagem = 'Erro ao carregar a foto de perfil.';
                $classe_mensagem = "erro";
            }
        } else {
            $mensagem = 'Extensão de arquivo não permitida.';
            $classe_mensagem = "erro";
        }
    }

    $sql = "UPDATE usuarios SET nome_usuario = ?, email = ?, senha = ?, palavra_passe = ?" . (isset($newFileName) ? ", foto_perfil = ?" : "") . " WHERE id_usuario = ?";
    $stmt = $pdo->prepare($sql);

    $params = [$nome, $email, $senha, $palavra_passe];
    if (isset($newFileName)) {
        $params[] = $newFileName;
    }
    $params[] = $_SESSION['id_usuario'];

    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        $_SESSION['nome_usuario'] = $nome;
        $_SESSION['email'] = $email;
        $_SESSION['senha_usuario'] = $senha;
        $_SESSION['palavra_passe'] = $palavra_passe;

        if (isset($newFileName)) {
            $_SESSION['foto_perfil'] = $newFileName;
        }

        $mensagem = "Informações atualizadas com sucesso!";
        $classe_mensagem = "sucesso";
    } 
}

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT DATE(data_prescricao) as data_prescricao, GROUP_CONCAT(nome_medicamento SEPARATOR ', ') as medicamentos, COUNT(*) as quantidade 
        FROM receitas 
        WHERE id_usuario = ? AND data_prescricao BETWEEN ? AND ?
        GROUP BY DATE(data_prescricao)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_usuario, $startDate, $endDate]);
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dataPoints = [];
$period = new DatePeriod(
    new DateTime($startDate),
    new DateInterval('P1D'),
    new DateTime($endDate . ' +1 day')
);

foreach ($period as $date) {
    $dayOfMonth = (int) $date->format('j');
    $dataPoints[$dayOfMonth] = ['quantidade' => 0, 'medicamentos' => ''];
}

foreach ($receitas as $receita) {
    $data = new DateTime($receita['data_prescricao']);
    $dayOfMonth = (int) $data->format('j');
    $dataPoints[$dayOfMonth] = ['quantidade' => $receita['quantidade'], 'medicamentos' => $receita['medicamentos']];
}

$dataPoints = array_map(function($day, $data) {
    return ['label' => $day, 'y' => $data['quantidade'], 'medicamentos' => $data['medicamentos']];
}, array_keys($dataPoints), $dataPoints);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/usuario_info.css">
    <title>Informações do Usuário</title>
</head>
<body class="fade-in">

<?php include 'navbar.php'; ?>

<div class="user-info">
    <div class="left-side">
        <div class="profile-info">
            <h2>Suas Informações</h2>
            <center><img id="profile-img" src="<?php echo isset($_SESSION['foto_perfil']) ? 'uploaded_images/' . $_SESSION['foto_perfil'] : 'img/profile.png'; ?>"></center>
            <?php if(isset($_SESSION['nome_usuario'])) { ?>
            <h3 style="margin-top: 0px;">Olá, <?php echo $_SESSION['nome_usuario']; ?></h3>
            <?php } ?>
        </div>
        <a style="margin-bottom: 24px" href="logout.php">Sair da conta</a>
    </div>

    <div class="right-side">
        <?php if (!empty($mensagem)) { ?>
            <div class="mensagem <?php echo $classe_mensagem; ?>"><?php echo $mensagem; ?></div>
        <?php } ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <label>Seu nome</label>
            <input class="form-input" type="text" name="nome" placeholder="seu nome" value="<?php echo htmlspecialchars($_SESSION['nome_usuario'] ?? ''); ?>" required>
            <label>Seu email</label>
            <input class="form-input" type="email" name="email" placeholder="seu email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
            <label>Sua senha</label>
            <input class="form-input" type="password" name="senha" placeholder="sua senha" value="<?php echo htmlspecialchars($_SESSION['senha_usuario'] ?? ''); ?>" required>
            <label>Sua palavra passe</label>
            <input class="form-input" type="password" name="palavra_passe" placeholder="sua palavra passe" value="<?php echo htmlspecialchars($_SESSION['palavra_passe'] ?? ''); ?>" required>
            <label>Foto de perfil</label>
            <input class="input-file" type="file" name="foto_perfil">
            <button class="form-button atualizar-button" type="submit">Atualizar Informações</button>

            <label for="start_date">Data de Início:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" required>

        <label for="end_date">Data de Fim:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" required>

        <button class="filtrar-button" type="submit">Filtrar</button>

        <div id="chartContainer" style="height: 300px; width: 100%; margin-top: 20px;"></div>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script>
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Receitas por Dia do Mês"
                },
                axisX: {
                    title: "Dias do Mês",
                    interval: 2,
                    labelAngle: -45
                },
                axisY: {
                    title: "Quantidade de Receitas",
                    maximum: 50,
                    interval: 5
                },
                data: [{
                    type: "column",
                    toolTipContent: "<b>Dia:</b> {label} <br/><b>Quantidade:</b> {y} <br/><b>Medicamentos:</b> <a href='historico.php'>{medicamentos}</a>",
                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                }],
                dataPointClick: function(e) {
                    var day = e.dataPoint.label;
                    var month = new Date().getMonth() + 1; // Pega o mês atual
                    var year = new Date().getFullYear(); // Pega o ano atual
                    var url = 'historico.php?date=' + year + '-' + month + '-' + day;
                    window.location.href = url;
                }
            });

            chart.render();
        }
    </script>

<script>
function toggleMenu() {
    const navItems = document.querySelector('.nav-items');
    const isMenuOpen = navItems.clientHeight > 0; // Verifica se o menu está aberto

    if (!isMenuOpen) {
        // Abrir o menu (de cima para baixo)
        navItems.style.display = 'flex'; // Certifica-se de que o menu está visível
        navItems.style.height = '0'; // Começa com a altura 0
        navItems.style.opacity = '0'; // Inicia com opacidade 0
        navItems.style.overflow = 'hidden'; // Esconde o conteúdo durante a animação

        const fullHeight = navItems.scrollHeight + 'px'; // Obtém a altura total do menu

        // Adiciona a animação de altura e opacidade
        setTimeout(() => {
            navItems.style.transition = 'height 0.5s ease, opacity 0.5s ease';
            navItems.style.height = fullHeight;
            navItems.style.opacity = '1';
        }, 10); // Timeout curto para permitir o reflow
    } else {
        // Fechar o menu (de baixo para cima)
        navItems.style.transition = 'height 0.5s ease, opacity 0.5s ease';
        navItems.style.height = '0'; // Fecha o menu
        navItems.style.opacity = '0'; // Reduz a opacidade para desaparecer

        // Aguarda a transição terminar antes de esconder completamente
        setTimeout(() => {
            navItems.style.display = 'none'; // Esconde o menu após a animação
        }, 500); // Tempo igual ao da animação
    }
}
</script>
    </form>
    </form>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
