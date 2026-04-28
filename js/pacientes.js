document.addEventListener('DOMContentLoaded', () => {
  const vistaLista = document.getElementById('vista-lista');
  const vistaForm  = document.getElementById('vista-form');

  document.getElementById('btn-abrir-form').addEventListener('click', () => {
    vistaLista.style.display = 'none';
    vistaForm.style.display  = 'block';
  });

  document.getElementById('btn-volver').addEventListener('click', () => {
    vistaForm.style.display  = 'none';
    vistaLista.style.display = 'block';
  });
});