document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelector('.slides-js');
    const prevBtn = document.querySelector('.slider-btn.prev');
    const nextBtn = document.querySelector('.slider-btn.next');
  
    let currentSlide = 0;
    const totalSlides = document.querySelectorAll('.slide-js').length;
  
    function updateSlider() {
      slides.style.transform = `translateX(-${currentSlide * 100}%)`;
      prevBtn.style.display = currentSlide === 0 ? 'none' : 'block';
      nextBtn.style.display = currentSlide === totalSlides - 1 ? 'none' : 'block';
    }
  
    prevBtn.addEventListener('click', () => {
      if (currentSlide > 0) {
        currentSlide--;
        updateSlider();
      }
    });
  
    nextBtn.addEventListener('click', () => {
      if (currentSlide < totalSlides - 1) {
        currentSlide++;
        updateSlider();
      }
    });
  
    updateSlider();
});