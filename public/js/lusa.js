// Comportamiento del panel. Todo el JS vive aqui (sin scripts en linea) para
// poder usar una politica CSP estricta.
(function () {
  'use strict';

  const palette = {
    blue: '#1d5fad', sky: '#3a8fd9', gold: '#e8a814', green: '#2e9e6a',
    purple: '#6e4fb8', red: '#c94a4a', ink: '#1b2333', muted: '#c3cad6'
  };
  const serie = [palette.blue, palette.gold, palette.green, palette.purple, palette.sky, palette.red, palette.ink];

  // Menu lateral en pantallas chicas
  document.querySelectorAll('[data-toggle-sidebar]').forEach((btn) => {
    btn.addEventListener('click', () => document.querySelector('.sidebar')?.classList.toggle('abierto'));
  });

  // Confirmacion antes de enviar formularios destructivos
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    });
  });

  // Inventario: los campos de equipo solo aplican a ciertas categorias
  document.querySelectorAll('[data-categoria-select]').forEach((select) => {
    const equipos = JSON.parse(select.dataset.categoriaSelect || '[]');
    const actualizar = () => {
      const esEquipo = equipos.includes(select.value);
      document.querySelectorAll('[data-solo-equipo]').forEach((el) => {
        el.classList.toggle('d-none', !esEquipo);
        el.querySelectorAll('input').forEach((i) => { i.required = esEquipo && i.dataset.requerido === '1'; });
      });
    };
    select.addEventListener('change', actualizar);
    actualizar();
  });

  // Tecnologias: muestra solo los campos del tipo elegido
  document.querySelectorAll('[data-tipo-select]').forEach((select) => {
    const actualizar = () => {
      document.querySelectorAll('[data-campos-tipo]').forEach((grupo) => {
        const visible = grupo.dataset.camposTipo === select.value;
        grupo.classList.toggle('d-none', !visible);
        grupo.querySelectorAll('input, select, textarea').forEach((i) => { i.disabled = !visible; });
      });
    };
    select.addEventListener('change', actualizar);
    actualizar();
  });

  // Seleccionar todas las unidades en el tablero de mantenimiento
  document.querySelectorAll('[data-check-all]').forEach((master) => {
    const selector = master.dataset.checkAll;
    const contador = document.querySelector('[data-check-count]');
    const items = () => document.querySelectorAll(selector);
    const contar = () => {
      const n = Array.from(items()).filter((i) => i.checked).length;
      if (contador) contador.textContent = n;
      document.querySelectorAll('[data-requiere-seleccion]').forEach((b) => { b.disabled = n === 0; });
    };
    master.addEventListener('change', () => { items().forEach((i) => { i.checked = master.checked; }); contar(); });
    items().forEach((i) => i.addEventListener('change', contar));
    contar();
  });

  // Botones que abren un modal y le asignan la accion del formulario
  document.querySelectorAll('[data-modal-action]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const modal = document.querySelector(btn.dataset.bsTarget);
      const form = modal?.querySelector('form');
      if (form) form.action = btn.dataset.modalAction;
      const titulo = modal?.querySelector('[data-modal-titulo]');
      if (titulo && btn.dataset.modalTitulo) titulo.textContent = btn.dataset.modalTitulo;
    });
  });

  // Firmas manuscritas en canvas (mouse, touch y lapiz)
  document.querySelectorAll('[data-firma]').forEach((caja) => {
    const canvas = caja.querySelector('canvas');
    const input = caja.querySelector('input[type="hidden"]');
    const limpiar = caja.querySelector('[data-firma-limpiar]');
    const ctx = canvas.getContext('2d');
    let dibujando = false;
    let trazado = false;

    const ajustar = () => {
      const r = canvas.getBoundingClientRect();
      const escala = window.devicePixelRatio || 1;
      canvas.width = r.width * escala;
      canvas.height = r.height * escala;
      ctx.setTransform(escala, 0, 0, escala, 0, 0);
      ctx.lineWidth = 2.2;
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = palette.ink;
    };
    const punto = (e) => {
      const r = canvas.getBoundingClientRect();
      return { x: e.clientX - r.left, y: e.clientY - r.top };
    };
    canvas.addEventListener('pointerdown', (e) => {
      dibujando = true; trazado = true;
      canvas.setPointerCapture(e.pointerId);
      const p = punto(e); ctx.beginPath(); ctx.moveTo(p.x, p.y);
    });
    canvas.addEventListener('pointermove', (e) => {
      if (!dibujando) return;
      const p = punto(e); ctx.lineTo(p.x, p.y); ctx.stroke();
    });
    const terminar = () => {
      if (!dibujando) return;
      dibujando = false;
      if (trazado) input.value = canvas.toDataURL('image/png');
    };
    canvas.addEventListener('pointerup', terminar);
    canvas.addEventListener('pointerleave', terminar);
    limpiar?.addEventListener('click', () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      input.value = ''; trazado = false;
    });
    ajustar();
    window.addEventListener('resize', () => { if (!trazado) ajustar(); });
  });

  // Codigos QR: <div data-qr="TOKEN"> se dibuja como SVG (libreria qrcode-generator)
  document.querySelectorAll('[data-qr]').forEach((caja) => {
    if (typeof window.qrcode !== 'function') {
      caja.textContent = 'No se pudo cargar el generador de QR (revisa la conexion a internet).';
      caja.classList.add('small', 'text-danger');
      return;
    }
    const qr = window.qrcode(0, 'M');
    qr.addData(caja.dataset.qr);
    qr.make();
    caja.innerHTML = qr.createSvgTag({ cellSize: 6, margin: 2, scalable: true });
  });

  // Boton de impresion (la hoja de QR se guarda como PDF desde el dialogo del navegador)
  document.querySelectorAll('[data-imprimir]').forEach((btn) => btn.addEventListener('click', () => window.print()));

  // Graficas declarativas: <canvas data-chart='{"type":"bar","labels":[],"data":[]}'>
  if (window.Chart) {
    Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;
    Chart.defaults.color = '#7a8496';
    Chart.defaults.borderColor = '#eef1f5';
    document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
      const cfg = JSON.parse(canvas.dataset.chart);
      const circular = cfg.type === 'doughnut';
      const colores = cfg.colors || (circular ? serie : [palette[cfg.color || 'blue']]);
      new Chart(canvas, {
        type: cfg.type,
        data: {
          labels: cfg.labels,
          datasets: [{
            data: cfg.data,
            backgroundColor: circular || cfg.type === 'bar' ? colores : 'rgba(29,95,173,.08)',
            borderColor: circular ? '#fff' : colores[0],
            borderWidth: circular ? 2 : 2,
            borderRadius: cfg.type === 'bar' ? 6 : 0,
            fill: cfg.type === 'line',
            tension: .35,
            pointRadius: cfg.type === 'line' ? 2 : 0,
            maxBarThickness: 36
          }]
        },
        options: {
          maintainAspectRatio: false,
          indexAxis: cfg.horizontal ? 'y' : 'x',
          cutout: circular ? '68%' : undefined,
          plugins: { legend: { display: circular, position: 'bottom', labels: { boxWidth: 10, usePointStyle: true } } },
          scales: circular ? {} : {
            x: { grid: { display: false } },
            y: { beginAtZero: true, ticks: { precision: 0 } }
          }
        }
      });
    });
  }
})();
