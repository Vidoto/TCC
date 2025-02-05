<?php
include 'config.php';

session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit();
}

$nome_admin = $_SESSION['nome_admin'];

if (!isset($_GET['id_usuario'])) {
    echo "ID do usuário não especificado.";
    exit();
}

$id_usuario = $_GET['id_usuario'];
?>

<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/css/style.css">
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="assets/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <title>Usuário</title>
</head>

<body>
    <!-- ============================================================== -->
    <!-- main wrapper -->
    <!-- ============================================================== -->
    <div class="dashboard-main-wrapper">
        <!-- ============================================================== -->
        <!-- navbar -->
        <!-- ============================================================== -->
        <div class="dashboard-header">
            <nav class="navbar navbar-expand-lg fixed-top" style="background-color: #001F33;">
            <div class="navbar-brand"><img src="img/gka.png" width="120px" height=""> </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse "  id="navbarSupportedContent" style="background-color: #001F33">
                    <ul class="navbar-nav ml-auto navbar-right-top" >
                        <li class="nav-item">
                            <div id="custom-search" class="top-search-bar">
                                <input class="form-control" type="text" placeholder="Search..">
                            </div>
                        </li>
                        
                        <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="assets/images/avatar-1.jpg" alt="" class="user-avatar-md rounded-circle"></a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-power-off mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <!-- ============================================================== -->
        <!-- end navbar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- left sidebar -->
        <!-- ============================================================== -->
        <div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider">Menu</li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php">
                            <i class="fa fa-fw fa-user-circle"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerenciar_usuarios.php">
                            <i class="fa fa-fw fa-users"></i> Gerenciar Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gerenciar_farmacias.php">
                            <i class="fa fa-fw fa-building"></i> Gerenciar Farmácias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="adicionar_farmacia.php">
                            <i class="fa fa-fw fa-plus"></i> Cadastrar Farmácia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="visualizar_feedbacks.php">
                            <i class="fa fa-fw fa-comments"></i> Visualizar Feedbacks
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
        <!-- ============================================================== -->
        <!-- end left sidebar -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- wrapper  -->
        <!-- ============================================================== -->
        <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce">
                <div class="container-fluid dashboard-content ">
                    <!-- ============================================================== -->
                    <!-- pageheader  -->
                    <!-- ============================================================== -->
                    <main class="main">
        <div class="header">
            <h1>Bem-vindo, <?php echo htmlspecialchars($nome_admin); ?>!</h1>
        </div>

        <div class="user-details">
    <?php
    try {
        // Buscar informações do usuário
        $stmt = $pdo->prepare("SELECT nome_usuario, email, data_cadastro, foto_perfil FROM usuarios WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            echo "<h2>" . htmlspecialchars($user["nome_usuario"]) . "</h2>
                  <p><strong>Email:</strong> " . htmlspecialchars($user["email"]) . "</p>
                  <p><strong>Data de Cadastro:</strong> " . htmlspecialchars($user["data_cadastro"]) . "</p>";
        } else {
            echo "Usuário não encontrado.";
        }
    } catch (PDOException $e) {
        echo "Erro ao buscar informações do usuário: " . $e->getMessage();
    }

    try {
        $stmt = $pdo->prepare("SELECT nome_medicamento, quantidade_medicamento, data_prescricao, imagem_receita FROM receitas WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        $receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($receitas) > 0) {
            echo "<h3>Histórico de Receitas</h3>
                  <div class='receitas-container'>";

            foreach ($receitas as $receita) {
                // Verificar se a imagem existe e construir o caminho absoluto
                if(!empty($receita["imagem_receita"])){
                    if(file_exists($receita["imagem_receita"])){
                        $imagem_receita = htmlspecialchars($receita["imagem_receita"]);
                    }else{
                        $imagem_receita = 'img/default_receita.png';
                    }
                }
                else{
                    $imagem_receita = 'img/default_receita.png';
                }
                
                echo "<div class='receita-card'>
                        <img src='" . $imagem_receita . "' alt='Imagem da Receita' onerror=\"this.onerror=null;this.src='img/default_receita.png';\">
                        <h4>" . htmlspecialchars($receita["nome_medicamento"]) . "</h4>
                        <p><strong>Quantidade:</strong> " . htmlspecialchars($receita["quantidade_medicamento"]) . "</p>
                        <p><strong>Data:</strong> " . htmlspecialchars($receita["data_prescricao"]) . "</p>
                      </div>";
            }
            echo "</div>";
        } else {
            echo "Nenhuma receita encontrada para este usuário.";
        }
    } catch (PDOException $e) {
        echo "Erro ao buscar receitas: " . $e->getMessage();
    }
    ?>
</div>

    </main>

            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <div class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                             Copyright © 2018 Concept. All rights reserved. Dashboard by <a href="https://colorlib.com/wp/">Colorlib</a>.
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="text-md-right footer-links d-none d-sm-block">
                                <a href="javascript: void(0);">About</a>
                                <a href="javascript: void(0);">Support</a>
                                <a href="javascript: void(0);">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- end footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- end wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- end main wrapper  -->
    <!-- ============================================================== -->
    <!-- Optional JavaScript -->
    <!-- jquery 3.3.1 -->
    <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <!-- bootstap bundle js -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <!-- slimscroll js -->
    <script src="assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <!-- main js -->
    <script src="assets/libs/js/main-js.js"></script>
    <!-- chart chartist js -->
    <script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <!-- sparkline js -->
    <script src="assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <!-- morris js -->
    <script src="assets/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="assets/vendor/charts/morris-bundle/morris.js"></script>
    <!-- chart c3 js -->
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>
</body>
 
</html>