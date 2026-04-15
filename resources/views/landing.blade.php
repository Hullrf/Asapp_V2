<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASAPP — Sistema POS para restaurantes</title>
<link rel="stylesheet" href="/css/asapp-base.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: var(--font-sans); background:#fff; color: var(--text); }
a { text-decoration:none; color:inherit; }

/* ── NAVBAR ─────────────────────────────── */
.nav {
    position:sticky; top:0; z-index:100;
    background:#fff;
    border-bottom:1px solid var(--border);
    padding:0 40px; height:64px;
    display:flex; align-items:center; justify-content:space-between;
}
.nav-logo {
    font-size:22px; font-weight:900; letter-spacing:-1px;
    background:linear-gradient(135deg, var(--purple), var(--purple-dk));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    background-clip:text;
}
.nav-links { display:flex; gap:12px; align-items:center; }
.btn-login {
    color: var(--purple); font-size:13px; font-weight:600;
    padding:8px 16px; border-radius:8px;
    border:1.5px solid var(--purple-lt);
    transition: background 0.15s;
}
.btn-login:hover { background: var(--purple-bg); }
.btn-register {
    background: var(--purple); color:#fff;
    font-size:13px; font-weight:700;
    padding:8px 18px; border-radius:8px;
    transition: background 0.15s;
}
.btn-register:hover { background: var(--purple-dk); }

/* ── HERO ────────────────────────────────── */
.hero {
    background:linear-gradient(160deg, #F4F1FA 0%, #EDE9FE 60%, #F4F1FA 100%);
    padding:72px 40px 80px;
}
.hero-inner {
    max-width:1100px; margin:0 auto;
    display:grid; grid-template-columns:1fr 1fr;
    gap:56px; align-items:center;
}
.hero-tag {
    display:inline-block;
    background:#EDE9FE; color: var(--purple);
    font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
    padding:6px 14px; border-radius:20px; margin-bottom:24px;
}
.hero-text h1 {
    font-size:50px; font-weight:900; line-height:1.1;
    letter-spacing:-2px; margin-bottom:20px;
}
.hero-text h1 span { color: var(--purple); }
.hero-text p {
    font-size:17px; color:#6B7280; line-height:1.6; margin-bottom:32px;
}
.hero-btns { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.btn-primary {
    display:inline-block;
    background: var(--purple); color:#fff;
    font-size:14px; font-weight:700;
    padding:14px 28px; border-radius:12px;
    transition: background 0.15s;
}
.btn-primary:hover { background: var(--purple-dk); }
.btn-secondary {
    display:inline-block;
    background:#fff; color: var(--purple);
    border:1.5px solid var(--purple-lt);
    font-size:14px; font-weight:600;
    padding:14px 22px; border-radius:12px;
    transition: background 0.15s;
}
.btn-secondary:hover { background: var(--purple-bg); }
.hero-sub { font-size:12px; color: var(--text-muted); }

.hero-img { position:relative; }
.hero-img img {
    width:100%; border-radius:20px;
    box-shadow:0 24px 64px rgba(107,33,232,0.18);
    display:block;
}
.hero-badge {
    position:absolute; bottom:20px; left:-20px;
    background:#fff; border:1px solid var(--border);
    border-radius:12px; padding:12px 16px;
    box-shadow:0 8px 24px rgba(107,33,232,0.12);
    display:flex; align-items:center; gap:10px;
}
.badge-dot {
    width:8px; height:8px;
    background:#4ade80; border-radius:50%;
    box-shadow:0 0 0 3px rgba(74,222,128,0.25);
    flex-shrink:0;
}
.badge-text { font-size:12px; font-weight:700; color: var(--text); }
.badge-sub { font-size:11px; color: var(--text-muted); }
</style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="nav">
    <div class="nav-logo">ASAPP</div>
    <div class="nav-links">
        <a href="{{ route('login') }}" class="btn-login" id="nav-login">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn-register">Registrar negocio</a>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-tag">Sistema POS para restaurantes</div>
            <h1>Tu restaurante,<br><span>sin fricciones.</span></h1>
            <p>Mesas con QR, pedidos en tiempo real y pagos directos desde el celular del cliente. Sin hardware extra.</p>
            <div class="hero-btns">
                <a href="{{ route('register') }}" class="btn-primary">Registrar mi negocio</a>
                <a href="#como-funciona" class="btn-secondary">Ver cómo funciona</a>
            </div>
            <div class="hero-sub">Sin tarjeta de crédito &middot; Configuración en minutos</div>
        </div>
        <div class="hero-img">
            <img src="/img/hero.png" alt="ASAPP en acción — panel de pedidos en tiempo real">
            <div class="hero-badge">
                <div class="badge-dot"></div>
                <div>
                    <div class="badge-text">Pedido actualizado</div>
                    <div class="badge-sub">Mesa 4 &middot; EN VIVO</div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>
