<?php
// Conexão com o banco de dados
include 'config.php';

// Inicia a sessão
session_start();

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o login é de um administrador
    $sqlAdmin = "SELECT * FROM admin WHERE email_admin = ? AND senha_admin = ?";
    $stmtAdmin = $pdo->prepare($sqlAdmin);
    $stmtAdmin->execute([$email, $senha]);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Cria a sessão do administrador
        $_SESSION['id_admin'] = $admin['id_admin'];
        $_SESSION['nome_admin'] = $admin['nome_admin'];
        $_SESSION['email_admin'] = $admin['email_admin'];
        
        // Redireciona para a página do administrador
        header("Location: admin.php");
        exit();
    } else {
        // Verifica se o login é de uma farmácia
        $sqlFarmacia = "SELECT * FROM farmacias WHERE email_farmacia = ? AND senha_farmacia = ?";
        $stmtFarmacia = $pdo->prepare($sqlFarmacia);
        $stmtFarmacia->execute([$email, $senha]);
        $farmacia = $stmtFarmacia->fetch(PDO::FETCH_ASSOC);

        if ($farmacia) {
            // Cria a sessão da farmácia
            $_SESSION['id_farmacia'] = $farmacia['id_farmacia'];
            $_SESSION['nome_farmacia'] = $farmacia['nome_farmacia'];
            $_SESSION['email_farmacia'] = $farmacia['email_farmacia'];
        
            // Redireciona para a página da farmácia
            header("Location: farmacia.php");
            exit();
        } else {
            // Verifica se o login é de um usuário comum
            $sqlUser = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
            $stmtUser = $pdo->prepare($sqlUser);
            $stmtUser->execute([$email, $senha]);
            $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Cria a sessão do usuário
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nome_usuario'] = $usuario['nome_usuario'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['senha_usuario'] = $usuario['senha'];
                $_SESSION['palavra_passe'] = $usuario['palavra_passe'];
                $_SESSION['foto_perfil'] = $usuario['foto_perfil']; // Adiciona a imagem de perfil à sessão
            
                // Redireciona para a página inicial do usuário
                header("Location: home.php");
                exit();
            } else {
                // Mensagem de erro se as credenciais não forem válidas
                $mensagem = "Email ou senha inválidos. Por favor, tente novamente.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="css/login.css">
    <style>
        .mensagem_erro {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #F2DEDE;
            color: #D8000C;
        }
    </style>
    <title>Login</title>
</head>
<body>

<container class="container-login">
    <div class="left-side">
        <img src="img/logo-form.png" width="" height="">
    </div>

    <div class="right-side">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1>Faça Login</h1>

            <?php if (!empty($mensagem)) { ?>
                <div class="mensagem_erro"><?php echo $mensagem; ?></div>
            <?php } ?>

            <div class="input-group">
                <input type="email" name="email" id="email" placeholder=" " />
                <label for="email">Email</label>
            </div>
            <div class="input-group">
                    <input type="password" name="senha" id="password" placeholder=" " required>
                    <label for="password">Senha</label>
                </div>
            <button type="submit">Login</button>

            <div class="form-links">
                <a href="cadastro.php">Não possui uma conta? Clique aqui</a>
                <a href="recuperar_senha.php">Esqueci minha senha</a>
            </div>
        </form>
    </div>
</container>

</body>
</html>
