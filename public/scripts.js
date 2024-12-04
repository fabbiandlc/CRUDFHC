// Muestra u oculta el menú desplegable
function toggleMenu() {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

// Cierra el menú si se hace clic fuera de él
document.addEventListener('click', (event) => {
    const menu = document.getElementById('dropdownMenu');
    const usernameContainer = document.querySelector('.username-container');
    if (!usernameContainer.contains(event.target)) {
        menu.style.display = 'none';
    }
});

// Dibuja una gráfica de barras simple
function drawBarChart() {
    const canvas = document.getElementById('sampleChart');
    const ctx = canvas.getContext('2d');

    // Datos de ejemplo
    const labels = ['January', 'February', 'March', 'April', 'May'];
    const data = [12, 19, 3, 5, 2];

    // Configuración del gráfico
    const barWidth = 50;
    const gap = 20;
    const maxData = Math.max(...data);
    const canvasHeight = canvas.height;
    const canvasWidth = canvas.width;

    // Dibujar ejes
    ctx.beginPath();
    ctx.moveTo(40, 10);
    ctx.lineTo(40, canvasHeight - 30);
    ctx.lineTo(canvasWidth - 10, canvasHeight - 30);
    ctx.stroke();

    // Dibujar barras
    data.forEach((value, index) => {
        const barHeight = (value / maxData) * (canvasHeight - 50);
        const x = 50 + index * (barWidth + gap);
        const y = canvasHeight - 30 - barHeight;

        // Barra
        ctx.fillStyle = 'rgba(54, 162, 235, 0.7)';
        ctx.fillRect(x, y, barWidth, barHeight);

        // Etiqueta de datos
        ctx.fillStyle = '#000';
        ctx.textAlign = 'center';
        ctx.fillText(value, x + barWidth / 2, y - 5);

        // Etiqueta del eje X
        ctx.fillStyle = '#000';
        ctx.fillText(labels[index], x + barWidth / 2, canvasHeight - 10);
    });
}

// Llama a la función para dibujar la gráfica
drawBarChart();
