<?php
include 'config.php';  // Inclui a configuração do banco de dados

session_start();  // Inicia a sessão

// Verifica se o administrador está logado
if (!isset($_SESSION['id_admin']) || !isset($_SESSION['nome_admin'])) {
    header("Location: login.php");
    exit();
}

$nome_admin = $_SESSION['nome_admin'];  // Armazena o nome do administrador na variável

// Verifica se o ID da farmácia foi passado via GET
if (!isset($_GET['id_farmacia'])) {
    echo "ID da farmácia não especificado.";
    exit();  // Encerra o script se o ID não for fornecido
}

$id_farmacia = (int) $_GET['id_farmacia'];  // Converte para inteiro
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
        <h1>Detalhes da Farmácia</h1>
    </div>

    <div class="farmacia-details" style="border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: 20px auto;">
        <?php
        // Verificar se o ID da farmácia foi passado via GET
        if (isset($_GET['id_farmacia']) && !empty($_GET['id_farmacia'])) {
            $id_farmacia = (int) $_GET['id_farmacia']; // Cast para inteiro

            try {
                // Conectar ao banco e buscar detalhes da farmácia
                $stmt = $pdo->prepare("SELECT nome_farmacia, telefone, email_farmacia, endereco, horario_funcionamento, latitude, longitude 
                                       FROM farmacias WHERE id_farmacia = :id_farmacia");
                $stmt->bindParam(':id_farmacia', $id_farmacia, PDO::PARAM_INT);
                $stmt->execute();

                $farmacia = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($farmacia) {
                    echo "<h2>" . htmlspecialchars($farmacia['nome_farmacia']) . "</h2>
                          <p><strong>Telefone:</strong> " . htmlspecialchars($farmacia['telefone']) . "</p>
                          <p><strong>Email:</strong> " . htmlspecialchars($farmacia['email_farmacia']) . "</p>
                          <p><strong>Endereço:</strong> " . htmlspecialchars($farmacia['endereco']) . "</p>
                          <p><strong>Horário de Funcionamento:</strong> " . htmlspecialchars($farmacia['horario_funcionamento']) . "</p>
                          <p><strong>Latitude:</strong> " . htmlspecialchars($farmacia['latitude']) . "</p>
                          <p><strong>Longitude:</strong> " . htmlspecialchars($farmacia['longitude']) . "</p>";
                } else {
                    echo "<p>Farmácia não encontrada.</p>";
                }
            } catch (PDOException $e) {
                echo "<p>Erro ao buscar informações da farmácia: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>ID da farmácia não especificado.</p>";
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