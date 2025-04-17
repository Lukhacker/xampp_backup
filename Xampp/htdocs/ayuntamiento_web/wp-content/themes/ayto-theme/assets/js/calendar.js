document.addEventListener("DOMContentLoaded", function () {
    const eventosDiv = document.getElementById("eventos-del-dia");

    // Delegación de eventos: escucha clicks en el calendario
    document.querySelector(".eo-widget-cal-wrap").addEventListener("click", function (e) {
        const target = e.target;

        // Si el clic fue en un enlace con evento
        if (target.tagName === "A" && target.closest("td")?.classList.contains("eo-event-future")) {
            e.preventDefault();

            const url = target.getAttribute("href");

            // Limpia la info anterior
            eventosDiv.innerHTML = "";
            eventosDiv.classList.remove("mostrar-evento");

            // Fetch al contenido del evento
            fetch(url)
                .then((res) => res.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");

                    // Puedes ajustar este selector según el contenido real
                    const contenidoEvento = doc.querySelector(".entry-content") || doc.querySelector("article") || doc.body;

                    eventosDiv.innerHTML = contenidoEvento.innerHTML;

                    // Aplica clase para transición
                    setTimeout(() => {
                        eventosDiv.classList.add("mostrar-evento");
                    }, 50);
                })
                .catch((error) => {
                    eventosDiv.innerHTML = "<p>Error al cargar el evento.</p>";
                    console.error("Error al cargar el evento:", error);
                });
        }
    });
});
