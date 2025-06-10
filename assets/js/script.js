let indice = 0;
const slides = document.querySelectorAll(".slide");
const indicadores = document.querySelectorAll(".indicadores span");

function mostrarSlide(i) {
  slides.forEach(slide => slide.classList.remove("ativo"));
  indicadores.forEach(dot => dot.classList.remove("ativo"));
  slides[i].classList.add("ativo");
  indicadores[i].classList.add("ativo");
  indice = i;
}

function avancarSlide() {
  let novo = (indice + 1) % slides.length;
  mostrarSlide(novo);
}

function voltarSlide() {
  let novo = (indice - 1 + slides.length) % slides.length;
  mostrarSlide(novo);
}

function mudarSlide(i) {
  mostrarSlide(i);
}

// Inicia o carrossel automático
setInterval(avancarSlide, 8000);
  
