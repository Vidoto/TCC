<?php
require_once 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $feedback = $_POST['feedback'] ?? '';
    
    if (empty($name) || empty($email) || empty($feedback)) {
        $message = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Por favor, forneça um endereço de e-mail válido.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO user_feedback (name, email, feedback_text) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $feedback]);
            
            $message = "Feedback enviado com sucesso! Obrigado por sua contribuição.";
            
            $_POST = array();
        } catch(PDOException $e) {
            $message = "Erro ao enviar feedback: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GKA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    
</head>
<body class="bg-gray-100">
    <header class="header">
        <div class="container mx-auto flex justify-between items-center">
            <img id="logo-txt" src="img/gka.png" alt="gka" width="75px" height="">
            <nav>
                <ul class="flex space-x-4">
                    <li><a href="#main" class="hover:text-blue-200">Início</a></li>
                    <li><a href="#about" class="hover:text-blue-200">Sobre</a></li>
                    <li><a href="#userFeedback" class="hover:text-blue-200">Contato</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main" class="container">
        <section class="slider mb-8">

            <div class="slide-text">
                <h2>Bem-vindo à GKA</h2>
                <p>Pacientes e farmácias unidos pela sua saúde  </p>
                <div class="slide-buttons">
                    <button class="slide-btn btn-primary" onclick="window.location.href='login.php'">Login</button>
                    <button class="slide-btn btn-secondary" onclick="window.location.href='cadastro.php'">Cadastro</button>
                </div>
            </div>

            <div class="slide active">
                <img src="https://images.pexels.com/photos/5726835/pexels-photo-5726835.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Slide 1" class="w-full h-full object-cover">
            </div>
            <div class="slide">
                <img src="https://images.pexels.com/photos/2280571/pexels-photo-2280571.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Slide 2" class="w-full h-full object-cover">
            </div>
            <div class="slide">
                <img src="https://images.pexels.com/photos/356040/pexels-photo-356040.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Slide 3" class="w-full h-full object-cover">
            </div>
            <div class="slide">
                <img src="https://images.pexels.com/photos/40568/medical-appointment-doctor-healthcare-40568.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Slide 3" class="w-full h-full object-cover">
            </div>
            
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="card" id="about">
        <h2 class="text-xl font-semibold mb-2">Escaneamento de Receitas</h2>
        <p>Os usuários podem facilmente escanear ou fazer upload de suas receitas médicas diretamente no sistema. Essas receitas são então automaticamente enviadas para todas as farmácias registradas na plataforma. </p>
    </div>
    <div class="card">
        <h2 class="text-xl font-semibold mb-2">Resposta das Farmácias</h2>
        <p>As farmácias têm acesso a uma página exclusiva onde podem visualizar todas as receitas recebidas. Elas respondem rapidamente, informando a disponibilidade dos medicamentos, preços e possíveis alternativas, como genéricos.</p>
    </div>
    <div class="card">
    <h2 class="text-xl font-semibold mb-2">Comparação de Preços</h2>
    <p>Os usuários podem avaliar rapidamente as opções disponíveis, considerando não apenas os preços, mas também a proximidade das farmácias e outras vantagens, como promoções ou alternativas sugeridas.</p>
</div>
        </section>

        <!-- Estatísticas-->

        <section class="statistics-section">
  <div class="statistics-container">
    <div class="statistics-wrapper">
      <!-- Farmácias Parceiras -->
      <div class="stat-item">
        <div class="icon">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
            <circle cx="12" cy="12" r="10"></circle>
            <circle cx="12" cy="12" r="6"></circle>
            <circle cx="12" cy="12" r="2"></circle>
          </svg>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">100+</h3>
          <p class="stat-label">Farmácias Parceiras</p>
        </div>
      </div>

      <!-- Divisor -->
      <div class="stat-divider"></div>

      <!-- Usuários Satisfeitos -->
      <div class="stat-item">
        <div class="icon">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">5000+</h3>
          <p class="stat-label">Usuários Satisfeitos</p>
        </div>
      </div>

      <!-- Divisor -->
      <div class="stat-divider"></div>

      <!-- Taxa de Satisfação -->
      <div class="stat-item">
        <div class="icon">
          <svg class="stat-icon" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
            <polyline points="17 6 23 6 23 12"></polyline>
          </svg>
        </div>
        <div class="stat-content">
          <h3 class="stat-number">90%</h3>
          <p class="stat-label">Taxa de Satisfação</p>
        </div>
      </div>
    </div>
  </div>
</section>

        <section>
    <div class="container-prob">
        <div class="prob">
            <img src="https://img.freepik.com/fotos-gratis/mulher-jovem-usando-tecnologia-domestica_23-2149216624.jpg" alt="pessoa acessando">
            <p class="textop">Receitas enviadas com um clique</p>
        </div>
        <div class="prob">
            <img src="https://blog.ipog.edu.br/wp-content/uploads/2018/02/Acompanhamento-farmacoterap%C3%AAutico.jpg" alt="mapa">
            <p class="textop">Respostas rápidas e precisas das farmácias parceiras</p>
        </div>
        <div class="prob">
            <img src="img/mapa.png" alt="Descrição da imagem">
            <p class="textop">As farmácias mais próximas de você!</p>
        </div>
        <div class="prob">
            <img src="https://images.pexels.com/photos/5910953/pexels-photo-5910953.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Descrição da imagem">
            <p class="textop">Compare preços e encontre os melhores remédios</p>
        </div>
    </div>
</section>


        <section class="feedback-form container mx-auto mt-8 mb-8">
            
            <form id="userFeedback" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <h2 class="text-2xl font-bold mb-4">Deixe seu Feedback</h2>
            <?php
            if (!empty($message)) {
                echo "<p class='sucesso'>{$message}</p>";
            }
            ?>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Nome
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Seu nome" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        E-mail
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" placeholder="seu@email.com" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="feedback">
                        Seu Feedback
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="feedback" name="feedback" placeholder="Nos conte o que você achou..." rows="4" required></textarea>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                        Enviar Feedback
                    </button>
                </div>
            </form>
        </section>
    </main>
    <br><br>

    <?php include 'footer.php'; ?>

        <script>
            let currentSlide = 0;
            const slides = document.querySelectorAll('.slide');
    
            function changeSlide(direction) {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + direction + slides.length) % slides.length;
                slides[currentSlide].classList.add('active');
            }
    
            setInterval(() => changeSlide(1), 3000);
        </script>
</body>
</html>