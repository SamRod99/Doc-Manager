document.addEventListener('DOMContentLoaded', () => {
  const vistaLista = document.getElementById('vista-lista');
  const vistaForm  = document.getElementById('vista-form');

  // Alternar vistas
  document.getElementById('btn-abrir-form').addEventListener('click', () => {
    vistaLista.style.display = 'none';
    vistaForm.style.display  = 'block';
  });

  document.getElementById('btn-volver').addEventListener('click', () => {
    vistaForm.style.display  = 'none';
    vistaLista.style.display = 'block';
  });

  // Filtro por tabs
  document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const filtro = tab.dataset.filter;
      document.querySelectorAll('tbody tr').forEach(row => {
        row.style.display =
          filtro === 'todas' || row.dataset.estado === filtro ? '' : 'none';
      });
    });
  });
});