
  let slides = document.querySelectorAll('.slide');
  let currentSlide = 0;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.remove('ativo');
      if (i === index) {
        slide.classList.add('ativo');
      };
    })
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  // inicia o carrosel automatico
  setInterval(nextSlide, 4000); 

