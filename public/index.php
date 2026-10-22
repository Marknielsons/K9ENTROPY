<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#080d10" />
  <meta name="description" content="K9ENTROPY research prototype. Explore biological neural signals and cryptographic randomness in a local PHP dashboard." />
  <title>K9ENTROPY · Neural Security Research</title>
  <link rel="icon" type="image/png" href="/favicon.png" />
  <link rel="stylesheet" href="/assets/style.css" />
  <script src="/assets/app.js" defer></script>
</head>
<body>
<div class="app-shell">
  <aside class="sidebar" aria-label="Primary navigation">
    <a href="#overview" class="brand" aria-label="K9ENTROPY overview">
      <span class="brand-mark" aria-hidden="true"><img src="/assets/k9entropy-dog-mark.png" alt="" width="42" height="42" /></span>
      <span><strong>K9<span>ENTROPY</span></strong><small>NEURAL SECURITY LAB</small></span>
    </a>
    <div class="workspace-caption">WORKSPACE</div>
    <nav class="side-links">
      <a href="#overview" class="active"><span class="nav-icon">◈</span> Overview</a>
      <a href="#neural"><span class="nav-icon">∿</span> Neural monitor</a>
      <a href="#security"><span class="nav-icon">◇</span> Cryptography</a>
      <a href="#api"><span class="nav-icon">⌘</span> API reference</a>
    </nav>
    <div class="sidebar-bottom">
      <div class="sidebar-status"><span class="pulse amber"></span><div><strong>Research environment</strong><small>Security claims unverified</small></div></div>
      <div class="sidebar-version">K9ENTROPY / v0.1.0 <span>OPEN SOURCE</span></div>
    </div>
  </aside>
  <main id="overview">
    <header class="topbar">
      <div class="breadcrumb">LABORATORY <span>/</span> OVERVIEW</div>
      <div class="top-right"><span class="top-notice"><span class="pulse amber"></span> PROTOTYPE MODE</span><a class="top-action" href="https://github.com/new" target="_blank" rel="noopener noreferrer">GitHub <span>↗</span></a></div>
    </header>
    <div class="content">
      <div class="kicker"><span class="kicker-line"></span> EXPERIMENTAL BIOLOGICAL CRYPTOGRAPHY</div>
      <section class="heading-row">
        <div>
          <h1>Intelligence is <em>organic.</em><br />Security is engineered.</h1>
          <p class="intro">Exploring whether measurable neural activity can contribute independent entropy to digital security. Real signals, rigorous measurement, no unsupported guarantees.</p>
        </div>
        <div class="research-tag"><span class="ring-icon">◎</span><div><strong>RESEARCH BUILD 001</strong><small>EARLY DEVELOPMENT</small></div></div>
      </section>
      <div class="disclaimer"><span class="warn-symbol">!</span><span><strong>Research prototype.</strong> The live dashboard uses synthetic EEG data until you connect an adapter. Brain signals are never used to generate cryptographic keys. All random samples come from the operating system.</span></div>
      <section class="metric-grid" aria-label="System overview">
        <article class="metric"><div class="metric-top"><span>NEURAL INPUT</span><span class="metric-symbol">∿</span></div><strong id="metric-source">SIMULATED</strong><small id="metric-source-detail"><span class="mini-dot amber"></span> No EEG adapter connected</small></article>
        <article class="metric"><div class="metric-top"><span>ACTIVE CHANNELS</span><span class="metric-symbol">◫</span></div><strong><span id="channel-count">04</span><small class="unit"> CH</small></strong><small><span class="mini-dot green"></span> Display streams</small></article>
        <article class="metric"><div class="metric-top"><span>NEURAL ENTROPY CREDIT</span><span class="metric-symbol">◈</span></div><strong>0 <small class="unit">BITS</small></strong><small><span class="mini-dot amber"></span> Not validated for crypto</small></article>
        <article class="metric"><div class="metric-top"><span>RANDOMNESS BACKEND</span><span class="metric-symbol">◇</span></div><strong class="metric-small">OS CSPRNG</strong><small><span class="mini-dot green"></span> PHP random_bytes()</small></article>
      </section>
      <section class="two-col" id="neural">
        <article class="panel neural-panel">
          <div class="panel-heading"><div><div class="section-eyebrow">INPUT TELEMETRY / 01</div><h2>Neural signal monitor</h2></div><div class="signal-mode"><span class="pulse green"></span><span id="feed-mode">SIMULATION</span></div></div>
          <div class="signal-info"><span><b class="signal-led"></b> 4 CHANNELS</span><span id="signal-sample-count">96 SAMPLES / CHANNEL</span><span id="signal-time">ACQUIRING SIGNAL</span></div>
          <div class="wave-wrap"><canvas id="wave-canvas" aria-label="Four live-rendered synthetic or unverified EEG waveforms" role="img"></canvas><div class="wave-labels" aria-hidden="true"><span>CH 01</span><span>CH 02</span><span>CH 03</span><span>CH 04</span></div></div>
          <div class="signal-footer"><span><span class="square green"></span> Signal visualization</span><span><span class="square amber"></span> Not cryptographic evidence</span><span id="mean-amplitude">MEAN |AMPLITUDE|: -- μV</span></div>
        </article>
        <article class="panel engine-panel" id="security">
          <div class="panel-heading"><div><div class="section-eyebrow">ENTROPY PROCESSING / 02</div><h2>Security engine</h2></div><span class="safe-tag">ISOLATED</span></div>
          <div class="engine-flow">
            <div class="engine-step"><span class="step-number">01</span><div><strong>Neural data capture</strong><small>Simulated / unverified adapter input</small></div><span class="step-tag pending">DISPLAY ONLY</span></div>
            <div class="connector-line"></div>
            <div class="engine-step"><span class="step-number">02</span><div><strong>Entropy validation</strong><small>Statistical evaluation not implemented</small></div><span class="step-tag pending">PENDING</span></div>
            <div class="connector-line"></div>
            <div class="engine-step"><span class="step-number">03</span><div><strong>Cryptographic boundary</strong><small>Never accepts unvalidated EEG</small></div><span class="step-tag safe">ENFORCED</span></div>
            <div class="connector-line"></div>
            <div class="engine-step"><span class="step-number">04</span><div><strong>OS random generator</strong><small>Trusted baseline: random_bytes()</small></div><span class="step-tag safe">ACTIVE</span></div>
          </div>
          <div class="engine-bottom"><span>KEY GENERATION FROM EEG</span><strong>DISABLED <span class="slash">/</span> BY DESIGN</strong></div>
        </article>
      </section>
      <section class="bottom-grid" id="api">
        <article class="panel random-panel">
          <div class="panel-heading"><div><div class="section-eyebrow">CRYPTOGRAPHIC DEMONSTRATION / 03</div><h2>Generate a random sample</h2></div><span class="tag-icon">↗</span></div>
          <p>Generate a fresh 128-bit value using PHP's operating-system-backed CSPRNG. This publicly displayed value is <strong>not a secret key</strong>.</p>
          <div class="output-line"><code id="random-output">••••••••••••••••••••••••••••••••</code><button id="copy-output" class="copy-btn" title="Copy sample" aria-label="Copy random sample">⧉</button></div>
          <div class="random-action"><button id="generate-btn" class="primary-btn">Generate random sample <span>↗</span></button><span id="random-state" role="status">Ready to generate</span></div>
        </article>
        <article class="panel api-panel">
          <div class="panel-heading"><div><div class="section-eyebrow">DEVELOPER INTERFACE / 04</div><h2>Local API</h2></div><span class="endpoint-count">03 ENDPOINTS</span></div>
          <div class="endpoints"><div><span class="get">GET</span><code>/api/status</code><span>Signal telemetry</span></div><div><span class="get">GET</span><code>/api/random?bytes=16</code><span>CSPRNG demo</span></div><div><span class="post">POST</span><code>/api/eeg</code><span>Local adapter</span></div></div>
          <div class="api-note"><span>⌁</span> <span>EEG adapter input requires a server-side token and localhost access. It never enters the cryptographic path.</span></div>
        </article>
      </section>
      <footer><span>K9ENTROPY © 2026 · EXPERIMENTAL OPEN-SOURCE RESEARCH</span><span>BUILT TO QUESTION ASSUMPTIONS, NOT TO PROMISE IMPOSSIBILITIES.</span></footer>
    </div>
  </main>
</div>
</body>
</html>
