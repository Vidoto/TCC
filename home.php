<?php 
include 'config.php';

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="icon" type="image/png" href="img/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
</head>
<body class="fade-in">

<?php include 'navbar.php'; ?>

<main>
    <div class="info" data-aos="zoom-out" data-aos-duration="1000" data-aos-delay="100" data-aos-offset="200">
        <div class="text">
            <h1>Pacientes e farmácias unidos pela sua saúde</h1>
            <p>Tenha acesso rápido aos seus remédios, utilizando geolocalização!</p>
        </div>
    </div>
        
    <div class="sobre-nos" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200" data-aos-offset="200">
        <div class="sobre-texto">
            <h2>Sobre Nós...</h2>
            <p>Nossa plataforma GKA surgiu quando percebemos a dificuldade dos pacientes em encontrar medicamentos prescritos. Juntos, desenvolvemos uma interface que conecta pacientes e farmácias, facilitando a localização de remédios nas farmácias mais próximas.</p>
        </div>
        <div class="sobre-imagem">
            <img src="https://images.pexels.com/photos/3153201/pexels-photo-3153201.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Imagem sobre nós">
        </div>
    </div>

    <div class="como-estamos" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200" data-aos-offset="200">
        <div class="como-imagem">
            <img src="https://portalhospitaisbrasil.com.br/wp-content/uploads/medicina-2.jpg" alt="Imagem como estamos hoje">
        </div>
        <div class="como-texto">
            <h2>Como estamos hoje?</h2>
            <p>Atualmente a GKA, alcança milhares de pessoas facilitando para elas o acesso a medicamentos, reduzindo o tempo de espera e aumentando a eficiência do tratamento. Pacientes e farmácias se beneficiam da solução, que continua a evoluir com novas funcionalidades e expansão para novas regiões. Nosso compromisso é a inovação contínua, melhorando a saúde e o bem-estar de nossos usuários!</p>
        </div>
    </div>
</main>

<div id="map"></div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAPo6v3wr-8pj6Z28WuHNbxk5LOivG6I3o&libraries=places"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var map;
        var searchInput = document.getElementById('searchInput');
        var searchButton = document.getElementById('searchButton');
        var markers = [];
        var autocomplete;
        var userMarker;
        var infoWindow;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -15.7797, lng: -47.9292},
                zoom: 6
            });

            autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', map);

            infoWindow = new google.maps.InfoWindow();

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(userLocation);
                    map.setZoom(18);

                    userMarker = new google.maps.Marker({
                        position: userLocation,
                        map: map,
                        title: 'Sua localização',
                        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                    });

                    infoWindow.setContent('<div style="font-size: 16px; font-weight: bold;">Você está aqui</div>');
                    infoWindow.open(map, userMarker);
                }, function() {
                    handleLocationError(true, map.getCenter());
                });
            } else {
                handleLocationError(false, map.getCenter());
            }
        }

        searchButton.addEventListener('click', function (event) {
            event.preventDefault();
            searchAndCenterMap();
            document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
        });

        function searchAndCenterMap() {
            var query = searchInput.value;
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({'address': query}, function(results, status) {
                if (status === google.maps.GeocoderStatus.OK && results.length > 0) {
                    var location = results[0].geometry.location;
                    map.setCenter(location);
                    map.setZoom(14);

                    var searchMarker = new google.maps.Marker({
                        position: location,
                        map: map,
                        title: 'Local encontrado'
                    });

                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers = [searchMarker];
                } else {
                    alert('Nenhum lugar encontrado para a pesquisa: ' + query);
                }
            });
        }

        function handleLocationError(browserHasGeolocation, pos) {
            var infoWindow = new google.maps.InfoWindow({
                map: map
            });
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                                  'Erro: O serviço de geolocalização falhou.' :
                                  'Erro: Seu navegador não suporta geolocalização.');
        }

        initMap();
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.body.classList.add('fade-in');

    var links = document.querySelectorAll('a');
    links.forEach(function(link) {
        link.addEventListener('click', function(event) {
            var href = this.getAttribute('href');
            if (href && href !== '#') {
                event.preventDefault();
                document.body.classList.add('fade-out');
                setTimeout(function() {
                    window.location = href;
                }, 500);
            }
        });
    });
});
</script>

<script>
function toggleMenu() {
    const navItems = document.querySelector('.nav-items');
    const isMenuOpen = navItems.clientHeight > 0;

    if (!isMenuOpen) {
        navItems.style.display = 'flex';
        navItems.style.height = '0';
        navItems.style.opacity = '0';
        navItems.style.overflow = 'hidden';

        const fullHeight = navItems.scrollHeight + 'px';

        setTimeout(() => {
            navItems.style.transition = 'height 0.5s ease, opacity 0.5s ease';
            navItems.style.height = fullHeight;
            navItems.style.opacity = '1';
        }, 10);
    } else {
        navItems.style.transition = 'height 0.5s ease, opacity 0.5s ease';
        navItems.style.height = '0';
        navItems.style.opacity = '0';

        setTimeout(() => {
            navItems.style.display = 'none';
        }, 500);
    }
}
</script>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1200,
    });
</script>

</body>
</html>
