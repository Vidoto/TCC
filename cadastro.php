<?php
include 'config.php';

session_start();

$mensagem = "";
$classe_mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $palavra_passe = $_POST['palavra_passe'];
    $default_foto_perfil = "\img\profile.png"; // Imagem padrão (deve estar presente no seu diretório de imagens)

    // Verifica se o email já está cadastrado
    $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $emailExistente = $stmt->fetchColumn();

    if ($emailExistente > 0) {
        $mensagem = "Este email já está cadastrado. Por favor, use outro email.";
        $classe_mensagem = "erro";
    } else {
        $sql = "INSERT INTO usuarios (nome_usuario, email, senha, palavra_passe, foto_perfil) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha, $palavra_passe, $default_foto_perfil]);

        // Verifica se a inserção foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            $userId = $pdo->lastInsertId();

            $_SESSION['id_usuario'] = $userId;
            $_SESSION['nome_usuario'] = $nome;
            $_SESSION['email'] = $email;
            $_SESSION['senha_usuario'] = $senha;
            $_SESSION['palavra_passe'] = $palavra_passe;
            $_SESSION['foto_perfil'] = $default_foto_perfil;

            $mensagem = "Cadastro realizado com sucesso!";
            $classe_mensagem = "sucesso";

            header("Location: home.php");
            exit();
        } else {
            $mensagem = "Erro ao cadastrar. Por favor, tente novamente.";
            $classe_mensagem = "erro";
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
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <style>
        .mensagem {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .sucesso {
            background-color: #DFF2BF;
            color: #4F8A10;
        }

        .erro {
            background-color: #F2DEDE;
            color: #D8000C;
        }
    </style>
    <title>Cadastro</title>
</head>
<body>

<container class="container-login">
    <div class="left-side">
        <img src="img/logo-form.png" width="275" height="">
    </div>

    <div class="right-side">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1>Cadastre-se</h1>

            <?php if (!empty($mensagem)) { ?>
                <div class="mensagem <?php echo $classe_mensagem; ?>"><?php echo $mensagem; ?></div>
            <?php } ?>

            <div class="input-group">
                <input type="text" name="nome" placeholder="" required>
                <label for="name">Nome</label>
            </div>
            
            <div class="input-group">
                <input type="email" name="email" id="email" placeholder=" " />
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                    <input type="password" name="senha" id="password" placeholder=" " required>
                    <label for="password">Senha</label>
                </div>

            <div class="input-group">
                <input type="password" name="palavra_passe" placeholder="" required>
                    <label for="password">Palavra Passe</label>
                </div>
            
            <button type="submit">Cadastrar</button>

            <div class="form-links">
                <a href="login.php">Já possui uma conta? Clique aqui</a>
            </div>
        </form>
    </div>
</container>

</body>
</html>
