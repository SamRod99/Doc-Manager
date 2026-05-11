document.addEventListener('DOMContentLoaded', () => {
    const vistaReporte = document.getElementById('vista-reporte');
    const vistaForm    = document.getElementById('vista-form');

    // Alternar vistas
    document.getElementById('btn-abrir-form').addEventListener('click', () => {
        vistaReporte.style.display = 'none';
        vistaForm.style.display    = 'block';
    });

    document.getElementById('btn-volver').addEventListener('click', () => {
        vistaForm.style.display    = 'none';
        vistaReporte.style.display = 'block';
    });

    // Tabs método de pago
    document.querySelectorAll('.metodo-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.metodo-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('metodo').value = tab.dataset.metodo;
        });
    });
});