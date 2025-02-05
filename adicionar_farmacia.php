<?php 
include 'config.php';
session_start();

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit();
}

$nome_admin = $_SESSION['nome_admin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_farmacia = $_POST['nome_farmacia'];
    $telefone = $_POST['telefone'];
    $email_farmacia = $_POST['email_farmacia'];
    $endereco = $_POST['endereco'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $horario_funcionamento = $_POST['horario_funcionamento'];
    $senha_farmacia = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO farmacias (nome_farmacia, telefone, email_farmacia, endereco, latitude, longitude, horario_funcionamento, senha_farmacia)
                VALUES (:nome_farmacia, :telefone, :email_farmacia, :endereco, :latitude, :longitude, :horario_funcionamento, :senha_farmacia)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome_farmacia', $nome_farmacia);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':email_farmacia', $email_farmacia);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        $stmt->bindParam(':horario_funcionamento', $horario_funcionamento);
        $stmt->bindParam(':senha_farmacia', $senha_farmacia);

        if ($stmt->execute()) {
            echo "<script>alert('Farmácia cadastrada com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar farmácia.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erro ao cadastrar farmácia: " . $e->getMessage() . "');</script>";
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
    <title>Cadastro de Farmácia</title>
</head>

<body>
    <div class="dashboard-main-wrapper">
        <div class="dashboard-header">
            <nav class="navbar navbar-expand-lg fixed-top" style="background-color: #001F33;">
                <div class="navbar-brand"><img src="img/gka.png" width="120px" height=""> </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent" style="background-color: #001F33">
                    <ul class="navbar-nav ml-auto navbar-right-top">
                        <li class="nav-item">
                            <div id="custom-search" class="top-search-bar">
                                <input class="form-control" type="text" placeholder="Search..">
                            </div>
                        </li>
                        <li class="nav-item dropdown nav-user">
                            <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="assets/images/avatar-1.jpg" alt="" class="user-avatar-md rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                                <a class="dropdown-item" href="logout.php"><i class="fas fa-power-off mr-2"></i>Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        
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

        <div class="dashboard-wrapper">
            <div class="dashboard-ecommerce">
                <div class="container-fluid dashboard-content">
                    <main class="main">
                        <div class="container-fluid">
                            <h2>Cadastrar Nova Farmácia</h2>
                            <form id="form-farmacia" method="POST" action="">
                                <div class="form-group">
                                    <label for="nome_farmacia">Nome da Farmácia</label>
                                    <input type="text" class="form-control" id="nome_farmacia" name="nome_farmacia" required placeholder="Digite o nome da farmácia">
                                </div>
                                <div class="form-group">
                                    <label for="telefone">Telefone</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" required placeholder="Digite o telefone">
                                </div>
                                <div class="form-group">
                                    <label for="email_farmacia">Email</label>
                                    <input type="email" class="form-control" id="email_farmacia" name="email_farmacia" required placeholder="Digite o email">
                                </div>
                                <div class="form-group">
                                    <label for="endereco">Endereço</label>
                                    <input type="text" class="form-control" id="endereco" name="endereco" required placeholder="Digite o endereço" autocomplete="off">
                                    <input type="hidden" id="latitude" name="latitude">
                                    <input type="hidden" id="longitude" name="longitude">
                                    <input type="hidden" id="bairro" name="bairro">
                                    <input type="hidden" id="cidade" name="cidade">
                                    <input type="hidden" id="estado" name="estado">
                                    <input type="hidden" id="cep" name="cep">
                                </div>
                                <div class="form-group">
                                    <label for="horario_funcionamento">Horário de Funcionamento</label>
                                    <input type="text" class="form-control" id="horario_funcionamento" name="horario_funcionamento" required placeholder="Digite o horário de funcionamento">
                                </div>
                                <div class="form-group">
                                    <label for="senha">Senha</label>
                                    <input type="password" class="form-control" id="senha" name="senha" required placeholder="Digite uma senha">
                                </div>
                                <button type="submit" class="btn btn-primary">Cadastrar Farmácia</button>
                            </form>


                        </div>
                    </main>

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
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <script src="assets/libs/js/main-js.js"></script>
    <script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <script src="assets/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <script src="assets/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="assets/vendor/charts/morris-bundle/morris.js"></script>
    <script src="assets/vendor/charts/c3charts/c3.min.js"></script>
    <script src="assets/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="assets/vendor/charts/c3charts/C3chartjs.js"></script>
    <script src="assets/libs/js/dashboard-ecommerce.js"></script>
    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPo6v3wr-8pj6Z28WuHNbxk5LOivG6I3o&libraries=places"></script>
    <script>
    function initAutocomplete() {
        const input = document.getElementById('endereco');
        const autocomplete = new google.maps.places.Autocomplete(input);

        // Define os campos que o autocomplete deve retornar
        autocomplete.setFields(['address_component', 'geometry']);

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();

            if (place.geometry) {
                // Preenche as coordenadas de latitude e longitude
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
                
                // Variáveis para armazenar os dados que serão preenchidos
                let bairro = '';
                let cidade = '';
                let estado = '';
                let cep = '';

                // Loop pelos componentes do endereço para capturar as informações necessárias
                for (const component of place.address_components) {
                    const types = component.types;

                    if (types.includes('sublocality') || types.includes('political')) {
                        bairro = component.long_name;
                    }

                    if (types.includes('locality')) {
                        cidade = component.long_name;
                    }

                    if (types.includes('administrative_area_level_1')) {
                        estado = component.short_name;
                    }

                    if (types.includes('postal_code')) {
                        cep = component.long_name;
                    }
                }

                // Preenche os campos ocultos com as informações capturadas
                document.getElementById('bairro').value = bairro;
                document.getElementById('cidade').value = cidade;
                document.getElementById('estado').value = estado;
                document.getElementById('cep').value = cep;

            } else {
                alert("Por favor, selecione um endereço da lista.");
            }
        });
    }

    window.onload = initAutocomplete;
</script>


</body>
</html>
