gsap.registerPlugin(ScrollTrigger);

//Función para las columnas que se mueven hacia la izquierda
function animateLeft(selector) {
  gsap.from(selector, {
    opacity: 0,
    duration: 1,
    x: -500,
    scrollTrigger: {
      trigger: selector,
      start: "top 90%",
    },
  });
}

//Función para las columnas que se mueven hacia la derecha
function animateRight(selector) {
  gsap.from(selector, {
    opacity: 0,
    duration: 1,
    x: 500,
    scrollTrigger: {
      trigger: selector,
      start: "top 90%",
    },
  });
}

//Función para los títulos
function titleAnimation(selector) {
    gsap.from(selector, {
        opacity: 0,
        duration: 1,
        x: -500,
        scrollTrigger: {
            trigger: selector,
            start: "top 90%",
        },
    });
}

//Función para las columnas que se mueven hacia arriba
function animateUp(selector) {
    gsap.from(selector, {
        opacity: 0,
        duration: 1,
        y: 300,
        scrollTrigger: {
            trigger: selector,
            start: "top 100%",
        },
    });
}

/*---------------INICIO---------------*/
//News
titleAnimation(".news-container h1");

//Aplico animaciones alternas a .news-item
const newsItems = document.querySelectorAll(".news-item");

newsItems.forEach((item, index) => {
  if ((index + 1) % 2 === 1) {
    //Índices impares de la izquierda
    animateLeft(item);
  } else {
    //Índices pares de la derecha
    animateRight(item);
  }
});

//Intro
titleAnimation(".intro-container h1");
animateLeft(".intro-row1")
animateRight(".intro-row2")

//Visitar
titleAnimation(".visitar-container h1");
animateUp(".visitar-link");

//Eventos
titleAnimation(".eventos-container h1");
animateRight("#eo_shortcode_calendar_0");

//Galeria
titleAnimation(".galeria-container h1");
animateLeft(".item1");
animateRight(".item2");
animateLeft(".item3");
animateUp(".item4");
animateLeft(".item5");
animateUp(".item6");
animateRight(".item7");
animateLeft(".item8");
animateRight(".item9");
animateLeft(".item10");
animateRight(".item11");
animateLeft(".item12");
animateRight(".item13");

/*---------------ÁREAS---------------*/
//Turismo
titleAnimation(".turismo-container h1");
animateLeft(".turismo-column1");
animateRight(".turismo-column2");

//Juventud
titleAnimation(".juventud-container h1");
animateLeft(".juventud-column1");
animateRight(".juventud-column2");

//Servicios
titleAnimation(".servicios-container h1");
animateLeft(".slider-js");

//Cultura
titleAnimation(".cultura-container h1");
animateLeft(".cultura-column1");
animateRight(".cultura-column2");

//Deportes
titleAnimation(".deportes-container h1");
animateLeft(".deportes-content");

//Fiestas
titleAnimation(".fiestas-container h1");
animateLeft(".fiestas-column1");
animateRight(".fiestas-column2");

/*---------------EVENTOS---------------*/
animateLeft(".events-item");

/*---------------CONTACTO---------------*/
//Info
titleAnimation(".info-container h1");
animateRight(".info-item");

//Instalaciones
titleAnimation(".instalaciones-container h1");
animateLeft(".instalaciones-item1");
animateRight(".instalaciones-item2");
animateLeft(".instalaciones-item3");
animateRight(".instalaciones-item4");

//Asistente
titleAnimation(".asistente-container h1");
animateRight(".wpaicg-chat-shortcode-content");

//Formulario
titleAnimation(".formulario-title h1");
animateRight(".formulario-container");