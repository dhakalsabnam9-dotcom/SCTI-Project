// =============================================
//  NOTICE BOARD PAGE — Enhanced UI v2
// =============================================
const noticesPage = `
  <style>
    .nbp-wrap{background:#f0f2f8;min-height:100vh}
    /* HERO */
    .nbp-hero{position:relative;background:linear-gradient(135deg,#1e0a4e 0%,#4c1d95 45%,#7c3aed 80%,#a78bfa 100%);padding:70px 20px 0;text-align:center;overflow:hidden}
    .nbp-hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")}
    .nbp-hero-inner{position:relative;z-index:2;color:white;padding-bottom:36px}
    .nbp-hero-icon{font-size:56px;margin-bottom:16px;filter:drop-shadow(0 4px 16px rgba(0,0,0,.35))}
    .nbp-hero-inner h1{font-size:46px;font-weight:900;margin:0 0 12px;letter-spacing:-2px;text-shadow:0 2px 16px rgba(0,0,0,.3)}
    .nbp-hero-inner p{font-size:16px;opacity:.85;max-width:560px;margin:0 auto 28px;line-height:1.7}
    .nbp-hero-stats{display:flex;justify-content:center;gap:14px;flex-wrap:wrap;margin-bottom:28px}
    .nbp-stat-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.28);border-radius:30px;padding:10px 24px;font-size:14px;font-weight:700;color:white;backdrop-filter:blur(6px);cursor:pointer;transition:all .22s}
    .nbp-stat-pill:hover{background:rgba(255,255,255,.28);transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.2)}
    .nbp-stat-pill.nbp-pill-active{background:white;color:#4c1d95;box-shadow:0 6px 20px rgba(0,0,0,.2)}
    /* SEARCH BAR in hero */
    .nbp-search-wrap{max-width:560px;margin:0 auto 10px;position:relative;z-index:3}
    .nbp-search-row{display:flex;gap:0;background:white;border-radius:50px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.25)}
    .nbp-search-row input{flex:1;padding:14px 22px;border:none;outline:none;font-size:15px;color:#333;background:transparent}
    .nbp-search-row button{padding:14px 28px;background:linear-gradient(135deg,#4c1d95,#7c3aed);color:white;border:none;cursor:pointer;font-size:14px;font-weight:700;display:flex;align-items:center;gap:7px;transition:.2s}
    .nbp-search-row button:hover{opacity:.88}
    .nbp-hero-wave{position:relative;z-index:2;line-height:0;margin-top:10px}
    .nbp-hero-wave svg{width:100%;height:60px;display:block}
    /* FILTER BAR */
    .nbp-filter-bar{background:white;border-bottom:2px solid #e4e8f0;box-shadow:0 4px 18px rgba(0,0,0,.08);position:sticky;top:80px;z-index:100}
    .nbp-filters{display:flex;gap:8px;padding:14px 32px;overflow-x:auto;scrollbar-width:none;align-items:center;justify-content:space-between}
    .nbp-filters::-webkit-scrollbar{display:none}
    .nbp-filter-left{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
    .nbp-filter-label{font-size:11px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-right:6px;white-space:nowrap;flex-shrink:0}
    .nbp-filter{display:inline-flex;align-items:center;gap:6px;padding:9px 20px;border:2px solid #e0e6ef;background:#f8fafc;color:#5a6a80;border-radius:24px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap;transition:all .22s;outline:none;flex-shrink:0}
    .nbp-filter:hover{border-color:#7c3aed;color:#7c3aed;background:white;transform:translateY(-1px);box-shadow:0 4px 12px rgba(124,58,237,.12)}
    .nbp-filter.nbp-active{background:linear-gradient(135deg,#4c1d95,#7c3aed);border-color:transparent;color:white;box-shadow:0 4px 18px rgba(124,58,237,.38);transform:translateY(-1px)}
    .nbp-add-btn{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;border-radius:24px;cursor:pointer;font-size:13px;font-weight:700;transition:.2s;white-space:nowrap;flex-shrink:0}
    .nbp-add-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(16,185,129,.35)}
    /* MAIN */
    .nbp-main{padding:36px 32px 80px}
    .nbp-loading{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:100px 20px;gap:18px}
    .nbp-dots{display:flex;gap:10px}
    .nbp-dots span{width:14px;height:14px;border-radius:50%;background:#7c3aed;animation:nbpBounce 1.3s ease-in-out infinite}
    .nbp-dots span:nth-child(2){animation-delay:.18s;background:#a78bfa}
    .nbp-dots span:nth-child(3){animation-delay:.36s;background:#c4b5fd}
    @keyframes nbpBounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1.2);opacity:1}}
    .nbp-loading p{color:#8a9ab5;font-size:14px;font-weight:500}
    .nbp-empty{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:100px 20px;text-align:center}
    .nbp-empty i{font-size:72px;color:#c5cfe0;margin-bottom:20px}
    .nbp-empty h3{margin:0 0 10px;color:#4a5568;font-size:22px;font-weight:700}
    .nbp-empty p{margin:0;color:#8a9ab5;font-size:14px}
    /* CARDS */
    .nbp-list{display:flex;flex-direction:column;gap:20px;width:100%}
    .nbp-card{background:white;border-radius:20px;overflow:hidden;box-shadow:0 3px 16px rgba(0,0,0,.08);transition:transform .28s,box-shadow .28s;border-left:6px solid #7c3aed;width:100%}
    .nbp-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.14)}
    .nbp-card.nbp-urgent-card{border-left-color:#dc2626}
    .nbp-card.nbp-exam-card{border-left-color:#3b82f6}
    .nbp-card.nbp-admission-card{border-left-color:#10b981}
    .nbp-card.nbp-event-card{border-left-color:#f97316}
    .nbp-card.nbp-holiday-card{border-left-color:#eab308}
    .nbp-card.nbp-general-card{border-left-color:#06b6d4}
    .nbp-card-accent{height:4px;width:100%}
    .nbp-card-inner{display:flex;align-items:stretch}
    .nbp-card-icon-col{width:88px;flex-shrink:0;display:flex;align-items:flex-start;justify-content:center;padding:26px 0}
    .nbp-cat-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;color:white;box-shadow:0 4px 14px rgba(0,0,0,.18)}
    .nbp-card-content{flex:1;padding:22px 32px 22px 0;min-width:0}
    .nbp-card-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px}
    .nbp-badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:capitalize;display:inline-flex;align-items:center;gap:4px}
    .nbp-badge-urgent{background:#fee2e2;color:#991b1b}
    .nbp-badge-high{background:#fef3c7;color:#92400e}
    .nbp-badge-normal{background:#ede9fe;color:#5b21b6}
    .nbp-badge-cat{color:white}
    .nbp-card-date{font-size:12px;color:#aaa;font-weight:500;margin-left:auto;white-space:nowrap;display:flex;align-items:center;gap:5px}
    .nbp-card-date i{color:#a78bfa}
    .nbp-card-title{font-size:20px;font-weight:800;color:#1a202c;margin:0 0 10px;line-height:1.35}
    .nbp-card-desc{font-size:14px;color:#4a5568;line-height:1.8;margin:0 0 18px;white-space:pre-line}
    .nbp-card-footer{display:flex;align-items:center;gap:10px;flex-wrap:wrap;background:#f8f7ff;margin:0 -32px -22px 0;padding:12px 32px 14px 0;border-top:1px solid #ede9fe;border-radius:0 0 20px 0}
    .nbp-card-tag{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;background:#ede9fe;color:#6d28d9}
    .nbp-read-btn{margin-left:auto;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;font-size:12px;font-weight:700;color:white;border:none;cursor:pointer;transition:.2s}
    .nbp-read-btn:hover{opacity:.88;transform:translateY(-1px)}
    .nbp-section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .nbp-section-title{font-size:22px;font-weight:800;color:#1a202c;display:flex;align-items:center;gap:10px}
    .nbp-section-title i{color:#7c3aed}
    .nbp-count-badge{background:linear-gradient(135deg,#4c1d95,#7c3aed);color:white;border-radius:20px;padding:5px 16px;font-size:13px;font-weight:700}
    .nbp-urgent-banner{background:linear-gradient(135deg,#7f1d1d,#dc2626);border-radius:16px;padding:18px 28px;margin-bottom:28px;display:flex;align-items:center;gap:16px;color:white;box-shadow:0 6px 20px rgba(220,38,38,.3)}
    .nbp-urgent-banner i.nbp-pulse{font-size:28px;flex-shrink:0;animation:nbpPulse 1.5s ease-in-out infinite}
    @keyframes nbpPulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.7;transform:scale(1.1)}}
    .nbp-urgent-banner-text h3{font-size:16px;font-weight:800;margin:0 0 4px}
    .nbp-urgent-banner-text p{font-size:13px;opacity:.88;margin:0}
    /* ADMIN BAR */
    .nbp-admin-bar{display:flex;gap:8px;margin-top:12px;padding-top:10px;border-top:1px dashed #e0e6ef;flex-wrap:wrap}
    .nbp-abtn{padding:7px 16px;border:none;border-radius:8px;cursor:pointer;font-size:12px;font-weight:700;display:inline-flex;align-items:center;gap:5px;transition:.2s}
    .nbp-abtn-edit{background:#fef3c7;color:#92400e}.nbp-abtn-edit:hover{background:#f59e0b;color:#fff}
    .nbp-abtn-tog{background:#d1fae5;color:#065f46}.nbp-abtn-tog:hover{background:#10b981;color:#fff}
    .nbp-abtn-del{background:#fee2e2;color:#991b1b}.nbp-abtn-del:hover{background:#dc2626;color:#fff}
    /* DETAIL MODAL */
    .nbp-detail-overlay{position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:9998;display:none;align-items:center;justify-content:center;backdrop-filter:blur(5px)}
    .nbp-detail-overlay.open{display:flex}
    .nbp-detail-modal{background:white;border-radius:24px;width:100%;max-width:640px;overflow:hidden;box-shadow:0 28px 70px rgba(0,0,0,.35);animation:nbpModalIn .25s ease;max-height:90vh;display:flex;flex-direction:column}
    .nbp-detail-head{padding:28px 32px 22px;color:white;display:flex;justify-content:space-between;align-items:flex-start;flex-shrink:0}
    .nbp-detail-head h2{margin:0 0 8px;font-size:22px;font-weight:900;line-height:1.3}
    .nbp-detail-head .nbp-detail-meta{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
    .nbp-detail-close{background:rgba(255,255,255,.2);border:none;color:white;width:36px;height:36px;border-radius:50%;cursor:pointer;font-size:16px;flex-shrink:0;transition:.2s}
    .nbp-detail-close:hover{background:rgba(255,255,255,.38)}
    .nbp-detail-body{padding:28px 32px;overflow-y:auto;flex:1}
    .nbp-detail-body p{font-size:15px;color:#374151;line-height:1.9;white-space:pre-line;margin:0}
    .nbp-detail-foot{padding:18px 32px;border-top:1px solid #f0f0f0;background:#fafafa;display:flex;justify-content:flex-end;flex-shrink:0}
    .nbp-detail-close-btn{padding:11px 28px;background:linear-gradient(135deg,#4c1d95,#7c3aed);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:7px}
    .nbp-detail-close-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(124,58,237,.35)}
    .nbp-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
    .nbp-modal-overlay.open{display:flex}
    .nbp-modal{background:white;border-radius:20px;width:100%;max-width:560px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.3);animation:nbpModalIn .25s ease}
    @keyframes nbpModalIn{from{transform:translateY(-24px) scale(.97);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
    .nbp-modal-head{background:linear-gradient(135deg,#4c1d95,#7c3aed);padding:22px 28px;color:white;display:flex;justify-content:space-between;align-items:center}
    .nbp-modal-head h3{margin:0;font-size:18px;font-weight:800;display:flex;align-items:center;gap:10px}
    .nbp-modal-close{background:rgba(255,255,255,.2);border:none;color:white;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:15px;transition:.2s}
    .nbp-modal-close:hover{background:rgba(255,255,255,.35)}
    .nbp-modal-body{padding:28px;max-height:70vh;overflow-y:auto}
    .nbp-fg{margin-bottom:16px}
    .nbp-fg label{display:block;font-size:12px;font-weight:700;color:#555;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
    .nbp-fc{width:100%;padding:11px 14px;border:2px solid #e0e6ef;border-radius:10px;font-size:14px;font-family:inherit;transition:.2s;background:#fff;color:#333}
    .nbp-fc:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,.12)}
    textarea.nbp-fc{resize:vertical;min-height:90px}
    .nbp-frow{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .nbp-modal-foot{padding:18px 28px;border-top:1px solid #f0f0f0;display:flex;gap:10px;justify-content:flex-end;background:#fafafa}
    .nbp-mbtn{padding:11px 26px;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;transition:.2s;display:flex;align-items:center;gap:7px}
    .nbp-mbtn-save{background:linear-gradient(135deg,#4c1d95,#7c3aed);color:white}
    .nbp-mbtn-save:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(124,58,237,.35)}
    .nbp-mbtn-cancel{background:#f0f0f0;color:#555}
    .nbp-mbtn-cancel:hover{background:#e0e0e0}
    .nbp-modal-alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;display:none}
    .nbp-modal-alert.ok{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
    .nbp-modal-alert.err{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5}
    @media(max-width:768px){.nbp-main{padding:24px 16px 60px}.nbp-hero-inner h1{font-size:30px}.nbp-card-icon-col{width:60px}.nbp-cat-icon{width:40px;height:40px;font-size:18px}.nbp-card-content{padding:16px 16px 16px 0}.nbp-card-title{font-size:16px}.nbp-frow{grid-template-columns:1fr}}
    @media(max-width:480px){.nbp-card-inner{flex-direction:column}.nbp-card-icon-col{width:100%;padding:18px 20px 0;flex-direction:row;justify-content:flex-start;gap:12px;align-items:center}.nbp-card-content{padding:12px 18px 18px}.nbp-card-date{margin-left:0}}
  </style>

  <div class="nbp-wrap">
    <!-- HERO -->
    <div class="nbp-hero">
      <div class="nbp-hero-inner">
        <div class="nbp-hero-icon"><i class="fa fa-bullhorn"></i></div>
        <h1>Notice Board</h1>
        <p>Stay updated with the latest announcements, exam schedules, events and important dates at SCTI</p>
        <div class="nbp-hero-stats" id="nbpCounters" style="display:none">
          <div class="nbp-stat-pill" id="nbpPillAll" onclick="nbpPillFilter('','nbpPillAll')"><i class="fa fa-bell"></i><span id="nbpTotal">0</span>&nbsp;Total</div>
          <div class="nbp-stat-pill" id="nbpPillUrgent" onclick="nbpPillFilter('urgent','nbpPillUrgent')"><i class="fa fa-circle-exclamation"></i><span id="nbpUrgent">0</span>&nbsp;Urgent</div>
          <div class="nbp-stat-pill" id="nbpPillRecent" onclick="nbpPillFilter('recent','nbpPillRecent')"><i class="fa fa-calendar-check"></i><span id="nbpRecent">0</span>&nbsp;This Month</div>
        </div>
        <div class="nbp-search-wrap">
          <div class="nbp-search-row">
            <input type="text" id="nbpSearchInput" placeholder="Search notices by title or keyword..." oninput="nbpSearch(this.value)">
            <button onclick="nbpSearch(document.getElementById('nbpSearchInput').value)"><i class="fa fa-search"></i> Search</button>
          </div>
        </div>
      </div>
      <div class="nbp-hero-wave">
        <svg viewBox="0 0 1440 60" preserveAspectRatio="none">
          <path d="M0,30 C480,60 960,0 1440,30 L1440,60 L0,60 Z" fill="#f0f2f8"/>
        </svg>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="nbp-filter-bar">
      <div class="nbp-filters">
        <div class="nbp-filter-left">
          <span class="nbp-filter-label">Filter:</span>
          <button class="nbp-filter nbp-active" onclick="nbpFilter(this,'')" type="button"><i class="fa fa-border-all"></i> All</button>
          <button class="nbp-filter" onclick="nbpFilter(this,'urgent')" type="button"><i class="fa fa-circle-exclamation"></i> Urgent</button>
          <button class="nbp-filter" onclick="nbpFilter(this,'admission')" type="button"><i class="fa fa-door-open"></i> Admission</button>
          <button class="nbp-filter" onclick="nbpFilter(this,'exam')" type="button"><i class="fa fa-pen-to-square"></i> Exam</button>
          <button class="nbp-filter" onclick="nbpFilter(this,'event')" type="button"><i class="fa fa-calendar-days"></i> Events</button>
          <button class="nbp-filter" onclick="nbpFilter(this,'holiday')" type="button"><i class="fa fa-umbrella-beach"></i> Holiday</button>
          <button class="nbp-filter" onclick="nbpFilter(this,'general')" type="button"><i class="fa fa-info-circle"></i> General</button>
        </div>
        <button class="nbp-add-btn" id="nbpAddBtn" style="display:none" onclick="nbpOpenModal(0)">
          <i class="fa fa-plus"></i> Create Notice
        </button>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="nbp-main">
      <div id="nbpLoading" class="nbp-loading">
        <div class="nbp-dots"><span></span><span></span><span></span></div>
        <p>Loading notices...</p>
      </div>
      <div id="nbpBoard" style="display:none">
        <div id="nbpUrgentBanner"></div>
        <div class="nbp-section-header">
          <div class="nbp-section-title"><i class="fa fa-list-ul"></i> All Notices</div>
          <div class="nbp-count-badge" id="nbpCountLabel">0 notices</div>
        </div>
        <div id="nbpList" class="nbp-list"></div>
      </div>
      <div id="nbpEmpty" class="nbp-empty" style="display:none">
        <i class="fa fa-bullhorn"></i>
        <h3>No Notices Found</h3>
        <p>No notices in this category right now.</p>
      </div>
    </div>

    <footer style="background:#1e0a4e;color:rgba(255,255,255,.7);text-align:center;padding:20px;font-size:13px;border-top:1px solid rgba(255,255,255,.1)">
      <p>© 2025 SCTI &nbsp;|&nbsp;
        <a href="#" onclick="loadPage('home');return false;" style="color:#a78bfa;text-decoration:none">Home</a> &nbsp;|&nbsp;
        <a href="#" onclick="loadPage('contact');return false;" style="color:#a78bfa;text-decoration:none">Contact Us</a>
      </p>
    </footer>
  </div>

  <!-- DETAIL MODAL -->
  <div class="nbp-detail-overlay" id="nbpDetailModal">
    <div class="nbp-detail-modal">
      <div class="nbp-detail-head" id="nbpDetailHead">
        <div style="flex:1;min-width:0">
          <h2 id="nbpDetailTitle"></h2>
          <div class="nbp-detail-meta" id="nbpDetailMeta"></div>
        </div>
        <button class="nbp-detail-close" onclick="nbpCloseDetail()"><i class="fa fa-times"></i></button>
      </div>
      <div class="nbp-detail-body">
        <p id="nbpDetailDesc"></p>
      </div>
      <div class="nbp-detail-foot">
        <button class="nbp-detail-close-btn" onclick="nbpCloseDetail()"><i class="fa fa-check"></i> Close</button>
      </div>
    </div>
  </div>

  <!-- CREATE / EDIT MODAL -->
  <div class="nbp-modal-overlay" id="nbpModal">
    <div class="nbp-modal">
      <div class="nbp-modal-head">
        <h3 id="nbpModalTitle"><i class="fa fa-plus-circle"></i> Create Notice</h3>
        <button class="nbp-modal-close" onclick="nbpCloseModal()"><i class="fa fa-times"></i></button>
      </div>
      <div class="nbp-modal-body">
        <div class="nbp-modal-alert" id="nbpModalAlert"></div>
        <input type="hidden" id="nbpFId" value="0">
        <div class="nbp-fg">
          <label>Title *</label>
          <input type="text" id="nbpFTitle" class="nbp-fc" placeholder="Notice title...">
        </div>
        <div class="nbp-fg">
          <label>Description *</label>
          <textarea id="nbpFDesc" class="nbp-fc" placeholder="Full notice content..."></textarea>
        </div>
        <div class="nbp-frow">
          <div class="nbp-fg">
            <label>Category</label>
            <select id="nbpFCat" class="nbp-fc">
              <option value="general">General</option>
              <option value="admission">Admission</option>
              <option value="exam">Exam</option>
              <option value="event">Event</option>
              <option value="holiday">Holiday</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          <div class="nbp-fg">
            <label>Priority</label>
            <select id="nbpFPri" class="nbp-fc">
              <option value="normal">Normal</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
        </div>
        <div class="nbp-frow">
          <div class="nbp-fg">
            <label>Date</label>
            <input type="date" id="nbpFDate" class="nbp-fc">
          </div>
          <div class="nbp-fg">
            <label>Status</label>
            <select id="nbpFStat" class="nbp-fc">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="nbp-modal-foot">
        <button class="nbp-mbtn nbp-mbtn-cancel" onclick="nbpCloseModal()"><i class="fa fa-times"></i> Cancel</button>
        <button class="nbp-mbtn nbp-mbtn-save" onclick="nbpSaveNotice()"><i class="fa fa-save"></i> <span id="nbpSaveTxt">Save Notice</span></button>
      </div>
    </div>
  </div>
`;

