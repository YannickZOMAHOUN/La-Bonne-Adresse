<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>{{ $titre ?? 'Bonnes Adresses Bénin' }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background:#f0ebe1; font-family: Georgia, 'Times New Roman', serif; }
  .wrapper { max-width:600px; margin:0 auto; padding:32px 16px; }

  /* Header */
  .header {
    background: linear-gradient(135deg, #0f1a12 0%, #1a3d22 60%, #0f1a12 100%);
    border-radius:16px 16px 0 0;
    padding: 36px 40px 32px;
    text-align:center;
    position:relative;
    overflow:hidden;
  }
  .header::before {
    content:'';
    position:absolute; inset:0;
    background-image:
      repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 0, transparent 20px),
      repeating-linear-gradient(-45deg, rgba(255,255,255,0.03) 0, rgba(255,255,255,0.03) 1px, transparent 0, transparent 20px);
  }
  .logo-wrap {
    display:inline-flex; align-items:center; gap:10px;
    position:relative; z-index:1;
    margin-bottom:20px;
  }
  .logo-icon {
    width:42px; height:42px;
    background:linear-gradient(135deg, #c8922a, #e8b84b);
    border-radius:10px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:1.3rem; line-height:1;
  }
  .logo-text {
    font-family: Georgia, serif;
    font-size:1.1rem; color:#fff; line-height:1.15; text-align:left;
  }
  .logo-text span { color:#e8b84b; }

  .header-icon {
    position:relative; z-index:1;
    font-size:3rem; display:block; margin-bottom:14px;
  }
  .header-title {
    position:relative; z-index:1;
    font-family: Georgia, serif;
    font-size:1.55rem; font-weight:700;
    color:#fff; line-height:1.25;
  }
  .header-title em { color:#e8b84b; font-style:italic; }

  /* Body */
  .body {
    background:#ffffff;
    padding:40px;
    border-left:1px solid #e8e0d0;
    border-right:1px solid #e8e0d0;
  }

  .greeting {
    font-size:1.05rem; color:#2a2a2a;
    margin-bottom:20px; line-height:1.6;
  }
  .greeting strong { color:#1a6b3c; }

  .message {
    font-size:0.95rem; color:#4a4a4a;
    line-height:1.8; margin-bottom:28px;
  }

  /* Info box */
  .info-box {
    background:#faf6ef;
    border:1px solid #e8e0d0;
    border-left:4px solid #1a6b3c;
    border-radius:0 10px 10px 0;
    padding:18px 22px;
    margin-bottom:28px;
  }
  .info-box-title {
    font-size:0.75rem; font-weight:700;
    letter-spacing:0.1em; text-transform:uppercase;
    color:#1a6b3c; margin-bottom:12px;
  }
  .info-row {
    display:flex; gap:8px;
    font-size:0.88rem; color:#4a4a4a;
    padding:5px 0;
    border-bottom:1px solid #f0e8d8;
  }
  .info-row:last-child { border-bottom:none; }
  .info-label { color:#6b7280; min-width:90px; }
  .info-value { color:#2a2a2a; font-weight:600; }

  /* CTA Button */
  .cta-wrap { text-align:center; margin:32px 0; }
  .cta-btn {
    display:inline-block;
    background:linear-gradient(135deg, #1a6b3c, #25954f);
    color:#ffffff !important;
    font-family: Georgia, serif;
    font-size:1rem; font-weight:700;
    text-decoration:none;
    padding:14px 36px;
    border-radius:50px;
    letter-spacing:0.02em;
  }
  .cta-btn-gold {
    background:linear-gradient(135deg, #c8922a, #e8b84b);
    color:#0f1a12 !important;
  }

  /* Alert box */
  .alert-box {
    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:10px;
    padding:16px 20px;
    margin-bottom:24px;
    font-size:0.88rem; color:#92400e;
    line-height:1.6;
  }

  /* Success box */
  .success-box {
    background:#f0fdf4;
    border:1px solid #bbf7d0;
    border-radius:10px;
    padding:16px 20px;
    margin-bottom:24px;
    font-size:0.88rem; color:#166534;
    line-height:1.6;
  }

  /* Divider */
  .divider {
    border:none; border-top:1px solid #e8e0d0;
    margin:28px 0;
  }

  /* Footer */
  .footer {
    background:#0f1a12;
    border-radius:0 0 16px 16px;
    padding:28px 40px;
    text-align:center;
  }
  .footer-logo {
    font-family:Georgia, serif;
    font-size:1rem; color:#fff;
    margin-bottom:8px;
  }
  .footer-logo span { color:#e8b84b; }
  .footer-text {
    font-size:0.78rem; color:rgba(255,255,255,0.35);
    line-height:1.6;
  }
  .footer-flag { font-size:1.2rem; margin-bottom:6px; display:block; }

  /* Signature */
  .signature {
    font-size:0.9rem; color:#4a4a4a; line-height:1.7;
    margin-top:8px;
  }
  .signature strong { color:#1a6b3c; }
</style>
</head>
<body>
<div class="wrapper">

  {{-- HEADER --}}
  <div class="header">
    <div class="logo-wrap">
      <div class="logo-icon">📍</div>
      <div class="logo-text">Bonnes<br><span>Adresses Bénin</span></div>
    </div>
    <span class="header-icon">{{ $headerIcon ?? '📬' }}</span>
    <div class="header-title">{!! $headerTitle ?? 'Message' !!}</div>
  </div>

  {{-- BODY --}}
  <div class="body">
    @yield('content')

    <hr class="divider"/>

    <div class="signature">
      Cordialement,<br>
      <strong>L'équipe Bonnes Adresses Bénin</strong>
    </div>
  </div>

  {{-- FOOTER --}}
  <div class="footer">
    <span class="footer-flag">🇧🇯</span>
    <div class="footer-logo">Bonnes <span>Adresses Bénin</span></div>
    <div class="footer-text">
      Cotonou · Bohicon · Parakou<br>
      Ce mail a été envoyé automatiquement, merci de ne pas y répondre.
    </div>
  </div>

</div>
</body>
</html>
