document.addEventListener('DOMContentLoaded', () => {
  const lightbox = document.querySelector('.galeria-lightbox');
  const lbImage  = lightbox.querySelector('img');
  const closeBtn = lightbox.querySelector('.close-btn');
  const items    = document.querySelectorAll('.galeria-item');

  items.forEach(item => {
    item.addEventListener('click', () => {
      const bg = getComputedStyle(item).getPropertyValue('background-image');
      const url = bg.slice(5, -2); //extrae URL sin comillas ni url()
      lbImage.src = url;
      lightbox.classList.add('active');
      item.style.transform = 'scale(1.05)';
    });
  });

  const closeLightbox = () => {
    lightbox.classList.remove('active');
    lbImage.src = '';
    document.querySelectorAll('.galeria-item')
      .forEach(i => i.style.transform = '');
  };

  closeBtn.addEventListener('click', closeLightbox);

  lightbox.addEventListener('click', e => {
    if (e.target === lightbox) closeLightbox();
  });
});