// =============================================
//  NOTICE BOARD — JS FUNCTIONS
// =============================================
let _nbpAll = [], _nbpFiltered = [], _nbpCat = '', _nbpSearch = '', _nbpIsAdmin = false;

const _nbpCatCfg = {
  urgent:    { icon:'fa-circle-exclamation', bg:'#dc2626', label:'Urgent' },
  exam:      { icon:'fa-pen-to-square',      bg:'#3b82f6', label:'Exam' },
  admission: { icon:'fa-door-open',          bg:'#10b981', label:'Admission' },
  event:     { icon:'fa-calendar-days',      bg:'#f97316', label:'Event' },
  holiday:   { icon:'fa-umbrella-beach',     bg:'#eab308', label:'Holiday' },
  general:   { icon:'fa-info-circle',        bg:'#06b6d4', label:'General' }
};

function nbpFilter(btn, cat) {
  _nbpCat = cat;
  document.querySelectorAll('.nbp-filter').forEach(b => b.classList.remove('nbp-active'));
  btn.classList.add('nbp-active');
  nbpApply();
}

function nbpSearch(val) {
  _nbpSearch = (val || '').toLowerCase().trim();
  nbpApply();
}

function nbpApply() {
  _nbpFiltered = _nbpAll.filter(n => {
    // Public board: only show notices meant for everyone or emergency
    const aud = (n.audience || 'all').toLowerCase();
    if (aud === 'student' || aud === 'teacher') return false;
    const catMatch = !_nbpCat || n.category === _nbpCat || (_nbpCat === 'urgent' && n.priority === 'urgent');
    const q = _nbpSearch;
    const textMatch = !q || (n.title||'').toLowerCase().includes(q) || (n.description||'').toLowerCase().includes(q);
    return catMatch && textMatch;
  });
  nbpRender();
}

