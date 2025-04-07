document.getElementById("contact-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevenir el envío tradicional del formulario

    let formData = new FormData(this);
    formData.append('ajax', true); // Indicamos que es una solicitud AJAX

    //Realizamos la solicitud AJAX
    fetch('contacto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Esperamos una respuesta en formato JSON
    .then(data => {
        const responseMessage = document.getElementById('responseMessage');
        responseMessage.classList.remove('success', 'error'); // Limpiamos posibles clases anteriores

        if (data.status === 'success') {
            responseMessage.classList.add('success');
        } else {
            responseMessage.classList.add('error');
        }

        responseMessage.textContent = data.message; // Mostrar el mensaje de respuesta
    })
    .catch(error => {
        const responseMessage = document.getElementById('responseMessage');
        responseMessage.classList.add('error');
        responseMessage.textContent = 'Hubo un error al enviar el mensaje. Intenta nuevamente.';
    });
});