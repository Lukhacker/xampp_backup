document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('.post-thumbnail');
    
    images.forEach(image => {
        const width = image.naturalWidth;
        const height = image.naturalHeight;

        if (width > height) {
            //Si la imagen es apaisada
            image.style.aspectRatio = '16/9';
        } else {
            //Si la imagen es vertical
            image.style.aspectRatio = '4/5';
        }
    });
});