function initNotices() {
  document.getElementById('nbpLoading').style.display = 'flex';
  document.getElementById('nbpBoard').style.display = 'none';
  document.getElementById('nbpEmpty').style.display = 'none';

  // Check admin status
  fetch('pages/session-info.php')
    .then(r => r.json())
    .then(s => {
      _nbpIsAdmin = (s.user_type === 'admin');
      if (_nbpIsAdmin) {
        const btn = document.getElementById('nbpAddBtn');
        if (btn) btn.style.display = 'inline-flex';
      }
    }).catch(() => {});

  fetch('pages/notice-list.php?status=active')
    .then(r => r.json())
    .then(data => {
      _nbpAll = Array.isArray(data) ? data : (data.notices || []);
      nbpUpdateCounters();
      nbpApply();
      document.getElementById('nbpLoading').style.display = 'none';
    })
    .catch(() => {
      document.getElementById('nbpLoading').style.display = 'none';
      document.getElementById('nbpEmpty').style.display = 'flex';
    });
}

function nbpUpdateCounters() {
  const total  = _nbpAll.length;
  const urgent = _nbpAll.filter(n => n.priority === 'urgent' || n.category === 'urgent').length;
  const now    = new Date();
  const recent = _nbpAll.filter(n => {
    const d = new Date(n.notice_date || n.date || n.created_at || 0);
    return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth();
  }).length;

  const el = document.getElementById('nbpCounters');
  if (el) {
    el.style.display = 'flex';
    document.getElementById('nbpTotal').textContent  = total;
    document.getElementById('nbpUrgent').textContent = urgent;
    document.getElementById('nbpRecent').textContent = recent;
  }
}

