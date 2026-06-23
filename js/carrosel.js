function iniciarCarrosselBanners() {
    var carrossel = document.getElementById('carrossel-banners');
    if (!carrossel) {
        return; // página atual não tem carrossel (ex: produto.php, carrinho.php)
    }

    var slides = carrossel.querySelectorAll('.banner-slide');
    var pontos = carrossel.querySelectorAll('.ponto');
    var indiceAtual = 0;
    var TEMPO_AUTOPLAY_MS = 6000;
    var temporizador = null;

    function irParaSlide(indice) {
        slides.forEach(function (slide, i) {
            slide.classList.toggle('banner-slide--ativo', i === indice);
        });
        pontos.forEach(function (ponto, i) {
            ponto.classList.toggle('ponto--ativo', i === indice);
        });
        indiceAtual = indice;
    }

    function proximoSlide() {
        var proximo = (indiceAtual + 1) % slides.length;
        irParaSlide(proximo);
    }

    function reiniciarAutoplay() {
        if (temporizador) {
            clearInterval(temporizador);
        }
        temporizador = setInterval(proximoSlide, TEMPO_AUTOPLAY_MS);
    }

    pontos.forEach(function (ponto) {
        ponto.addEventListener('click', function () {
            var indice = parseInt(ponto.getAttribute('data-ir-para-slide'), 10);
            irParaSlide(indice);
            reiniciarAutoplay();
        });
    });

    if (slides.length > 1) {
        reiniciarAutoplay();
    }
}