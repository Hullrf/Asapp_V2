<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Control — ASAPP</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="/css/asapp-base.css">
    <style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:#F4F1FA; --surface:#ffffff; --surface2:#FAF8FF;
  --border:#E0D9F5; --border-soft:#EDE9F8;
  --sb-bg:#0F0A1E; --sb-active:rgba(107,33,232,0.30);
  --sb-text:rgba(255,255,255,0.45); --sb-text-on:#C4A0FF;
  --sb-divider:rgba(255,255,255,0.07);
  --purple:#6B21E8; --purple-dk:#3D0E8A; --purple-lt:#8B5CF6;
  --purple-dim:rgba(107,33,232,0.10); --purple-glow:rgba(107,33,232,0.20);
  --accent:#C4A0FF;
  --text:#1a1a2e; --text-muted:#6B7280; --text-faint:#9B8EC4;
  --danger:#B91C1C; --danger-bg:#FEF2F2; --danger-border:#FECACA;
  --warn-bg:#FFFBEB; --warn-text:#92400E; --warn-border:#FDE68A;
  --r-sm:6px; --r-md:10px; --r-lg:14px; --r-xl:20px;
  --shadow-sm:0 1px 4px rgba(107,33,232,0.06);
  --shadow-md:0 4px 16px rgba(107,33,232,0.10);
  --shadow-lg:0 8px 32px rgba(0,0,0,0.18);
  --font:'Plus Jakarta Sans',system-ui,sans-serif;
}

