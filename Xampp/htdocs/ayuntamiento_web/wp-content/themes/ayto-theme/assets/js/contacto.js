document.getElementById("contact-form").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    formData.append('ajax', true);

    fetch('contacto.php', {
        method: 'POST',
        body: formData
    })
    
    .then(response => response.json())
    .then(data => {
        const responseMessage = document.getElementById('responseMessage');
        responseMessage.classList.remove('success', 'error');

        if (data.status === 'success') {
            responseMessage.classList.add('success');
        } else {
            responseMessage.classList.add('error');
        }

        responseMessage.textContent = data.message;
        responseMessage.style.display = 'block';
    })
    .catch(error => {
        const responseMessage = document.getElementById('responseMessage');
        responseMessage.classList.remove('success');
        responseMessage.classList.add('error');
        responseMessage.textContent = 'Hubo un error al enviar el mensaje. Intenta nuevamente.';
        responseMessage.style.display = 'block';
    });
});