function nbpRender() {
  const list  = document.getElementById('nbpList');
  const board = document.getElementById('nbpBoard');
  const empty = document.getElementById('nbpEmpty');
  const label = document.getElementById('nbpCountLabel');
  const banner= document.getElementById('nbpUrgentBanner');

  if (!_nbpFiltered.length) {
    board.style.display = 'none';
    empty.style.display = 'flex';
    return;
  }

  board.style.display = 'block';
  empty.style.display = 'none';
  label.textContent = _nbpFiltered.length + ' notice' + (_nbpFiltered.length !== 1 ? 's' : '');

  // Urgent banner
  const urgents = _nbpFiltered.filter(n => n.priority === 'urgent' || n.category === 'urgent');
  if (urgents.length && !_nbpCat) {
    banner.innerHTML = `<i class="fa fa-triangle-exclamation nbp-pulse"></i>
      <div class="nbp-urgent-banner-text">
        <h3>⚠ ${urgents.length} Urgent Notice${urgents.length > 1 ? 's' : ''}</h3>
        <p>${urgents.map(u => u.title).join(' &nbsp;|&nbsp; ')}</p>
      </div>`;
    banner.className = 'nbp-urgent-banner';
  } else {
    banner.innerHTML = '';
    banner.className = '';
  }

  list.innerHTML = _nbpFiltered.map(n => nbpBuildCard(n)).join('');
}