body { font-family:var(--font); background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

/* -- PANEL SHELL -- */
.panel-shell { display:flex; width:100%; min-height:100vh; }

/* -- SIDEBAR -- */
.sidebar { width:220px; background:var(--sb-bg); display:flex; flex-direction:column; flex-shrink:0; position:sticky; top:0; height:100vh; overflow-y:auto; }
.sb-header { padding:28px 20px 24px; border-bottom:1px solid var(--sb-divider); min-height:96px; display:flex; flex-direction:column; justify-content:center; }
.sb-logo { font-size:24px; font-weight:800; letter-spacing:-1px; background:linear-gradient(135deg,#C4A0FF,#A78BFA); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; line-height:1; }
.sb-tagline { font-size:10px; color:rgba(255,255,255,0.28); text-transform:uppercase; letter-spacing:1px; margin-top:6px; }
.sb-section { padding:16px 0 8px; flex:1; }
.sb-section-label { font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:rgba(255,255,255,0.2); padding:0 20px 8px; display:block; }
.sb-item { display:flex; align-items:center; gap:11px; padding:10px 20px; font-size:13.5px; font-weight:500; color:var(--sb-text); cursor:pointer; position:relative; border:none; background:none; width:100%; text-align:left; font-family:var(--font); text-decoration:none; transition:background 0.15s; }
.sb-item:hover { background:rgba(255,255,255,0.05); color:rgba(255,255,255,0.7); }
.sb-item.active { background:var(--sb-active); color:var(--sb-text-on); font-weight:600; }
.sb-item.active::before { content:''; position:absolute; left:0; top:4px; bottom:4px; width:3px; background:var(--accent); border-radius:0 3px 3px 0; }
.sb-icon { width:18px; height:18px; flex-shrink:0; opacity:0.6; display:flex; align-items:center; justify-content:center; }
.sb-item.active .sb-icon { opacity:1; }
.sb-badge { margin-left:auto; background:#B91C1C; color:#fff; font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; min-width:20px; text-align:center; }
.sb-footer { padding:12px 0; border-top:1px solid var(--sb-divider); margin-top:auto; }
.sb-user { display:flex; align-items:center; gap:10px; padding:10px 20px; }
.sb-avatar { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#6B21E8,#3D0E8A); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; }
.sb-user-name { font-size:12px; font-weight:600; color:rgba(255,255,255,0.7); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sb-user-role { font-size:10px; color:rgba(255,255,255,0.3); }

/* -- MAIN -- */
.main { flex:1; display:flex; flex-direction:column; min-width:0; background:var(--bg); }

/* -- TOPBAR (dentro de .main) -- */
.topbar { height:56px; background:var(--surface); border-bottom:1px solid var(--border); display:flex; align-items:center; padding:0 28px; gap:12px; flex-shrink:0; box-shadow:var(--shadow-sm); position:sticky; top:0; z-index:50; }
.topbar-title { font-size:17px; font-weight:700; color:var(--text); flex:1; }

/* -- SEDE SWITCHER -- */
.sede-switcher { position:relative; }
.sede-btn { background:var(--purple-dim); color:var(--purple); border:1px solid var(--border); padding:6px 12px; border-radius:var(--r-md); cursor:pointer; font-size:12px; font-weight:600; display:flex; align-items:center; gap:7px; transition:background 0.15s; font-family:var(--font); white-space:nowrap; max-width:200px; }
.sede-btn:hover { background:rgba(107,33,232,0.18); }
.sede-btn-nombre { overflow:hidden; text-overflow:ellipsis; max-width:130px; }
.sede-dot-btn { width:7px; height:7px; border-radius:50%; background:var(--purple); flex-shrink:0; }
.sede-drop { display:none; position:absolute; top:calc(100% + 8px); right:0; background:#fff; border-radius:12px; min-width:210px; box-shadow:var(--shadow-lg); z-index:500; overflow:hidden; border:1px solid var(--border); }
.sede-drop.open { display:block; }
.sede-drop-title { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:var(--text-faint); padding:12px 16px 6px; }
.sede-item { display:block; width:100%; padding:9px 16px; text-align:left; font-size:13px; color:var(--text); background:none; border:none; cursor:pointer; font-family:var(--font); transition:background 0.1s; text-decoration:none; }
.sede-item:hover { background:#F5F3FF; color:var(--purple); }
.sede-item.activa { color:var(--purple); font-weight:700; background:#EDE9FE; }
.sede-divider { height:1px; background:var(--border); margin:4px 0; }
.sede-nueva { color:var(--purple); font-weight:600; }

/* -- CONTENIDO -- */
.content { flex:1; overflow-y:auto; padding:24px 28px; }
.section { display:none; }
.section.active { display:block; }

/* -- FLASH -- */
.flash { padding:12px 18px; border-radius:var(--r-md); margin-bottom:20px; font-size:14px; font-weight:500; }
.flash.ok  { background:#EDE9FE; color:#5B21B6; border:1px solid #C4B5FD; }
.flash.err { background:var(--danger-bg); color:var(--danger); border:1px solid var(--danger-border); }

/* -- CARDS -- */
.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--r-lg); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:16px; }
.card-header { display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-bottom:1px solid var(--border-soft); flex-wrap:wrap; gap:8px; }
.card-title { font-size:14px; font-weight:700; color:var(--text); display:flex; align-items:center; gap:8px; margin-bottom:0; }
.card-icon { width:26px; height:26px; border-radius:var(--r-sm); background:var(--purple-dim); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.card-body { padding:16px 20px; }
.card-count { font-size:12px; font-weight:500; color:var(--text-faint); }

/* -- FORMULARIOS -- */
.form-group { display:flex; flex-direction:column; gap:5px; }
.form-label { font-size:11px; font-weight:700; color:var(--purple); text-transform:uppercase; letter-spacing:0.5px; }
.form-row { display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end; }
.form-row .form-group { flex:1; min-width:120px; }

input[type="text"], input[type="number"], input[type="email"],
input[type="password"], input[type="date"], select, textarea {
  padding:9px 12px; border:1.5px solid var(--border); border-radius:var(--r-md);
  font-size:13.5px; font-family:var(--font); background:var(--surface2);
  color:var(--text); outline:none; transition:border-color 0.15s, box-shadow 0.15s;
  width:100%;
}
input:focus, select:focus, textarea:focus {
  border-color:var(--purple); box-shadow:0 0 0 3px var(--purple-glow);
}

/* -- BOTONES -- */
.btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:var(--r-md); font-size:13px; font-weight:600; font-family:var(--font); border:none; cursor:pointer; transition:opacity 0.15s; text-decoration:none; white-space:nowrap; }
.btn:hover { opacity:0.85; }
.btn-primary { background:var(--purple); color:#fff; box-shadow:0 2px 8px rgba(107,33,232,0.25); }
.btn-ghost   { background:var(--purple-dim); color:var(--purple); }
.btn-outline { background:transparent; color:var(--text-muted); border:1.5px solid var(--border); }
.btn-danger  { background:var(--danger-bg); color:var(--danger); border:1px solid var(--danger-border); }
/* ── COMPAT: aliases for partials not yet updated ── */
.btn-success { background:var(--purple); color:#fff; box-shadow:0 2px 8px rgba(107,33,232,0.25); }
.btn-warning { background:var(--purple-dim); color:var(--purple); }
.btn-info    { background:var(--surface2); color:var(--text-muted); border:1.5px solid var(--border); }
.btn-sm      { padding:6px 11px; font-size:12px; }
.btn-xs      { padding:4px 9px; font-size:11px; border-radius:var(--r-sm); }
.btn-icon    { padding:7px; border-radius:var(--r-sm); background:var(--surface2); border:1px solid var(--border); color:var(--text-muted); cursor:pointer; display:inline-flex; align-items:center; transition:background 0.15s; }
.btn-icon:hover { background:var(--border); }

/* -- TABLA -- */
table { width:100%; border-collapse:collapse; font-size:13px; }
thead th { padding:9px 14px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-faint); border-bottom:1px solid var(--border); background:var(--surface2); }
tbody tr { border-bottom:1px solid var(--border-soft); transition:background 0.1s; }
tbody tr:hover { background:var(--surface2); }
tbody td { padding:10px 14px; vertical-align:middle; }
tbody tr:last-child { border-bottom:none; }
.table-wrap { overflow-x:auto; }

/* -- BADGES -- */
.badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
.badge-ok   { background:#ECFDF5; color:#065F46; }
.badge-off  { background:#F3F4F6; color:var(--text-muted); }
.badge-warn { background:var(--warn-bg); color:var(--warn-text); }

/* -- TAB BADGE (inventario) -- */
.tab-badge { display:inline-flex; align-items:center; justify-content:center; background:#B91C1C; color:#fff; border-radius:20px; font-size:10px; font-weight:700; min-width:18px; height:18px; padding:0 5px; margin-left:6px; }

/* -- TOAST -- */
#panel-toast { position:fixed; bottom:28px; right:28px; padding:14px 22px; border-radius:var(--r-lg); font-size:14px; font-weight:600; max-width:420px; box-shadow:var(--shadow-lg); z-index:9999; opacity:0; transform:translateY(10px); transition:opacity 0.25s,transform 0.25s; pointer-events:none; }
#panel-toast.show { opacity:1; transform:translateY(0); }
#panel-toast.toast-ok  { background:#EDE9FE; color:#5B21B6; border:1px solid #C4B5FD; }
#panel-toast.toast-err { background:var(--danger-bg); color:var(--danger); border:1px solid var(--danger-border); }
.toast-inner { display:flex; align-items:center; gap:14px; }
.toast-close { background:none; border:none; font-size:16px; cursor:pointer; opacity:0.5; color:inherit; padding:0; line-height:1; flex-shrink:0; }
.toast-close:hover { opacity:1; }

/* -- MODALES -- */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:999; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:#fff; border-radius:var(--r-xl); padding:32px; max-width:360px; width:90%; text-align:center; box-shadow:var(--shadow-lg); }
.modal h3 { font-size:17px; font-weight:700; color:var(--purple-dk); margin-bottom:6px; font-family:var(--font); }
.modal p  { font-size:13px; color:var(--text-faint); margin-bottom:20px; }
#qr-container { margin:0 auto 20px; display:flex; justify-content:center; }
.modal-url { font-size:11px; color:var(--text-faint); word-break:break-all; background:var(--surface2); padding:8px 12px; border-radius:var(--r-md); margin-bottom:20px; border:1px solid var(--border); }
.modal-acciones { display:flex; gap:10px; justify-content:center; flex-wrap:wrap; }
.btn-modal-pri { background:var(--purple); color:#fff; border:none; padding:9px 18px; border-radius:var(--r-md); font-size:13px; font-weight:600; cursor:pointer; font-family:var(--font); }
.btn-modal-sec { background:transparent; color:var(--text-muted); border:1.5px solid var(--border); padding:9px 18px; border-radius:var(--r-md); font-size:13px; font-weight:600; cursor:pointer; font-family:var(--font); }

/* -- MODAL NUEVO PEDIDO -- */
.modal-np { max-width:640px; text-align:left; max-height:90vh; display:flex; flex-direction:column; }
.modal-np h3 { margin-bottom:0; }
.np-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
.np-close { background:none; border:none; font-size:16px; color:var(--text-faint); cursor:pointer; padding:4px 8px; border-radius:var(--r-sm); transition:background 0.15s; }
.np-close:hover { background:var(--surface2); color:var(--purple-dk); }
.np-body { overflow-y:auto; flex:1; margin-bottom:16px; }
.np-body::-webkit-scrollbar { width:4px; }
.np-body::-webkit-scrollbar-track { background:var(--surface2); border-radius:4px; }
.np-body::-webkit-scrollbar-thumb { background:#C4B5FD; border-radius:4px; }
.pedido-tabla { width:100%; font-size:13px; border-collapse:collapse; }
.pedido-tabla th { text-align:left; padding:8px 10px; color:var(--purple); font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid var(--border); position:sticky; top:0; background:#fff; }
.pedido-tabla td { padding:9px 10px; border-bottom:1px solid var(--border-soft); vertical-align:middle; }
.pedido-tabla input[type="number"] { width:65px; }

/* -- SWIPE DOTS (movil) -- */
#swipe-dots { display:none; }

/* -- MOBILE (<900px): ocultar sidebar, mostrar topbar + tabs -- */
.m-topbar { display:none; }
.m-tabs   { display:none; }
.swipe-hint { display:none; }

@media (max-width: 900px) {
  body { flex-direction:column; }
  .panel-shell { flex-direction:column; min-height:0; flex:1; }
  .sidebar { display:none; }
  .main { min-height:0; flex:1; }
  .topbar { display:none; }

  .m-topbar {
    display:flex; align-items:center; height:52px; padding:0 16px; gap:10px;
    background:var(--sb-bg); flex-shrink:0; position:sticky; top:0; z-index:50;
  }
  .m-logo { font-size:19px; font-weight:800; letter-spacing:-0.5px; background:linear-gradient(135deg,#C4A0FF,#A78BFA); -webkit-background-clip:text; -webkit-text-fill-color:transparent; flex:1; }
  .m-sede { font-size:11px; color:rgba(255,255,255,0.5); display:flex; align-items:center; gap:5px; cursor:pointer; background:none; border:none; font-family:var(--font); padding:0; position:relative; }
  .m-sede-dot { width:6px; height:6px; border-radius:50%; background:#C4A0FF; flex-shrink:0; }
  .m-sede-drop { display:none; position:absolute; top:calc(100% + 10px); right:0; background:#fff; border-radius:var(--r-lg); min-width:200px; box-shadow:var(--shadow-lg); z-index:500; overflow:hidden; border:1px solid var(--border); }
  .m-sede-drop.open { display:block; }
  .m-logout-btn { background:none; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; padding:6px; color:rgba(255,255,255,0.5); border-radius:var(--r-sm); flex-shrink:0; }
  .m-logout-btn:hover { background:rgba(255,255,255,0.1); color:#fff; }

  .m-tabs {
    display:flex; background:var(--surface); border-bottom:1px solid var(--border);
    overflow-x:auto; padding:0 12px; gap:0; flex-shrink:0;
    scrollbar-width:none; -webkit-overflow-scrolling:touch;
  }
  .m-tabs::-webkit-scrollbar { display:none; }
  .m-tab { padding:11px 13px; font-size:12.5px; font-weight:600; color:var(--text-faint); white-space:nowrap; border-bottom:2.5px solid transparent; cursor:pointer; flex-shrink:0; background:none; border-top:none; border-left:none; border-right:none; font-family:var(--font); }
  .m-tab.active { color:var(--purple); border-bottom-color:var(--purple); }

  .swipe-hint {
    display:flex; align-items:center; justify-content:center; gap:6px;
    font-size:10px; color:rgba(107,33,232,0.45); padding:4px 0;
    background:var(--bg); border-bottom:1px solid var(--border-soft); flex-shrink:0;
  }

  #swipe-dots {
    display:flex; justify-content:center; align-items:center;
    gap:7px; padding:12px 0 8px;
  }
  .swipe-dot { width:7px; height:7px; border-radius:50%; background:#C4B5FD; transition:background 0.2s,transform 0.2s; }
  .swipe-dot.active { background:var(--purple); transform:scale(1.35); }

  .content { padding:16px 14px; }
  #panel-toast { right:12px; left:12px; max-width:none; bottom:16px; }
  .btn { min-height:40px; }
}

@media (max-width: 400px) {
  .m-logo { font-size:17px; }
}
    </style>
</head>
<body>

{{-- SHELL --}}
<div class="panel-shell">

{{-- SIDEBAR (desktop) --}}
<aside class="sidebar">
    <div class="sb-header">
        <div class="sb-logo">ASAPP</div>
        <div class="sb-tagline">Panel de Control</div>
    </div>
    <div class="sb-section">
        <span class="sb-section-label">Gestión</span>
        <button class="sb-item active" data-tab="inventario" onclick="showTab('inventario', this)">
            <span class="sb-icon"><svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg></span>
            Inventario
            <span class="sb-badge" id="badge-stock" style="{{ $productosStockBajo->isEmpty() ? 'display:none' : '' }}">{{ $productosStockBajo->count() }}</span>
        </button>
        <button class="sb-item" data-tab="mesas" onclick="showTab('mesas', this)">
            <span class="sb-icon"><svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"/></svg></span>
            Mesas
        </button>
        <button class="sb-item" data-tab="estadisticas" onclick="showTab('estadisticas', this)">
            <span class="sb-icon"><svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg></span>
            Estadísticas
        </button>
        <button class="sb-item" data-tab="historial" onclick="showTab('historial', this)">
            <span class="sb-icon"><svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg></span>
            Historial
        </button>
        <span class="sb-section-label" style="margin-top:14px;">Equipo</span>
        <button class="sb-item" data-tab="meseros" onclick="showTab('meseros', this)">
            <span class="sb-icon"><svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg></span>
            Meseros
        </button>
    </div>
    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->nombre, 0, 2)) }}</div>
            <div style="min-width:0;">
                <div class="sb-user-name">{{ auth()->user()->nombre }}</div>
                <div class="sb-user-role">{{ $negocio->nombre }}</div>
            </div>
        </div>
        <div style="padding:4px 20px 8px;">
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="width:100%; justify-content:center;">Cerrar sesión</button>
            </form>
        </div>
    </div>
</aside>

{{-- MAIN --}}
<div class="main">

{{-- TOPBAR MÓVIL --}}
<div class="m-topbar">
    <div class="m-logo">ASAPP</div>
    <button class="m-sede" id="mSedeBtn" type="button" onclick="toggleMSedeDrop(event)">
        <span class="m-sede-dot"></span>
        {{ $negocio->nombre }}
        <svg viewBox="0 0 20 20" fill="currentColor" width="10" height="10" style="opacity:0.5;margin-left:2px;"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
        <div class="m-sede-drop" id="mSedeDrop">
            <div class="sede-drop-title">Tus sedes</div>
            @foreach ($todasLasSedes as $sede)
                <a href="{{ route('panel.sedes.activar', $sede) }}"
                   class="sede-item {{ $sede->id_negocio === $negocio->id_negocio ? 'activa' : '' }}">
                    {{ $sede->nombre }}
                    @if ($sede->id_negocio === $negocio->id_negocio) &nbsp;✓ @endif
                </a>
            @endforeach
            <div class="sede-divider"></div>
            <button type="button" class="sede-item sede-nueva"
                    onclick="abrirNuevaSede(); document.getElementById('mSedeDrop').classList.remove('open');">
                + Nueva sede
            </button>
        </div>
    </button>
    <form action="{{ route('logout') }}" method="POST" style="margin:0">
        @csrf
        <button type="submit" class="m-logout-btn" title="Cerrar sesión">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </button>
    </form>
</div>

{{-- TABS MÓVIL --}}
<div class="m-tabs">
    <button class="m-tab active" onclick="showTab('inventario', this)">Inventario</button>
    <button class="m-tab" onclick="showTab('mesas', this)">Mesas</button>
    <button class="m-tab" onclick="showTab('estadisticas', this)">Estadísticas</button>
    <button class="m-tab" onclick="showTab('historial', this)">Historial</button>
    <button class="m-tab" onclick="showTab('meseros', this)">Meseros</button>
</div>

<div class="swipe-hint">
    <span>←</span> desliza para navegar <span>→</span>
</div>

{{-- TOPBAR DESKTOP --}}
<div class="topbar">
    <div class="topbar-title">Inventario</div>
    <div class="sede-switcher">
        <button class="sede-btn" id="sedeBtn" type="button">
            <span class="sede-dot-btn"></span>
            <span class="sede-btn-nombre">{{ $negocio->nombre }}</span>
            <svg viewBox="0 0 20 20" fill="currentColor" width="11" height="11" style="opacity:0.5;flex-shrink:0;"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
        </button>
        <div class="sede-drop" id="sedeDrop">
            <div class="sede-drop-title">Tus sedes</div>
            @foreach ($todasLasSedes as $sede)
                <a href="{{ route('panel.sedes.activar', $sede) }}"
                   class="sede-item {{ $sede->id_negocio === $negocio->id_negocio ? 'activa' : '' }}">
                    {{ $sede->nombre }}
                    @if ($sede->id_negocio === $negocio->id_negocio) &nbsp;✓ @endif
                </a>
            @endforeach
            <div class="sede-divider"></div>
            <button type="button" class="sede-item sede-nueva"
                    onclick="abrirNuevaSede(); document.getElementById('sedeDrop').classList.remove('open');">
                + Nueva sede
            </button>
        </div>
    </div>
    {{-- Botón Personalizar: visible solo en estadísticas (JS lo muestra/oculta) --}}
    <button id="btn-personalizar" class="btn btn-outline btn-sm" onclick="abrirPersonalizar()" style="display:none;">
        <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"/></svg>
        Personalizar
    </button>
</div>

{{-- CONTENIDO --}}
<div class="content">

    {{-- Flash message --}}
    @if (session('message'))
        @php $es_error = str_starts_with(session('message'), '❌'); @endphp
        <div class="flash {{ $es_error ? 'err' : 'ok' }}">
            {!! session('message') !!}
        </div>
    @endif

    {{-- TAB: INVENTARIO --}}
    <div id="tab-inventario" class="section active">
        @include('admin.partials.inventario')
    </div>

    {{-- TAB: MESAS --}}
    <div id="tab-mesas" class="section">
        @include('admin.partials.mesas')
    </div>

    {{-- TAB: ESTADÍSTICAS --}}
    <div id="tab-estadisticas" class="section">
        @include('admin.partials.estadisticas')
    </div>

    {{-- TAB: HISTORIAL --}}
    <div id="tab-historial" class="section">
        @include('admin.partials.historial')
    </div>

    {{-- TAB: MESEROS --}}
    <div id="tab-meseros" class="section">
        @include('admin.partials.meseros')
    </div>

</div>{{-- /content --}}
</div>{{-- /main --}}
</div>{{-- /panel-shell --}}

{{-- Indicador de posición (solo visible en móvil) --}}
<div id="swipe-dots">
    <span class="swipe-dot active"></span>
    <span class="swipe-dot"></span>
    <span class="swipe-dot"></span>
    <span class="swipe-dot"></span>
    <span class="swipe-dot"></span>
</div>

{{-- TOAST --}}
<div id="panel-toast"></div>

{{-- MODAL QR --}}
<div class="modal-overlay" id="modal-qr">
    <div class="modal">
        <h3 id="qr-titulo">Mesa</h3>
        <p>Escanea este código con el celular para acceder a la factura</p>
        <div id="qr-container"></div>
        <div class="modal-url" id="qr-url"></div>
        <div class="modal-acciones">
            <button class="btn btn-success" onclick="copiarLink()" id="btn-copiar">📋 Copiar link</button>
            <button class="btn btn-primary" onclick="imprimirQR()">🖨️ Imprimir</button>
            <button class="btn btn-outline" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>
</div>

{{-- MODAL NUEVO PEDIDO --}}
<div class="modal-overlay" id="modal-np">
    <div class="modal modal-np">
        <div class="np-header">
            <h3 id="np-titulo">🧾 Nuevo pedido</h3>
            <button class="np-close" onclick="cerrarNuevoPedido()">✕</button>
        </div>

        @php $productosDisp = $productos->filter(fn($p) => $p->disponible)->values(); @endphp

        @if ($productosDisp->isEmpty())
            <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
                No hay productos disponibles. Agrégalos en la pestaña Inventario.
            </p>
        @else
            <input type="text" id="np-buscador" placeholder="🔍 Buscar producto…"
                   oninput="filtrarNP(this.value)"
                   style="width:100%; margin-bottom:12px; font-size:13px;">

            <form action="{{ route('panel.pedidos.store') }}" method="POST" id="form-np">
                @csrf
                <input type="hidden" name="id_mesa" id="np-id-mesa">

                <div class="np-body" style="max-height:360px;">
                    <table class="pedido-tabla" id="np-tabla">
                        <thead>
                            <tr>
                                <th>Agregar</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productosDisp as $p)
                                <tr data-np="{{ strtolower($p->nombre . ' ' . ($p->categoria?->nombre ?? '')) }}">
                                    <td>
                                        <input type="checkbox" name="productos[]"
                                               value="{{ $p->id_producto }}"
                                               onchange="toggleNP(this, {{ $p->id_producto }})">
                                    </td>
                                    <td>
                                        <div style="font-weight:600; color:#1a1a2e;">{{ $p->nombre }}</div>
                                        @if ($p->categoria)
                                            <div style="font-size:11px; color:#9B8EC4;">{{ $p->categoria->nombre }}</div>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">${{ number_format($p->precio, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="number"
                                               name="cantidades[{{ $p->id_producto }}]"
                                               id="np-cant-{{ $p->id_producto }}"
                                               value="1" min="1" disabled>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="np-sin-resultados" style="display:none;">
                                <td colspan="4" style="text-align:center; color:#9B8EC4; padding:16px; font-size:13px;">
                                    Sin productos que coincidan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding-top:4px;">
                    <button type="button" class="btn btn-outline" onclick="cerrarNuevoPedido()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">✅ Crear pedido</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- MODAL NUEVA SEDE --}}
<div class="modal-overlay" id="modal-sede">
    <div class="modal" style="max-width:420px; text-align:left;">
        <h3 style="margin-bottom:6px;">🏪 Nueva sede</h3>
        <p style="margin-bottom:20px;">Añade una nueva sucursal o punto de venta a tu cuenta.</p>
        <form action="{{ route('panel.sedes.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:var(--purple); display:block; margin-bottom:6px;">
                    Nombre de la sede *
                </label>
                <input type="text" name="nombre" required maxlength="100"
                       placeholder="Ej: Sede Norte, Sucursal Centro"
                       style="width:100%; padding:10px 12px; border:1.5px solid var(--border);
                              border-radius:10px; font-size:14px; font-family:inherit;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:var(--purple); display:block; margin-bottom:6px;">
                    Dirección
                </label>
                <input type="text" name="direccion" maxlength="150"
                       placeholder="Calle 10 # 20-30"
                       style="width:100%; padding:10px 12px; border:1.5px solid var(--border);
                              border-radius:10px; font-size:14px; font-family:inherit;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:var(--purple); display:block; margin-bottom:6px;">
                    Teléfono
                </label>
                <input type="text" name="telefono" maxlength="20"
                       placeholder="300 000 0000"
                       style="width:100%; padding:10px 12px; border:1.5px solid var(--border);
                              border-radius:10px; font-size:14px; font-family:inherit;">
            </div>
            @if($todasLasSedes->count() > 0)
            <div style="margin-bottom:20px; padding:14px; background:var(--bg); border-radius:10px; border:1.5px solid var(--border);">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:var(--purple); display:block; margin-bottom:8px;">
                    Importar catálogo desde otra sede
                </label>
                <p style="font-size:12px; color:var(--text-muted); margin:0 0 10px;">
                    Copia las categorías y productos de una sede existente a esta nueva sede.
                </p>
                <select name="importar_desde"
                        style="width:100%; padding:10px 12px; border:1.5px solid var(--border);
                               border-radius:10px; font-size:14px; font-family:inherit; background:var(--surface);">
                    <option value="">— No importar —</option>
                    @foreach($todasLasSedes as $sede)
                        <option value="{{ $sede->id_negocio }}">{{ $sede->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="importar_desde" value="">
            @endif
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="cerrarNuevaSede()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear sede →</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Tabs ──────────────────────────────────────────────────────────────
const TAB_ORDER = ['inventario', 'mesas', 'estadisticas', 'historial', 'meseros'];
const TAB_TITLES = { inventario:'Inventario', mesas:'Mesas', estadisticas:'Estadísticas', historial:'Historial', meseros:'Meseros' };
let currentTabIndex = 0;

function showTab(tabName, btn) {
    // Secciones
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');

    // Sidebar items
    document.querySelectorAll('.sb-item[data-tab]').forEach(t => t.classList.remove('active'));
    const sbItem = document.querySelector(`.sb-item[data-tab="${tabName}"]`);
    if (sbItem) sbItem.classList.add('active');

    // Tabs móvil
    document.querySelectorAll('.m-tab').forEach(t => t.classList.remove('active'));
    const idx = TAB_ORDER.indexOf(tabName);
    const mTab = (btn?.classList.contains('m-tab')) ? btn : document.querySelectorAll('.m-tab')[idx];
    if (mTab) {
        mTab.classList.add('active');
        mTab.scrollIntoView({ behavior:'smooth', block:'nearest', inline:'center' });
    }

    // Título topbar desktop
    const titleEl = document.querySelector('.topbar-title');
    if (titleEl) titleEl.textContent = TAB_TITLES[tabName] || tabName;

    // Botón Personalizar (solo en estadísticas)
    const btnP = document.getElementById('btn-personalizar');
    if (btnP) btnP.style.display = tabName === 'estadisticas' ? '' : 'none';

    currentTabIndex = idx;
    actualizarDots(currentTabIndex);

    if (tabName === 'estadisticas' && typeof initEstadisticasCharts === 'function') {
        initEstadisticasCharts();
    }
}

function actualizarDots(index) {
    document.querySelectorAll('.swipe-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
}

// ── Swipe entre tabs (solo móvil) ─────────────────────────────────────
(function() {
    let startX = 0, startY = 0;
    const content = document.querySelector('.content');
    if (!content) return;

    content.addEventListener('touchstart', e => {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    }, { passive: true });

    content.addEventListener('touchend', e => {
        const deltaX = e.changedTouches[0].clientX - startX;
        const deltaY = e.changedTouches[0].clientY - startY;

        // Ignorar si el gesto es más vertical que horizontal, o muy corto
        if (Math.abs(deltaX) < 60 || Math.abs(deltaX) < Math.abs(deltaY)) return;

        const nextIndex = deltaX < 0
            ? Math.min(currentTabIndex + 1, TAB_ORDER.length - 1)  // swipe izquierda → siguiente
            : Math.max(currentTabIndex - 1, 0);                     // swipe derecha  → anterior

        if (nextIndex !== currentTabIndex) {
            showTab(TAB_ORDER[nextIndex]);
        }
    }, { passive: true });
})();

// ── Modal Nuevo Pedido ────────────────────────────────────────────────
function abrirNuevoPedido(idMesa, nombreMesa) {
    document.getElementById('np-id-mesa').value = idMesa;
    document.getElementById('np-titulo').textContent = '🧾 Nuevo pedido — ' + nombreMesa;

    // Resetear checkboxes y cantidades
    document.querySelectorAll('#np-tabla input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        const cant = document.getElementById('np-cant-' + cb.value);
        if (cant) { cant.disabled = true; cant.value = 1; }
    });

    // Resetear buscador y mostrar todas las filas
    const buscador = document.getElementById('np-buscador');
    if (buscador) { buscador.value = ''; filtrarNP(''); }

    document.getElementById('modal-np').classList.add('open');
}

function cerrarNuevoPedido() {
    document.getElementById('modal-np').classList.remove('open');
}

function toggleNP(checkbox, id) {
    const input = document.getElementById('np-cant-' + id);
    input.disabled = !checkbox.checked;
    if (checkbox.checked) input.focus();
}

function filtrarNP(q) {
    const term  = q.toLowerCase().trim();
    const filas = document.querySelectorAll('#np-tabla tbody tr[data-np]');
    let visibles = 0;
    filas.forEach(fila => {
        const match = !term || fila.dataset.np.includes(term);
        fila.style.display = match ? '' : 'none';
        if (match) visibles++;
    });
    const aviso = document.getElementById('np-sin-resultados');
    if (aviso) aviso.style.display = visibles === 0 ? '' : 'none';
}

// ── QR Modal ─────────────────────────────────────────────────────────
function mostrarQR(nombreMesa, url) {
    document.getElementById('qr-titulo').textContent = '📷 ' + nombreMesa;
    document.getElementById('qr-url').textContent = url;

    const container = document.getElementById('qr-container');
    container.innerHTML = '';

    new QRCode(container, {
        text: url,
        width: 200,
        height: 200,
        colorDark: '#3D0E8A',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    document.getElementById('modal-qr').classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal-qr').classList.remove('open');
    document.getElementById('qr-container').innerHTML = '';
}

// ── Sede switcher (desktop) ────────────────────────────────────────────
const sedeBtn  = document.getElementById('sedeBtn');
const sedeDrop = document.getElementById('sedeDrop');

sedeBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    sedeDrop.classList.toggle('open');
});

document.addEventListener('click', function () {
    sedeDrop.classList.remove('open');
    const md = document.getElementById('mSedeDrop');
    if (md) md.classList.remove('open');
});

sedeDrop.addEventListener('click', function (e) {
    e.stopPropagation();
});

// ── Sede switcher (mobile) ─────────────────────────────────────────────
function toggleMSedeDrop(e) {
    e.stopPropagation();
    const md = document.getElementById('mSedeDrop');
    if (md) md.classList.toggle('open');
}

const mSedeDrop = document.getElementById('mSedeDrop');
if (mSedeDrop) {
    mSedeDrop.addEventListener('click', function (e) { e.stopPropagation(); });
}

// ── Modal nueva sede ──────────────────────────────────────────────────
function abrirNuevaSede() {
    document.getElementById('modal-sede').classList.add('open');
}

function cerrarNuevaSede() {
    document.getElementById('modal-sede').classList.remove('open');
}

document.getElementById('modal-sede').addEventListener('click', function (e) {
    if (e.target === this) cerrarNuevaSede();
});

function copiarLink() {
    const url = document.getElementById('qr-url').textContent;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('btn-copiar');
        btn.textContent = '✅ ¡Copiado!';
        btn.style.background = 'var(--purple-dk)';
        setTimeout(() => { btn.textContent = '📋 Copiar link'; btn.style.background = ''; }, 2500);
    });
}

function imprimirQR() {
    const nombre = document.getElementById('qr-titulo').textContent;
    const url    = document.getElementById('qr-url').textContent;
    const canvas = document.querySelector('#qr-container canvas');
    const imgSrc = canvas ? canvas.toDataURL() : '';

    const win = window.open('', '_blank');
    win.document.write(`
        <!DOCTYPE html><html><head>
        <title>QR ${nombre}</title>
        <style>
            body { font-family: sans-serif; text-align: center; padding: 40px; }
            h2   { color: #3D0E8A; margin-bottom: 8px; }
            p    { color: #888; font-size: 12px; margin-bottom: 20px; }
            img  { border: 2px solid #6B21E8; border-radius: 8px; padding: 8px; }
            .url { font-size: 10px; color: #aaa; margin-top: 12px; word-break: break-all; }
        </style></head><body>
        <h2>${nombre}</h2>
        <p>Escanea el código QR para acceder a tu factura</p>
        <img src="${imgSrc}" width="220">
        <div class="url">${url}</div>
        <script>window.onload=()=>window.print()<\/script>
        </body></html>
    `);
    win.document.close();
}

// Cerrar modales al hacer clic fuera
document.getElementById('modal-qr').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
document.getElementById('modal-np').addEventListener('click', function(e) {
    if (e.target === this) cerrarNuevoPedido();
});

// ── AJAX Form Interceptor ─────────────────────────────────────────────
document.addEventListener('submit', async function(e) {
    const form = e.target;
    if (!('ajax' in form.dataset)) return;
    e.preventDefault();

    let res, data;
    try {
        res = await fetch(form.action, {
            method: form.method.toUpperCase(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        });
        data = await res.json();
    } catch {
        showToast('❌ Error de conexión', false);
        return;
    }

    if (res.status === 422) {
        const msg = data.errors
            ? Object.values(data.errors).flat().join(' · ')
            : (data.message || 'Error de validación');
        showToast('❌ ' + msg, false);
        return;
    }

    showToast(data.message, data.success !== false);

    if (data.success !== false) {
        const toRefresh = (form.dataset.refresh || '').split(',').filter(Boolean);
        if (toRefresh.length) await refreshPartials(toRefresh);
    }
});

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, ok = true) {
    const t = document.getElementById('panel-toast');
    t.innerHTML = `<div class="toast-inner"><span>${msg}</span><button class="toast-close" onclick="document.getElementById('panel-toast').classList.remove('show')">✕</button></div>`;
    t.className = (ok ? 'toast-ok' : 'toast-err');
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 3500);
}

// ── Refresh parciales ─────────────────────────────────────────────────
async function refreshPartials(names) {
    await Promise.all(names.map(async name => {
        try {
            const res       = await fetch('/panel/partials/' + name, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html      = await res.text();
            const container = document.getElementById('tab-' + name);
            if (!container) return;
            container.innerHTML = html;
            activarScripts(container);
            if (name === 'inventario') actualizarBadgeStock();
            if (name === 'estadisticas' && container.classList.contains('active')) {
                if (typeof initEstadisticasCharts === 'function') initEstadisticasCharts();
            }
        } catch { /* fallo silencioso por parcial */ }
    }));
}

function activarScripts(container) {
    container.querySelectorAll('script').forEach(old => {
        const s = document.createElement('script');
        s.textContent = old.textContent;
        document.head.appendChild(s);
        document.head.removeChild(s);
    });
}

function actualizarBadgeStock() {
    const el    = document.getElementById('stock-bajo-count');
    const badge = document.getElementById('badge-stock');
    if (!el || !badge) return;
    const n = parseInt(el.dataset.count || '0');
    badge.textContent    = n;
    badge.style.display  = n > 0 ? '' : 'none';
}
</script>

</body>
</html>
