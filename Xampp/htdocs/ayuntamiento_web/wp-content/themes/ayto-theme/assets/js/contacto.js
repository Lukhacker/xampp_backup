document.getElementById("contact-form").addEventListener("submit", function (e) {
    e.preventDefault(); // Evita el envío normal

    const area = document.getElementById("area").value;
    const nombre = document.getElementById("nombre").value;
    const email = document.getElementById("email").value;
    const mensaje = document.getElementById("mensaje").value;

    //Correos según el área seleccionada
    const destinatarios = {
      ayuntamiento: "atencionciudadana@ayuntamientolazubia.com",
      mayores: "mayores@ayuntamientolazubia.com",
      juventud: "juventudlazubia@gmail.com",
      cultura: "cultura@ayuntamientolazubia.com"
    };

    const destino = destinatarios[area];

    const asunto = encodeURIComponent("Consulta para el área de " + area);
    const cuerpo = encodeURIComponent(
      `${mensaje}`
    );

    //Abre el cliente de correo con los datos
    window.location.href = `mailto:${destino}?subject=${asunto}&body=${cuerpo}`;
});