function nbpBuildCard(n) {
  const cat  = (n.category || 'general').toLowerCase();
  const pri  = (n.priority  || 'normal').toLowerCase();
  const cfg  = _nbpCatCfg[cat] || _nbpCatCfg.general;
  const date = nbpFmtDate(n.notice_date || n.date || n.created_at);
  const isActive = (n.status || 'active') === 'active';

  const priBadge = pri === 'urgent'
    ? `<span class="nbp-badge nbp-badge-urgent"><i class="fa fa-circle-exclamation"></i> Urgent</span>`
    : pri === 'high'
    ? `<span class="nbp-badge nbp-badge-high"><i class="fa fa-arrow-up"></i> High</span>`
    : `<span class="nbp-badge nbp-badge-normal"><i class="fa fa-circle"></i> Normal</span>`;

  const catBadge = `<span class="nbp-badge nbp-badge-cat" style="background:${cfg.bg}">
    <i class="fa ${cfg.icon}"></i> ${cfg.label}
  </span>`;

  const adminBar = _nbpIsAdmin ? `
    <div class="nbp-admin-bar">
      <button class="nbp-abtn nbp-abtn-edit" onclick="nbpEditNotice(${n.id})"><i class="fa fa-pen"></i> Edit</button>
      <button class="nbp-abtn nbp-abtn-tog" onclick="nbpToggleNotice(${n.id},'${isActive ? 'inactive' : 'active'}')">
        <i class="fa fa-${isActive ? 'eye-slash' : 'eye'}"></i> ${isActive ? 'Deactivate' : 'Activate'}
      </button>
      <button class="nbp-abtn nbp-abtn-del" onclick="nbpDeleteNotice(${n.id})"><i class="fa fa-trash"></i> Delete</button>
    </div>` : '';

  const opacity = isActive ? '1' : '0.55';

  return `
  <div class="nbp-card nbp-${cat}-card" id="nbpCard${n.id}" style="opacity:${opacity}">
    <div class="nbp-card-accent" style="background:${cfg.bg}"></div>
    <div class="nbp-card-inner">
      <div class="nbp-card-icon-col">
        <div class="nbp-cat-icon" style="background:${cfg.bg}">
          <i class="fa ${cfg.icon}"></i>
        </div>
      </div>
      <div class="nbp-card-content">
        <div class="nbp-card-meta">
          ${catBadge}
          ${priBadge}
          <span class="nbp-card-date"><i class="fa fa-calendar"></i> ${date}</span>
        </div>
        <div class="nbp-card-title">${nbpEsc(n.title)}</div>
        <div class="nbp-card-desc">${nbpEsc(n.description || '')}</div>
        <div class="nbp-card-footer">
          <span class="nbp-card-tag"><i class="fa fa-tag"></i> ${cfg.label}</span>
          ${!isActive ? '<span class="nbp-card-tag" style="background:#fee2e2;color:#991b1b"><i class="fa fa-eye-slash"></i> Inactive</span>' : ''}
          <button class="nbp-read-btn" style="background:${cfg.bg}" onclick="nbpOpenDetail(${n.id})">
            <i class="fa fa-book-open"></i> Read More
          </button>
        </div>
        ${adminBar}
      </div>
    </div>
  </div>`;
}

