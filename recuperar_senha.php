<?php
include 'config.php';

session_start();

$senhaErr = $confirmarSenhaErr = $palavraPasseErr = $emailErr = "";
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['email'])) {
        $emailErr = "Por favor, insira seu email.";
    } else {
        $email = $_POST['email'];
    }

    if (empty($_POST['senha'])) {
        $senhaErr = "Por favor, digite sua nova senha.";
    } else {
        $novaSenha = $_POST['senha'];
    }

    if (empty($_POST['confirmar_senha'])) {
        $confirmarSenhaErr = "Por favor, confirme sua nova senha.";
    } else {
        $confirmarSenha = $_POST['confirmar_senha'];
    }

    if (empty($_POST['palavra_passe'])) {
        $palavraPasseErr = "Por favor, digite sua palavra-passe.";
    } else {
        $palavraPasse = $_POST['palavra_passe'];
    }

    if (!empty($email) && !empty($novaSenha) && !empty($confirmarSenha) && !empty($palavraPasse) && $novaSenha === $confirmarSenha) {
        $query = "SELECT id_usuario FROM usuarios WHERE email = ? AND palavra_passe = ?";
        if ($stmt = $pdo->prepare($query)) {
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $palavraPasse);

            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $queryUpdate = "UPDATE usuarios SET senha = ? WHERE email = ?";
                    if ($stmtUpdate = $pdo->prepare($queryUpdate)) {
                        if ($stmtUpdate->execute([$novaSenha, $email])) {
                            $mensagem = "Senha atualizada com sucesso!";
                        } else {
                            $mensagem = "Erro ao atualizar a senha. Por favor, tente novamente mais tarde.";
                        }
                    }
                } else {
                    $palavraPasseErr = "Email ou palavra-passe incorretos.";
                }
            } else {
                $mensagem = "Algo deu errado. Por favor, tente novamente mais tarde.";
            }
        }
    } elseif (!empty($novaSenha) && !empty($confirmarSenha)) {
        $confirmarSenhaErr = "As senhas não coincidem. Por favor, tente novamente.";
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
        .mensagem_erro{
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #F2DEDE;
            color: #D8000C;
        }

        .mensagem {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #DFF2BF;
            color: #4F8A10;
        }
    </style>
    <title>Recuperar Senha</title>
</head>
<body>

<container class="container-login">
    <div class="left-side">
        <img src="img/logo-form.png" width="275" height="">
    </div>

    <div class="right-side">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h1>Recuperar Senha</h1>
            <?php if(!empty($mensagem)) { ?>
                <div class="mensagem"><?php echo $mensagem; ?></div>
            <?php } ?>

            <?php if(!empty($emailErr)) { ?>
                <div class="mensagem_erro"><?php echo $emailErr; ?></div>
            <?php } ?>
            <?php if(!empty($senhaErr)) { ?>
                <div class="mensagem_erro"><?php echo $senhaErr; ?></div>
            <?php } ?>
            <?php if(!empty($confirmarSenhaErr)) { ?>
                <div class="mensagem_erro"><?php echo $confirmarSenhaErr; ?></div>
            <?php } ?>
            <?php if(!empty($palavraPasseErr)) { ?>
                <div class="mensagem_erro"><?php echo $palavraPasseErr; ?></div>
            <?php } ?>

            <div class="input-group">
                <input type="email" name="email" id="email" placeholder=" " />
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                    <input type="password" name="senha" id="password" placeholder=" " required>
                    <label for="password">Nova senha</label>
                </div>

                <div class="input-group">
                    <input type="password" name="confirmar senha" id="password" placeholder=" " required>
                    <label for="password">Reescreva senha</label>
                </div>

                <div class="input-group">
                <input type="password" name="palavra passe" placeholder="" required>
                    <label for="password">Palavra Passe</label>
                </div>

            <button type="submit">Alterar Senha</button>

            <div class="form-links">
                <a href="login.php">Voltar</a>
            </div>
        </form>
    </div>
</container>

</body>
</html>
