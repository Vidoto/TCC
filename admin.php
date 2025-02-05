<?php
session_start();
include 'config.php';

// Verifica se id_admin e nome_admin estão definidos
if (!isset($_SESSION['id_admin']) || !isset($_SESSION['nome_admin'])) {
    header("Location: login.php");
    exit();
}

// Se as variáveis de sessão estiverem presentes, armazena o nome do admin
$nome_admin = $_SESSION['nome_admin'];

function displayViewData($pdo, $viewName, $title) {
    try {
        $stmt = $pdo->query("SELECT * FROM $viewName");

        echo "<div class='table-responsive'>
                <table class='table table-striped table-bordered first w-100'>
                    <thead>
                        <tr>";
        
        // Output table headers
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            foreach ($row as $key => $value) {
                echo "<th>$key</th>";
            }
            echo "</tr></thead><tbody>";

            // Output first row of data
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";

            // Output remaining rows
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }

            echo "</tbody></table></div>";
        } else {
            echo "<p>No data available in this view.</p>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

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
    <title>Dashboard</title>
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
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="page-header">
                                <h2 class="pageheader-title">Administrador - Dashboard</h2>
                                <p class="pageheader-text">Nulla euismod urna eros, sit amet scelerisque torton lectus vel mauris facilisis faucibus at enim quis massa lobortis rutrum.</p>
                                <div class="page-breadcrumb">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Dashboard</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Estatísticas</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ============================================================== -->
                    <!-- end pageheader  -->
                    <!-- ============================================================== -->
                    <div class="ecommerce-widget">


                    <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-muted">Número de usuários</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">500</h1>
                                        </div>
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span><i class="fa fa-fw fa-arrow-up"></i></span><span>5.86%</span>
                                        </div>
                                    </div>
                                    <div id="sparkline-revenue"></div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-muted">Número de receitas</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">1054</h1>
                                        </div>
                                        <div class="metric-label d-inline-block float-right text-success font-weight-bold">
                                            <span><i class="fa fa-fw fa-arrow-up"></i></span><span>5.86%</span>
                                        </div>
                                    </div>
                                    <div id="sparkline-revenue2"></div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-muted">Feedbacks</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">327</h1>
                                        </div>
                                        <div class="metric-label d-inline-block float-right text-primary font-weight-bold">
                                            <span>N/A</span>
                                        </div>
                                    </div>
                                    <div id="sparkline-revenue3"></div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="text-muted">Número de exames</h5>
                                        <div class="metric-value d-inline-block">
                                            <h1 class="mb-1">269</h1>
                                        </div>
                                        <div class="metric-label d-inline-block float-right text-secondary font-weight-bold">
                                            <span>-2.00%</span>
                                        </div>
                                    </div>
                                    <div id="sparkline-revenue4"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
    <div class="col-xl-9 col-lg-12 col-md-6 col-sm-12 col-12">
        <div class="card">
            <h5 class="card-header">Usuários com mais receitas</h5>
            <div class="card-body">
                <?php displayViewData($pdo, "usuarios_mais_receitas", "Usuários com Mais Receitas"); ?>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <h5 class="card-header">Receitas</h5>
            <div class="card-body">
                <div class="ct-chart ct-golden-section" style="height: 354px;"></div>
                <div class="text-center">
                    <span class="legend-item mr-2">
                        <span class="fa-xs text-primary mr-1 legend-tile">
                            <i class="fa fa-fw fa-square-full"></i>
                        </span>
                        <span class="legend-text">Por hora</span>
                    </span>
                    <span class="legend-item mr-2">
                        <span class="fa-xs text-secondary mr-1 legend-tile">
                            <i class="fa fa-fw fa-square-full"></i>
                        </span>
                        <span class="legend-text">Por dia</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12">
        <div class="card">
            <h5 class="card-header">Controle Geral</h5>
            <div class="card-body">
                <div class="ct-chart-category ct-golden-section" style="height: 315px;"></div>
                <div class="text-center m-t-40">
                    <span class="legend-item mr-3">
                        <span class="fa-xs text-primary mr-1 legend-tile">
                            <i class="fa fa-fw fa-square-full"></i>
                        </span>
                        <span class="legend-text">Usuários</span>
                    </span>
                    <span class="legend-item mr-3">
                        <span class="fa-xs text-secondary mr-1 legend-tile">
                            <i class="fa fa-fw fa-square-full"></i>
                        </span>
                        <span class="legend-text">Farmácias</span>
                    </span>
                    <span class="legend-item mr-3">
                        <span class="fa-xs text-info mr-1 legend-tile">
                            <i class="fa fa-fw fa-square-full"></i>
                        </span>
                        <span class="legend-text">Administradores</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-7 col-lg-7 col-md-12 col-sm-12 col-12">
        <div class="card">
            <h5 class="card-header">Previsão de crescimento</h5>
            <div class="card-body">
                <div id="morris_totalrevenue"></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Card de Novos Usuários por Mês (Lateral Esquerda) -->
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-header">Novos Usuários por Mês</h5>
                    <?php displayViewData($pdo, "novos_usuarios_mes", "Novos Usuários por Mês"); ?>
                </div>
            </div>
        </div>

        <!-- Card de Últimos Feedbacks (Lateral Direita) -->
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-header">Últimos Feedbacks</h5>
                    <?php displayViewData($pdo, "feedbacks_mais_novos", "Feedbacks Mais Recentes"); ?>
                </div>
            </div>
        </div>
    </div>
</div>


                                      
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