function nbpOpenModal(id) {
  const overlay = document.getElementById('nbpModal');
  const alert   = document.getElementById('nbpModalAlert');
  alert.style.display = 'none';
  alert.className = 'nbp-modal-alert';

  if (id === 0) {
    // Create mode
    document.getElementById('nbpModalTitle').innerHTML = '<i class="fa fa-plus-circle"></i> Create Notice';
    document.getElementById('nbpSaveTxt').textContent = 'Save Notice';
    document.getElementById('nbpFId').value    = '0';
    document.getElementById('nbpFTitle').value = '';
    document.getElementById('nbpFDesc').value  = '';
    document.getElementById('nbpFCat').value   = 'general';
    document.getElementById('nbpFPri').value   = 'normal';
    document.getElementById('nbpFStat').value  = 'active';
    document.getElementById('nbpFDate').value  = new Date().toISOString().split('T')[0];
  }
  overlay.classList.add('open');
}

function nbpCloseModal() {
  document.getElementById('nbpModal').classList.remove('open');
}

function nbpSaveNotice() {
  const id    = document.getElementById('nbpFId').value;
  const title = document.getElementById('nbpFTitle').value.trim();
  const desc  = document.getElementById('nbpFDesc').value.trim();
  const cat   = document.getElementById('nbpFCat').value;
  const pri   = document.getElementById('nbpFPri').value;
  const stat  = document.getElementById('nbpFStat').value;
  const date  = document.getElementById('nbpFDate').value;
  const alert = document.getElementById('nbpModalAlert');

  if (!title || !desc) {
    alert.textContent = 'Title and description are required.';
    alert.className = 'nbp-modal-alert err';
    alert.style.display = 'block';
    return;
  }

  const btn = document.querySelector('.nbp-mbtn-save');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

  const payload = { title, description: desc, category: cat, priority: pri, status: stat, notice_date: date };
  if (id !== '0') payload.id = parseInt(id);

  fetch('pages/notice-save.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
    .then(r => r.json())
    .then(res => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-save"></i> <span id="nbpSaveTxt">Save Notice</span>';
      if (res.success) {
        nbpCloseModal();
        nbpShowToast(id === '0' ? 'Notice created!' : 'Notice updated!', 'ok');
        initNotices();
      } else {
        alert.textContent = res.message || 'Failed to save notice.';
        alert.className = 'nbp-modal-alert err';
        alert.style.display = 'block';
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa fa-save"></i> <span id="nbpSaveTxt">Save Notice</span>';
      alert.textContent = 'Network error. Please try again.';
      alert.className = 'nbp-modal-alert err';
      alert.style.display = 'block';
    });
}

function nbpEditNotice(id) {
  const n = _nbpAll.find(x => x.id == id);
  if (!n) return;
  document.getElementById('nbpModalTitle').innerHTML = '<i class="fa fa-pen"></i> Edit Notice';
  document.getElementById('nbpSaveTxt').textContent = 'Update Notice';
  document.getElementById('nbpFId').value    = n.id;
  document.getElementById('nbpFTitle').value = n.title || '';
  document.getElementById('nbpFDesc').value  = n.description || '';
  document.getElementById('nbpFCat').value   = n.category || 'general';
  document.getElementById('nbpFPri').value   = n.priority  || 'normal';
  document.getElementById('nbpFStat').value  = n.status    || 'active';
  document.getElementById('nbpFDate').value  = (n.date || n.created_at || '').split(' ')[0] || new Date().toISOString().split('T')[0];
  nbpOpenModal(id);
}

function nbpToggleNotice(id, newStatus) {
  const n = _nbpAll.find(x => x.id == id);
  if (!n) return;
  const payload = {
    id: parseInt(n.id), title: n.title, description: n.description,
    category: n.category, priority: n.priority,
    notice_date: (n.notice_date || n.date || '').split(' ')[0] || new Date().toISOString().split('T')[0],
    status: newStatus
  };
  fetch('pages/notice-save.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        nbpShowToast('Notice ' + newStatus + '!', 'ok');
        initNotices();
      } else {
        nbpShowToast(res.message || 'Failed.', 'err');
      }
    });
}

