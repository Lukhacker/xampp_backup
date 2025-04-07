document.addEventListener("DOMContentLoaded", () => {
    //Encuentra la lista de navegación
    const listNav = document.querySelector('.list-nav');

    //Me aseguro de que existe antes de modificarla
    if (listNav) {
        //Agrego la clase para habilitar transiciones
        listNav.classList.add('menu-initialized');
    }
});