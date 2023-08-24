
document.addEventListener("DOMContentLoaded", function () {
    // Obtener todas las pestañas
    const tabs = document.querySelectorAll('.tabu');

    // Obtener todos los contenidos de las pestañas
    const tabContents = document.querySelectorAll('.tab-pane');

    // Agregar un evento clic a cada pestaña
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (event) {
            event.preventDefault();

            // Desactivar todas las pestañas y ocultar todos los contenidos de pestañas
            tabs.forEach(function (t) {
                t.classList.remove('active');
            });
            tabContents.forEach(function (content) {
                content.classList.remove('active');
            });

            // Activar la pestaña clickeada y mostrar su contenido correspondiente
            tab.classList.add('active');
            const target = tab.getAttribute('href');
            document.querySelector(target).classList.add('active');
        });
    });



});

/*--------------------------------------------------------------*/
/* Funcion para abrir link dando doble clik a las tablas
/*--------------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", function () {
    const rows = document.querySelectorAll(".clickable-row");
    let lastClickTime = 0;

    rows.forEach(row => {
        row.addEventListener("click", function () {
            const currentTime = new Date().getTime();
            if (currentTime - lastClickTime < 300) {
                const link = row.getAttribute("data-href");
                if (link) {
                    window.location.href = link;
                }
            }
            lastClickTime = currentTime;
        });
    });
});