function nbpDeleteNotice(id) {
  if (!confirm('Delete this notice? This cannot be undone.')) return;
  fetch('pages/notice-delete.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: parseInt(id) }) })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        nbpShowToast('Notice deleted.', 'ok');
        initNotices();
      } else {
        nbpShowToast(res.message || 'Failed.', 'err');
      }
    });
}

function nbpShowToast(msg, type) {
  let t = document.getElementById('nbpToast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'nbpToast';
    t.style.cssText = 'position:fixed;bottom:28px;right:28px;z-index:99999;padding:14px 24px;border-radius:12px;font-size:14px;font-weight:700;color:white;box-shadow:0 8px 24px rgba(0,0,0,.2);transition:opacity .3s;pointer-events:none';
    document.body.appendChild(t);
  }
  t.textContent = msg;
  t.style.background = type === 'ok' ? '#10b981' : '#dc2626';
  t.style.opacity = '1';
  clearTimeout(t._to);
  t._to = setTimeout(() => { t.style.opacity = '0'; }, 3000);
}

function nbpScrollTop() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nbpOpenDetail(id) {
  const n = _nbpAll.find(x => x.id == id);
  if (!n) return;
  const cat = (n.category || 'general').toLowerCase();
  const cfg = _nbpCatCfg[cat] || _nbpCatCfg.general;
  const pri = (n.priority || 'normal').toLowerCase();
  const date = nbpFmtDate(n.notice_date || n.date || n.created_at);

  document.getElementById('nbpDetailHead').style.background = `linear-gradient(135deg, ${cfg.bg}cc, ${cfg.bg})`;
  document.getElementById('nbpDetailTitle').textContent = n.title || '';
  document.getElementById('nbpDetailDesc').textContent = n.description || '';
  document.getElementById('nbpDetailMeta').innerHTML = `
    <span class="nbp-badge nbp-badge-cat" style="background:rgba(255,255,255,.25);color:white"><i class="fa ${cfg.icon}"></i> ${cfg.label}</span>
    <span class="nbp-badge" style="background:rgba(255,255,255,.2);color:white"><i class="fa fa-calendar"></i> ${date}</span>
    ${pri === 'urgent' ? '<span class="nbp-badge" style="background:rgba(255,255,255,.2);color:white"><i class="fa fa-circle-exclamation"></i> Urgent</span>' : ''}
  `;
  document.getElementById('nbpDetailModal').classList.add('open');
}

