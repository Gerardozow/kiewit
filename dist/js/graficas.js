// script.js
var ctx = document.getElementById('ventas').getContext('2d');

// Obtener los datos de ventas desde PHP
fetch('get_sales_data.php')
    .then(response => response.json())
    .then(data => {
        var productos = [];
        var ventas = [];

        data.forEach(item => {
            productos.push(item.producto);
            ventas.push(item.ventas);
        });

        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: productos,
                datasets: [{
                    label: 'Ventas',
                    data: ventas,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });