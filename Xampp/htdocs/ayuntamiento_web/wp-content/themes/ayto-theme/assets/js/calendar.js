document.addEventListener("DOMContentLoaded", function () {
    const eventosDiv = document.getElementById("eventos-del-dia");

    document.querySelector(".eo-widget-cal-wrap").addEventListener("click", function (e) {
        const target = e.target;

        //Cualquier día con evento (pasado, presente o futuro) para que se muestren todos en el calendario
        if (target.tagName === "A" && target.closest("td")?.classList.contains("event")) {
            e.preventDefault();

            const url = target.getAttribute("href");

            eventosDiv.innerHTML = "";
            eventosDiv.classList.remove("mostrar-evento");

            fetch(url)
                .then((res) => res.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");

                    const contenidoEvento = doc.querySelector(".entry-content") || doc.querySelector("article") || doc.body;

                    eventosDiv.innerHTML = contenidoEvento.innerHTML;

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
