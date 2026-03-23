// =============================================
//  CONTACT PAGE — Enhanced UI v2
// =============================================
const contactPage = `
  <style>
    .cp-wrap{background:#f0f2f8;min-height:100vh}
    /* HERO */
    .cp-hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 45%,#1d4ed8 80%,#60a5fa 100%);padding:70px 20px 0;text-align:center;overflow:hidden;position:relative}
    .cp-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")}
    .cp-hero-inner{position:relative;z-index:2;color:white;padding-bottom:40px}
    .cp-hero-icon{font-size:56px;margin-bottom:16px;filter:drop-shadow(0 4px 16px rgba(0,0,0,.35))}
    .cp-hero-inner h1{font-size:46px;font-weight:900;margin:0 0 12px;letter-spacing:-2px;text-shadow:0 2px 16px rgba(0,0,0,.3)}
    .cp-hero-inner p{font-size:16px;opacity:.85;max-width:560px;margin:0 auto;line-height:1.7}
    .cp-hero-wave{position:relative;z-index:2;line-height:0;margin-top:10px}
    .cp-hero-wave svg{width:100%;height:60px;display:block}
    /* INFO CARDS */
    .cp-info-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;padding:40px 40px 0;max-width:1200px;margin:0 auto}
    .cp-info-card{background:white;border-radius:18px;padding:28px 22px;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,.08);transition:transform .25s,box-shadow .25s;border-top:4px solid transparent}
    .cp-info-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(0,0,0,.14)}
    .cp-info-card:nth-child(1){border-top-color:#1d4ed8}
    .cp-info-card:nth-child(2){border-top-color:#10b981}
    .cp-info-card:nth-child(3){border-top-color:#f97316}
    .cp-info-card:nth-child(4){border-top-color:#8b5cf6}
    .cp-info-icon{width:60px;height:60px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:24px;color:white;margin:0 auto 16px}
    .cp-info-card:nth-child(1) .cp-info-icon{background:linear-gradient(135deg,#1d4ed8,#60a5fa)}
    .cp-info-card:nth-child(2) .cp-info-icon{background:linear-gradient(135deg,#059669,#10b981)}
    .cp-info-card:nth-child(3) .cp-info-icon{background:linear-gradient(135deg,#ea580c,#f97316)}
    .cp-info-card:nth-child(4) .cp-info-icon{background:linear-gradient(135deg,#7c3aed,#8b5cf6)}
    .cp-info-card h4{font-size:14px;font-weight:800;color:#1a202c;margin:0 0 8px;text-transform:uppercase;letter-spacing:.5px}
    .cp-info-card p{font-size:13px;color:#64748b;margin:0;line-height:1.7}
    .cp-info-card a{color:#1d4ed8;text-decoration:none;font-weight:600}
    .cp-info-card a:hover{text-decoration:underline}
    /* MAIN SECTION */
    .cp-main{display:grid;grid-template-columns:1fr 1.6fr;gap:32px;padding:40px 40px 80px;max-width:1200px;margin:0 auto}
    /* LEFT PANEL */
    .cp-left{}
    .cp-panel{background:white;border-radius:20px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.08);margin-bottom:24px}
    .cp-panel-title{font-size:18px;font-weight:800;color:#1a202c;margin:0 0 20px;display:flex;align-items:center;gap:10px}
    .cp-panel-title i{color:#1d4ed8}
    .cp-social-row{display:flex;gap:12px;flex-wrap:wrap}
    .cp-social-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:10px;font-size:13px;font-weight:700;color:white;text-decoration:none;transition:.2s}
    .cp-social-btn:hover{transform:translateY(-2px);opacity:.9}
    .cp-social-fb{background:#1877f2}
    .cp-social-tw{background:#1da1f2}
    .cp-social-yt{background:#ff0000}
    /* MAP */
    .cp-map-wrap{border-radius:14px;overflow:hidden;height:220px;position:relative;border:2px solid #e2e8f0;box-shadow:0 2px 10px rgba(0,0,0,.08)}
    .cp-map-wrap iframe{width:100%;height:100%;border:none;display:block}
    .cp-map-link{display:block;margin-top:8px;text-align:center;font-size:12px;color:#004080;text-decoration:none;font-weight:600}
    .cp-map-link:hover{text-decoration:underline}
    /* FORM */
    .cp-form-panel{background:white;border-radius:20px;padding:36px;box-shadow:0 4px 20px rgba(0,0,0,.08)}
    .cp-form-title{font-size:22px;font-weight:800;color:#1a202c;margin:0 0 6px;display:flex;align-items:center;gap:10px}
    .cp-form-title i{color:#1d4ed8}
    .cp-form-sub{font-size:14px;color:#64748b;margin:0 0 28px}
    .cp-fg{margin-bottom:18px}
    .cp-fg label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:6px}
    .cp-fg label i{color:#1d4ed8;font-size:11px}
    .cp-fc{width:100%;padding:13px 16px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:inherit;transition:.2s;background:#fff;color:#1a202c;box-sizing:border-box}
    .cp-fc:focus{outline:none;border-color:#1d4ed8;box-shadow:0 0 0 3px rgba(29,78,216,.1)}
    .cp-fc.cp-err{border-color:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.1)}
    .cp-fc.cp-ok{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.1)}
    textarea.cp-fc{resize:vertical;min-height:110px}
    .cp-frow{display:grid;grid-template-columns:1fr 1fr;gap:16px}
    .cp-err-msg{font-size:12px;color:#dc2626;margin-top:5px;display:none;align-items:center;gap:4px}
    .cp-err-msg.show{display:flex}
    .cp-char-count{font-size:11px;color:#94a3b8;text-align:right;margin-top:4px}
    .cp-submit-btn{width:100%;padding:15px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);color:white;border:none;border-radius:12px;font-size:15px;font-weight:800;cursor:pointer;transition:.25s;display:flex;align-items:center;justify-content:center;gap:10px;margin-top:8px}
    .cp-submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(29,78,216,.35)}
    .cp-submit-btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .cp-alert{padding:14px 18px;border-radius:12px;font-size:14px;font-weight:600;margin-bottom:20px;display:none;align-items:center;gap:10px}
    .cp-alert.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;display:flex}
    .cp-alert.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;display:flex}
    @media(max-width:1024px){.cp-info-grid{grid-template-columns:repeat(2,1fr)}.cp-main{grid-template-columns:1fr}}
    @media(max-width:600px){.cp-info-grid{grid-template-columns:1fr;padding:24px 16px 0}.cp-main{padding:24px 16px 60px}.cp-hero-inner h1{font-size:30px}.cp-frow{grid-template-columns:1fr}}
  </style>

  <div class="cp-wrap">
    <!-- HERO -->
    <div class="cp-hero">
      <div class="cp-hero-inner">
        <div class="cp-hero-icon"><i class="fa fa-envelope-open-text"></i></div>
        <h1>Contact Us</h1>
        <p>Have a question or want to know more about SCTI? We're here to help — reach out anytime.</p>
      </div>
      <div class="cp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C480,60 960,0 1440,30 L1440,60 L0,60 Z" fill="#f0f2f8"/>
        </svg>
      </div>
    </div>

    <!-- INFO CARDS -->
    <div class="cp-info-grid">
      <div class="cp-info-card">
        <div class="cp-info-icon"><i class="fa fa-map-location-dot"></i></div>
        <h4>Address</h4>
        <p>Kamalamai Municipality<br>Sindhuli, Province 3<br>Nepal</p>
      </div>
      <div class="cp-info-card">
        <div class="cp-info-icon"><i class="fa fa-phone-volume"></i></div>
        <h4>Phone</h4>
        <p><a href="tel:+9779841234567">+977-9841234567</a><br><a href="tel:+9779841234568">+977-9841234568</a></p>
      </div>
      <div class="cp-info-card">
        <div class="cp-info-icon"><i class="fa fa-envelope"></i></div>
        <h4>Email</h4>
        <p><a href="mailto:info@scti.edu.np">info@scti.edu.np</a><br><a href="mailto:admin@scti.edu.np">admin@scti.edu.np</a></p>
      </div>
      <div class="cp-info-card">
        <div class="cp-info-icon"><i class="fa fa-clock"></i></div>
        <h4>Office Hours</h4>
        <p>Sun – Fri: 10:00 AM – 5:00 PM<br>Saturday: Closed</p>
      </div>
    </div>

    <!-- MAIN -->
    <div class="cp-main">
      <!-- LEFT -->
      <div class="cp-left">
        <div class="cp-panel">
          <div class="cp-panel-title"><i class="fa fa-share-nodes"></i> Follow Us</div>
          <div class="cp-social-row">
            <a href="https://www.facebook.com/SCTISINDHULI/" target="_blank" class="cp-social-btn cp-social-fb"><i class="fab fa-facebook-f"></i> Facebook</a>
            <a href="#" class="cp-social-btn cp-social-tw"><i class="fab fa-twitter"></i> Twitter</a>
            <a href="https://www.tiktok.com/@scti_sindhuli/video/7528023104916966663" target="_blank" class="cp-social-btn cp-social-yt" style="background:#010101"><i class="fab fa-tiktok"></i> TikTok</a>
          </div>
        </div>
        <div class="cp-panel">
          <div class="cp-panel-title"><i class="fa fa-map-pin"></i> Find Us</div>
          <div class="cp-map-wrap">
            <iframe
              src="https://scti.edu.np/map/"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              title="SCTI Location">
            </iframe>
          </div>
          <a class="cp-map-link" href="https://scti.edu.np/map/" target="_blank" rel="noopener noreferrer">
            <i class="fa fa-external-link-alt"></i> Open Full Map
          </a>
        </div>
        <div class="cp-panel">
          <div class="cp-panel-title"><i class="fa fa-circle-info"></i> Quick Info</div>
          <p style="font-size:13px;color:#64748b;line-height:1.8;margin:0">
            SCTI is a government-recognized technical institute offering diploma and certificate programs in engineering and technology. We welcome students from all backgrounds.
          </p>
        </div>
      </div>

      <!-- FORM -->
      <div class="cp-form-panel">
        <div class="cp-form-title"><i class="fa fa-paper-plane"></i> Send Us a Message</div>
        <p class="cp-form-sub">Fill out the form below and we'll get back to you within 24 hours.</p>

        <div class="cp-alert" id="cpAlert"></div>

        <form id="cpForm" onsubmit="cpSubmit(event)" novalidate>
          <div class="cp-frow">
            <div class="cp-fg">
              <label><i class="fa fa-user"></i> Full Name *</label>
              <input type="text" id="cpName" class="cp-fc" placeholder="Your full name" oninput="cpValidate('cpName')">
              <div class="cp-err-msg" id="cpNameErr"><i class="fa fa-circle-exclamation"></i> Name is required</div>
            </div>
            <div class="cp-fg">
              <label><i class="fa fa-phone"></i> Phone *</label>
              <input type="tel" id="cpPhone" class="cp-fc" placeholder="+977-98XXXXXXXX" oninput="cpValidate('cpPhone')">
              <div class="cp-err-msg" id="cpPhoneErr"><i class="fa fa-circle-exclamation"></i> Valid phone required</div>
            </div>
          </div>
          <div class="cp-fg">
            <label><i class="fa fa-envelope"></i> Email Address *</label>
            <input type="email" id="cpEmail" class="cp-fc" placeholder="your@email.com" oninput="cpValidate('cpEmail')">
            <div class="cp-err-msg" id="cpEmailErr"><i class="fa fa-circle-exclamation"></i> Valid email required</div>
          </div>
          <div class="cp-fg">
            <label><i class="fa fa-tag"></i> Subject *</label>
            <input type="text" id="cpSubject" class="cp-fc" placeholder="What is this about?" oninput="cpValidate('cpSubject')">
            <div class="cp-err-msg" id="cpSubjectErr"><i class="fa fa-circle-exclamation"></i> Subject is required</div>
          </div>
          <div class="cp-fg">
            <label><i class="fa fa-comment-dots"></i> Message *</label>
            <textarea id="cpMessage" class="cp-fc" placeholder="Write your message here..." oninput="cpValidate('cpMessage');cpCount(this)"></textarea>
            <div class="cp-char-count" id="cpCharCount">0 / 1000</div>
            <div class="cp-err-msg" id="cpMessageErr"><i class="fa fa-circle-exclamation"></i> Message must be at least 10 characters</div>
          </div>
          <button type="submit" class="cp-submit-btn" id="cpSubmitBtn">
            <i class="fa fa-paper-plane"></i> Send Message
          </button>
        </form>
      </div>
    </div>

    <footer style="background:#0f172a;color:rgba(255,255,255,.7);text-align:center;padding:20px;font-size:13px">
      <p>© 2025 SCTI &nbsp;|&nbsp;
        <a href="#" onclick="loadPage('home');return false;" style="color:#60a5fa;text-decoration:none">Home</a> &nbsp;|&nbsp;
        <a href="#" onclick="loadPage('notices');return false;" style="color:#60a5fa;text-decoration:none">Notice Board</a>
      </p>
    </footer>
  </div>
`;

