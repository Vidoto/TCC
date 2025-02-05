<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
    <link rel="stylesheet" href="css/navbar.css">
</head>
<body>

<div class="navbar">
    <header>
        <div class="logo">
            <img id="logo-img" src="img/logo.png" alt="Logo" width="55px" height="">
            <img id="logo-txt" src="img/gka.png" alt="gka" width="75px" height="">
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="searchInput" placeholder="Pesquisar farmácia">
            <button id="searchButton" class="searchButton"><i class="fas fa-search"></i></button>
        </div>

        <button class="menu-toggle" aria-label="Abrir Menu" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>

        <div class="user">
            <?php if(isset($_SESSION['nome_usuario'])) { ?>
                <div class="nome_usuario">Bem-vindo, <br><?php echo $_SESSION['nome_usuario']; ?>!</div>
            <?php } ?>

            <a href="usuario_info.php">
                <div class="profile">
                    <img src="<?php echo isset($_SESSION['foto_perfil']) ? 'uploaded_images/' . $_SESSION['foto_perfil'] : 'img/profile.png'; ?>" alt="Foto de Perfil">
                </div>
            </a>
        </div>
    </header>

    <nav class="nav-items">
        <a href="home.php">Home</a>
        <a href="historico.php">Histórico</a>
        <a href="scan.php">Escanear receita</a>
        <a href="exames.php">Exames</a>
    </nav>
    </div>

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
</body>
</html>
