// Esto es informacion solo de referencia, para que veas como se ve en el html
const pacientes = {
  '101': {
    nombre: 'Juan Perez', sangre: 'O+', edad: '32 años',
    diagnostico: 'Hipertensión',
    tratamiento: 'Control presión y dieta',
    alergias: 'Ninguna',
    cronicas: 'Hipertensión'
  },
  '102': {
    nombre: 'Jose Ortega', sangre: 'A+', edad: '41 años',
    diagnostico: 'Diabetes tipo 2',
    tratamiento: 'Metformina 500mg',
    alergias: 'Penicilina',
    cronicas: 'Diabetes'
  },
  '103': {
    nombre: 'Miguel Camacho', sangre: 'B-', edad: '20 años',
    diagnostico: 'Asma leve',
    tratamiento: 'Salbutamol inhalado',
    alergias: 'Polen',
    cronicas: 'Asma'
  },
  '104': {
    nombre: 'Aldo Moran', sangre: 'AB+', edad: '21 años',
    diagnostico: 'Gastritis',
    tratamiento: 'Omeprazol 20mg',
    alergias: 'Ninguna',
    cronicas: 'Ninguna'
  }
};

document.addEventListener('DOMContentLoaded', () => {
  const selector = document.getElementById('selector-paciente');
  const panel    = document.getElementById('historial-panel');

  selector.addEventListener('change', () => {
    const id = selector.value;
    if (!id) { panel.style.display = 'none'; return; }

    const p = pacientes[id];

    document.getElementById('banner-nombre').textContent  = p.nombre;
    document.getElementById('banner-id').textContent      = `ID: ${id}`;
    document.getElementById('banner-sangre').textContent  = p.sangre;
    document.getElementById('banner-edad').textContent    = p.edad;
    document.getElementById('h-diagnostico').textContent  = p.diagnostico;
    document.getElementById('h-tratamiento').textContent  = p.tratamiento;
    document.getElementById('h-alergias').textContent     = p.alergias;
    document.getElementById('h-cronicas').textContent     = p.cronicas;

    panel.style.display = 'flex';
  });
});