// ---- Contact form JS ----
function cpValidate(id) {
  const el  = document.getElementById(id);
  const err = document.getElementById(id + 'Err');
  if (!el) return true;
  let valid = true;
  const v = el.value.trim();

  if (id === 'cpName')    valid = v.length >= 2;
  if (id === 'cpEmail')   valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  if (id === 'cpPhone')   valid = v.length >= 7;
  if (id === 'cpSubject') valid = v.length >= 2;
  if (id === 'cpMessage') valid = v.length >= 10;

  el.classList.toggle('cp-err', !valid);
  el.classList.toggle('cp-ok', valid && v.length > 0);
  if (err) err.classList.toggle('show', !valid && v.length > 0);
  return valid;
}

function cpCount(el) {
  const c = document.getElementById('cpCharCount');
  if (c) c.textContent = el.value.length + ' / 1000';
  if (el.value.length > 1000) el.value = el.value.slice(0, 1000);
}

function cpSubmit(e) {
  e.preventDefault();
  const fields = ['cpName','cpEmail','cpPhone','cpSubject','cpMessage'];
  const allOk  = fields.map(cpValidate).every(Boolean);
  if (!allOk) return;

  const btn   = document.getElementById('cpSubmitBtn');
  const alert = document.getElementById('cpAlert');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
  alert.className = 'cp-alert';
  alert.style.display = 'none';

  const fd = new FormData();
  fd.append('name',    document.getElementById('cpName').value.trim());
  fd.append('email',   document.getElementById('cpEmail').value.trim());
  fd.append('phone',   document.getElementById('cpPhone').value.trim());
  fd.append('subject', document.getElementById('cpSubject').value.trim());
  fd.append('message', document.getElementById('cpMessage').value.trim());

  fetch('pages/contact-submit.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Message';
      if (res.success) {
        alert.innerHTML = '<i class="fa fa-circle-check"></i> Message sent! We\'ll get back to you soon.';
        alert.className = 'cp-alert ok';
        document.getElementById('cpForm').reset();
        fields.forEach(id => {
          const el = document.getElementById(id);
          if (el) { el.classList.remove('cp-ok','cp-err'); }
        });
        document.getElementById('cpCharCount').textContent = '0 / 1000';
      } else {
        alert.innerHTML = '<i class="fa fa-circle-exclamation"></i> ' + (res.message || 'Failed to send. Please try again.');
        alert.className = 'cp-alert err';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Message';
      alert.innerHTML = '<i class="fa fa-circle-exclamation"></i> Network error. Please try again.';
      alert.className = 'cp-alert err';
    });
}
