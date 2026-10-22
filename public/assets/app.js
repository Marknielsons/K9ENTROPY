(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const canvas = $('wave-canvas');
  const ctx = canvas.getContext('2d');
  const colors = ['#b4ee6d', '#78ddc3', '#b6d96a', '#77aeb0'];
  let latest = null;
  let sampleValue = '';
  let lastError = false;
  function resize() {
    const ratio = Math.min(window.devicePixelRatio || 1, 2);
    const box = canvas.getBoundingClientRect();
    canvas.width = Math.max(1, Math.floor(box.width * ratio));
    canvas.height = Math.max(1, Math.floor(box.height * ratio));
    draw();
  }
  function draw() {
    if (!ctx || !latest) return;
    const w = canvas.width, h = canvas.height;
    ctx.clearRect(0, 0, w, h);
    const data = latest.signal.channels;
    ['CH1','CH2','CH3','CH4'].forEach((name, c) => {
      const samples = data[name] || [];
      if (!samples.length) return;
      const baseline = (c + .5) * h / 4;
      const scale = Math.min(h / 360, 1.9 * (window.devicePixelRatio || 1));
      ctx.beginPath();
      samples.forEach((v, i) => {
        const x = (i / (samples.length - 1)) * w;
        const y = baseline - v * scale;
        if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
      });
      ctx.lineCap = 'round';
      ctx.lineJoin = 'round';
      ctx.strokeStyle = colors[c];
      ctx.lineWidth = 1.5 * Math.min(window.devicePixelRatio || 1, 2);
      ctx.shadowColor = colors[c];
      ctx.shadowBlur = 8;
      ctx.globalAlpha = .95;
      ctx.stroke();
      ctx.globalAlpha = 1;
      ctx.shadowBlur = 0;
    });
  }
  async function refresh() {
    try {
      const response = await fetch('/api/status', {cache:'no-store'});
      if (!response.ok) throw Error('API status ' + response.status);
      latest = await response.json();
      const simulated = latest.source === 'simulation';
      $('metric-source').textContent = simulated ? 'SIMULATED' : 'EEG INPUT';
      $('metric-source-detail').innerHTML = simulated ? '<span class="mini-dot amber"></span> No EEG adapter connected' : '<span class="mini-dot amber"></span> Adapter input, unverified';
      $('channel-count').textContent = String(latest.signal.channel_count).padStart(2, '0');
      $('feed-mode').textContent = simulated ? 'SIMULATION' : 'ADAPTER · UNVERIFIED';
      $('signal-sample-count').textContent = latest.signal.samples_per_channel + ' SAMPLES / CHANNEL';
      $('signal-time').textContent = new Date(latest.received_at).toLocaleTimeString();
      $('mean-amplitude').textContent = 'MEAN |AMPLITUDE|: ' + latest.signal.mean_absolute_amplitude_uv.toFixed(1) + ' μV';
      if (lastError) $('random-state').textContent = 'Signal feed recovered';
      lastError = false;
      draw();
    } catch (e) {
      $('feed-mode').textContent = 'SIGNAL UNAVAILABLE';
      lastError = true;
    }
  }
  async function generate() {
    const btn = $('generate-btn');
    btn.disabled = true;
    $('random-state').textContent = 'Requesting secure sample…';
    try {
      const response = await fetch('/api/random?bytes=16', {cache:'no-store'});
      if (!response.ok) throw Error('Random sample unavailable');
      const data = await response.json();
      if (data.source !== 'operating_system_csprng' || data.brain_data_used !== false || !/^[0-9a-f]{32}$/.test(data.hex)) throw Error('Unexpected response');
      sampleValue = data.hex;
      $('random-output').textContent = sampleValue;
      $('random-state').textContent = 'From OS CSPRNG, not EEG';
    } catch (err) {
      $('random-state').textContent = 'Unable to generate sample';
    } finally {
      btn.disabled = false;
    }
  }
  async function copy() {
    if (!sampleValue) return;
    try {
      await navigator.clipboard.writeText(sampleValue);
      $('random-state').textContent = 'Sample copied';
    } catch (_) {
      $('random-state').textContent = 'Copy unavailable in this browser';
    }
  }
  $('generate-btn').addEventListener('click', generate);
  $('copy-output').addEventListener('click', copy);
  document.querySelectorAll('.side-links a').forEach(link => {
    link.addEventListener('click', () => {
      document.querySelectorAll('.side-links a').forEach(a => a.classList.remove('active'));
      link.classList.add('active');
    });
  });
  window.addEventListener('resize', resize);
  resize();
  refresh();
  setInterval(refresh, 1200);
})();