function nbpCloseDetail() {
  document.getElementById('nbpDetailModal').classList.remove('open');
}

function nbpPillFilter(type, pillId) {
  // Reset all pills
  ['nbpPillAll','nbpPillUrgent','nbpPillRecent'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.remove('nbp-pill-active');
  });
  const pill = document.getElementById(pillId);
  if (pill) pill.classList.add('nbp-pill-active');

  if (type === 'urgent') {
    _nbpCat = 'urgent';
    // also activate the filter bar button
    document.querySelectorAll('.nbp-filter').forEach(b => b.classList.remove('nbp-active'));
    const urgentBtn = [...document.querySelectorAll('.nbp-filter')].find(b => b.textContent.trim().includes('Urgent'));
    if (urgentBtn) urgentBtn.classList.add('nbp-active');
  } else if (type === 'recent') {
    _nbpCat = '';
    document.querySelectorAll('.nbp-filter').forEach(b => b.classList.remove('nbp-active'));
    const allBtn = document.querySelector('.nbp-filter');
    if (allBtn) allBtn.classList.add('nbp-active');
    // filter to this month only
    const now = new Date();
    _nbpFiltered = _nbpAll.filter(n => {
      const d = new Date(n.notice_date || n.date || n.created_at || 0);
      return d.getFullYear() === now.getFullYear() && d.getMonth() === now.getMonth();
    });
    nbpRender();
    document.querySelector('.nbp-main')?.scrollIntoView({ behavior: 'smooth' });
    return;
  } else {
    _nbpCat = '';
    document.querySelectorAll('.nbp-filter').forEach(b => b.classList.remove('nbp-active'));
    const allBtn = document.querySelector('.nbp-filter');
    if (allBtn) allBtn.classList.add('nbp-active');
  }
  nbpApply();
  document.querySelector('.nbp-main')?.scrollIntoView({ behavior: 'smooth' });
}

function nbpFmtDate(d) {
  if (!d) return 'N/A';
  const dt = new Date(d);
  if (isNaN(dt)) return d;
  return dt.toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
}

function nbpEsc(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
  const overlay = document.getElementById('nbpModal');
  if (overlay && e.target === overlay) nbpCloseModal();
});
