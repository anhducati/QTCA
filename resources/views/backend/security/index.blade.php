@extends('layouts.panel')

@section('main')
<div id="secDash">

  {{-- HEADER --}}
  <div class="sd-header">
    <div>
      <div class="sd-title-line">
        <span class="sd-dot"></span>
        <div class="sd-title">Trung tâm Bảo mật CA</div>
        <span class="sd-pill sd-pill-live"><span class="sd-pill-dot"></span> TRỰC TIẾP</span>
      </div>
      <div class="sd-sub">Giám sát realtime • Danh sách chặn • Nhật ký tấn công • Điều khiển khẩn</div>
    </div>

    <div class="sd-header-right">
      <span class="sd-pill sd-pill-time" id="nowTime">--:--:--</span>
      <button class="sd-btn sd-btn-primary" id="btnRefresh" type="button">
        <i class="fa fa-refresh"></i> Làm mới
      </button>
    </div>
  </div>

  {{-- TABS --}}
  <div class="sd-tabs">
    <button class="sd-tab active" data-tab="tabOverview" type="button"><i class="fa fa-dashboard"></i> Tổng quan</button>
    <button class="sd-tab" data-tab="tabBlocks" type="button"><i class="fa fa-shield"></i> Danh sách chặn</button>
    <button class="sd-tab" data-tab="tabLogs" type="button"><i class="fa fa-bug"></i> Nhật ký</button>
  </div>

  {{-- TAB: OVERVIEW --}}
  <div class="sd-pane active" id="tabOverview">

    <div class="sd-grid-3">
      {{-- PANIC --}}
      <div class="sd-card sd-metric sd-left-danger">
        <div class="sd-card-head">
          <div class="sd-card-head-left">
            <span class="sd-ic"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="sd-card-title">CHẾ ĐỘ KHẨN</div>
          </div>
          <span id="panicBadge" class="sd-badge sd-badge-danger" style="display:none;">
            <i class="fa fa-bolt"></i> ĐANG BẬT
          </span>
        </div>

        <div class="sd-card-body">
          <div class="sd-big" id="panicText">---</div>
          <div class="sd-hint">Bật/Tắt chế độ khẩn từ dashboard sẽ khóa/mở server theo cấu hình ServerStateService.</div>
          <div class="sd-actions">
            <button class="sd-btn sd-btn-danger" id="btnPanicOn" type="button">
              <i class="fa fa-power-off"></i> BẬT
            </button>
            <button class="sd-btn sd-btn-outline" id="btnPanicOff" type="button">
              <i class="fa fa-toggle-off"></i> TẮT
            </button>
          </div>
          <div class="sd-mini" id="panicMeta"></div>
        </div>
      </div>

      {{-- TRAFFIC --}}
      <div class="sd-card sd-metric sd-left-primary">
        <div class="sd-card-head">
          <div class="sd-card-head-left">
            <span class="sd-ic"><i class="fa fa-area-chart"></i></span>
            <div class="sd-card-title">LƯỢT TRUY CẬP / PHÚT</div>
          </div>
          <span class="sd-badge sd-badge-primary" id="trafficTrend">--</span>
        </div>

        <div class="sd-card-body">
          <div class="sd-row-between">
            <div class="sd-big" id="trafficVal">0</div>
            <div class="sd-spark"><canvas id="sparkTraffic" height="36"></canvas></div>
          </div>
          <div class="sd-hint">Tổng request của phút hiện tại (theo key traffic:YYYYmmddHHii).</div>
        </div>
      </div>

      {{-- BLOCKED --}}
      <div class="sd-card sd-metric sd-left-warning">
        <div class="sd-card-head">
          <div class="sd-card-head-left">
            <span class="sd-ic"><i class="fa fa-ban"></i></span>
            <div class="sd-card-title">IP ĐANG BỊ CHẶN</div>
          </div>
          <span class="sd-badge sd-badge-warning">
            <i class="fa fa-shield"></i> <span id="blockedCount">0</span> IP
          </span>
        </div>

        <div class="sd-card-body">
          <div class="sd-row-between">
            <div class="sd-big" id="blockedBig">0</div>
            <div class="sd-spark"><canvas id="sparkBlocked" height="36"></canvas></div>
          </div>
          <div class="sd-hint">Số IP trong cache blocked_ips (dashboard đọc).</div>
        </div>
      </div>
    </div>

    {{-- CHART --}}
    <div class="sd-card sd-space">
      <div class="sd-card-head">
        <div class="sd-card-head-left">
          <span class="sd-ic"><i class="fa fa-line-chart"></i></span>
          <div class="sd-card-title">Lưu lượng realtime (30 phút gần nhất)</div>
        </div>
        <div class="sd-card-head-right">
          <span class="sd-pill sd-pill-soft">Cập nhật: <span id="lastUpdate">--</span></span>
        </div>
      </div>
      <div class="sd-card-body">
        <div class="sd-chart"><canvas id="trafficChart" height="120"></canvas></div>
      </div>
    </div>

    {{-- OVERVIEW TABLES --}}
    <div class="sd-grid-2 sd-space">
      {{-- latest blocks --}}
      <div class="sd-card">
        <div class="sd-card-head">
          <div class="sd-card-head-left">
            <span class="sd-ic"><i class="fa fa-shield"></i></span>
            <div class="sd-card-title">Danh sách chặn mới nhất</div>
            <span class="sd-pill sd-pill-soft" id="blockCountPill_over">0</span>
          </div>
          <div class="sd-card-head-right">
            <button class="sd-btn sd-btn-ghost" type="button" id="gotoBlocks">
              Mở danh sách chặn <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <div class="sd-table-wrap" style="max-height:280px;">
          <table class="sd-tbl">
            <thead>
              <tr>
                <th style="width:30%;">IP</th>
                <th>Lý do</th>
                <th style="width:24%;">Hết hạn</th>
              </tr>
            </thead>
            <tbody id="blockTbody_over">
              <tr><td colspan="3" class="sd-loading">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="sd-card-foot">Hiển thị 8 bản ghi gần nhất.</div>
      </div>

      {{-- latest logs --}}
      <div class="sd-card">
        <div class="sd-card-head">
          <div class="sd-card-head-left">
            <span class="sd-ic"><i class="fa fa-bug"></i></span>
            <div class="sd-card-title">Nhật ký tấn công mới nhất</div>
            <span class="sd-pill sd-pill-soft" id="logCountPill_over">0</span>
          </div>
          <div class="sd-card-head-right">
            <button class="sd-btn sd-btn-ghost" type="button" id="gotoLogs">
              Mở nhật ký <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <div class="sd-table-wrap" style="max-height:280px;">
          <table class="sd-tbl">
            <thead>
              <tr>
                <th style="width:22%;">Loại</th>
                <th style="width:26%;">IP</th>
                <th>Thông tin</th>
              </tr>
            </thead>
            <tbody id="logTbody_over">
              <tr><td colspan="3" class="sd-loading">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="sd-card-foot">Hiển thị 8 bản ghi gần nhất.</div>
      </div>
    </div>
  </div>

  {{-- TAB: BLOCKS --}}
  <div class="sd-pane" id="tabBlocks">
    <div class="sd-card sd-space">
      <div class="sd-card-head">
        <div class="sd-card-head-left">
          <span class="sd-ic"><i class="fa fa-shield"></i></span>
          <div class="sd-card-title">Danh sách chặn</div>
          <span class="sd-pill sd-pill-soft" id="blockCountPill">0</span>
        </div>
        <div class="sd-card-head-right sd-tools">
          <div class="sd-search">
            <i class="fa fa-search"></i>
            <input id="blockSearch" type="text" placeholder="Tìm IP / lý do..." />
          </div>
        </div>
      </div>

      <div class="sd-table-wrap">
        <table class="sd-tbl">
          <thead>
            <tr>
              <th style="width:28%;">IP</th>
              <th>Lý do</th>
              <th style="width:24%;">Hết hạn</th>
              <th style="width:16%;" class="sd-right">Thao tác</th>
            </tr>
          </thead>
          <tbody id="blockTbody">
            <tr><td colspan="4" class="sd-loading">Đang tải...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="sd-card-foot">Bấm “Gỡ chặn” để gọi API admin.security.unblock.</div>
    </div>
  </div>

  {{-- TAB: LOGS --}}
  <div class="sd-pane" id="tabLogs">
    <div class="sd-card sd-space">
      <div class="sd-card-head">
        <div class="sd-card-head-left">
          <span class="sd-ic"><i class="fa fa-bug"></i></span>
          <div class="sd-card-title">Nhật ký tấn công</div>
          <span class="sd-pill sd-pill-soft" id="logCountPill">0</span>
        </div>
        <div class="sd-card-head-right sd-tools">
          <div class="sd-search">
            <i class="fa fa-filter"></i>
            <input id="logSearch" type="text" placeholder="Lọc theo loại / IP / nội dung..." />
          </div>
        </div>
      </div>

      <div class="sd-table-wrap">
        <table class="sd-tbl">
          <thead>
            <tr>
              <th style="width:18%;">Loại</th>
              <th style="width:22%;">IP</th>
              <th>Thông tin</th>
              <th style="width:18%;">Thời gian</th>
            </tr>
          </thead>
          <tbody id="logTbody">
            <tr><td colspan="4" class="sd-loading">Đang tải...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="sd-card-foot">Dữ liệu lấy từ cache security_logs (tối đa 50 bản ghi từ API).</div>
    </div>
  </div>

  {{-- TOAST --}}
  <div class="sd-toast" id="toastx">
    <div class="sd-toast-ic" id="toastIcon"><i class="fa fa-info"></i></div>
    <div>
      <div class="sd-toast-title" id="toastTitle">Thông báo</div>
      <div class="sd-toast-text" id="toastText">---</div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* scope chặt */
#secDash{
  --bg:#f6f7fb;
  --card:#ffffff;
  --stroke:#e9ecef;
  --text:#212529;
  --muted:#6c757d;
  --primary:#0d6efd;
  --success:#198754;
  --warning:#ffc107;
  --danger:#dc3545;
  --shadow: 0 6px 18px rgba(16,24,40,.08);

  background: var(--bg) !important;
  color: var(--text) !important;
  border-radius: 14px !important;
  padding: 16px !important;
  isolation: isolate !important;
}
#secDash, #secDash *{ box-sizing:border-box !important; }

#secDash .sd-header{
  display:flex !important;
  align-items:flex-start !important;
  justify-content:space-between !important;
  gap:14px !important;
  margin-bottom: 14px !important;
}
#secDash .sd-title-line{
  display:flex !important;
  align-items:center !important;
  gap:10px !important;
}
#secDash .sd-title{ font-weight:900 !important; font-size:22px !important; }
#secDash .sd-sub{ margin-top:4px !important; color:var(--muted) !important; font-size:13px !important; }
#secDash .sd-header-right{ display:flex !important; align-items:center !important; gap:10px !important; flex-wrap:wrap !important; }
#secDash .sd-dot{ width:10px !important; height:10px !important; border-radius:999px !important; background:var(--primary) !important; }

#secDash .sd-pill{
  display:inline-flex !important;
  align-items:center !important;
  gap:8px !important;
  padding:6px 10px !important;
  border-radius:999px !important;
  background:#f1f3f5 !important;
  border:1px solid var(--stroke) !important;
  font-size:12px !important;
  color:var(--text) !important;
}
#secDash .sd-pill-dot{ width:7px !important; height:7px !important; border-radius:999px !important; background:var(--success) !important; }
#secDash .sd-pill-live{ background: rgba(25,135,84,.08) !important; border-color: rgba(25,135,84,.18) !important; }
#secDash .sd-pill-time{ background: rgba(13,110,253,.08) !important; border-color: rgba(13,110,253,.18) !important; }
#secDash .sd-pill-soft{ background:#f8f9fa !important; }

#secDash .sd-tabs{
  display:flex !important;
  gap:10px !important;
  margin: 10px 0 16px !important;
  flex-wrap:wrap !important;
}
#secDash .sd-tab{
  border:1px solid var(--stroke) !important;
  background: var(--card) !important;
  color: var(--muted) !important;
  padding:9px 12px !important;
  border-radius: 10px !important;
  font-weight:900 !important;
  font-size:12px !important;
  cursor:pointer !important;
  line-height: 1 !important;
}
#secDash .sd-tab.active{
  border-color: rgba(13,110,253,.28) !important;
  color: var(--text) !important;
  box-shadow: 0 2px 10px rgba(13,110,253,.12) !important;
}

#secDash .sd-pane{ display:none !important; }
#secDash .sd-pane.active{ display:block !important; }

#secDash .sd-grid-3{
  display:grid !important;
  grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
  gap: clamp(14px, 1.6vw, 20px) !important;
  align-items: stretch !important;
}
#secDash .sd-grid-2{
  display:grid !important;
  grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  gap: clamp(14px, 1.6vw, 20px) !important;
  align-items: stretch !important;
}
#secDash .sd-space{ margin-top: clamp(14px, 1.6vw, 20px) !important; }

#secDash .sd-card{
  background: var(--card) !important;
  border:1px solid var(--stroke) !important;
  border-radius: 12px !important;
  box-shadow: var(--shadow) !important;
  overflow:hidden !important;
  min-width:0 !important;
}
#secDash .sd-card-head{
  display:flex !important;
  align-items:center !important;
  justify-content:space-between !important;
  gap:12px !important;
  padding: 12px 14px !important;
  border-bottom:1px solid var(--stroke) !important;
  background:#fff !important;
}
#secDash .sd-card-head-left{
  display:flex !important;
  align-items:center !important;
  gap:10px !important;
  min-width:0 !important;
}
#secDash .sd-card-head-right{ display:flex !important; align-items:center !important; gap:10px !important; flex-shrink:0 !important; }
#secDash .sd-card-title{
  font-weight:900 !important;
  font-size:13px !important;
  color:var(--text) !important;
  white-space:nowrap !important;
  overflow:hidden !important;
  text-overflow:ellipsis !important;
}
#secDash .sd-ic{
  width:34px !important;
  height:34px !important;
  display:grid !important;
  place-items:center !important;
  border:1px solid var(--stroke) !important;
  border-radius:10px !important;
  background:#f8f9fa !important;
  color: var(--muted) !important;
}
#secDash .sd-card-body{ padding: 12px 14px 14px !important; background:#fff !important; }
#secDash .sd-card-foot{
  padding: 10px 14px !important;
  border-top:1px solid var(--stroke) !important;
  color: var(--muted) !important;
  background:#fff !important;
  font-size: 13px !important;
}

#secDash .sd-metric{ height:100% !important; }
#secDash .sd-left-danger{ border-left:4px solid var(--danger) !important; }
#secDash .sd-left-primary{ border-left:4px solid var(--primary) !important; }
#secDash .sd-left-warning{ border-left:4px solid var(--warning) !important; }

#secDash .sd-big{ font-size: 40px !important; font-weight: 950 !important; line-height:1 !important; }
#secDash .sd-hint{ margin-top:6px !important; color: var(--muted) !important; font-size: 13px !important; }
#secDash .sd-row-between{ display:flex !important; align-items:flex-end !important; justify-content:space-between !important; gap:12px !important; }
#secDash .sd-actions{ display:flex !important; gap:10px !important; margin-top: 12px !important; flex-wrap:wrap !important; }
#secDash .sd-mini{ margin-top:10px !important; color:#6c757d !important; font-size:12px !important; }

#secDash .sd-badge{
  display:inline-flex !important;
  align-items:center !important;
  gap:8px !important;
  padding:6px 10px !important;
  border-radius: 10px !important;
  font-size:12px !important;
  font-weight:900 !important;
  border:1px solid var(--stroke) !important;
  white-space:nowrap !important;
  background:#f8f9fa !important;
}
#secDash .sd-badge-danger{ background: rgba(220,53,69,.10) !important; border-color: rgba(220,53,69,.22) !important; color:#b02a37 !important; }
#secDash .sd-badge-primary{ background: rgba(13,110,253,.10) !important; border-color: rgba(13,110,253,.22) !important; color:#0b5ed7 !important; }
#secDash .sd-badge-warning{ background: rgba(255,193,7,.18) !important; border-color: rgba(255,193,7,.30) !important; color:#997404 !important; }

#secDash .sd-btn{
  display:inline-flex !important;
  align-items:center !important;
  gap:8px !important;
  border:1px solid var(--stroke) !important;
  background: var(--card) !important;
  color: var(--text) !important;
  padding:8px 10px !important;
  border-radius: 10px !important;
  font-weight:900 !important;
  font-size:12px !important;
  cursor:pointer !important;
  white-space:nowrap !important;
  line-height: 1 !important;
}
#secDash .sd-btn:hover{ background:#f8f9fa !important; }
#secDash .sd-btn-primary{
  border-color: rgba(13,110,253,.25) !important;
  background: rgba(13,110,253,.08) !important;
  color:#0b5ed7 !important;
}
#secDash .sd-btn-danger{
  border-color: rgba(220,53,69,.25) !important;
  background: rgba(220,53,69,.10) !important;
  color:#b02a37 !important;
}
#secDash .sd-btn-outline{
  border-color: rgba(13,110,253,.25) !important;
  background: rgba(13,110,253,.06) !important;
  color:#0b5ed7 !important;
}
#secDash .sd-btn-ghost{ background:#fff !important; }
#secDash .sd-btn:disabled{ opacity:.55 !important; cursor:not-allowed !important; }

#secDash .sd-tools{
  display:flex !important;
  align-items:center !important;
  gap:10px !important;
  flex-wrap:wrap !important;
}
#secDash .sd-search{ position:relative !important; }
#secDash .sd-search i{
  position:absolute !important;
  left:10px !important;
  top:50% !important;
  transform:translateY(-50%) !important;
  color:#adb5bd !important;
  font-size:12px !important;
}
#secDash .sd-search input{
  width: 240px !important;
  padding:9px 10px 9px 30px !important;
  border-radius: 10px !important;
  border:1px solid var(--stroke) !important;
  background:#fff !important;
  color: var(--text) !important;
  outline:none !important;
}

#secDash .sd-chart{ height: 260px !important; }

#secDash .sd-table-wrap{
  max-height: 520px !important;
  overflow:auto !important;
  background:#fff !important;
}
#secDash .sd-tbl{
  width:100% !important;
  border-collapse: separate !important;
  border-spacing:0 !important;
  font-size:13px !important;
  background:#fff !important;
}
#secDash .sd-tbl thead th{
  position: sticky !important;
  top:0 !important;
  background:#fff !important;
  border-bottom:1px solid var(--stroke) !important;
  padding:10px !important;
  color: var(--muted) !important;
  font-weight:900 !important;
  z-index:2 !important;
}
#secDash .sd-tbl tbody td{
  padding:10px !important;
  border-bottom:1px solid #f1f3f5 !important;
  vertical-align: middle !important;
}
#secDash .sd-tbl tbody tr:hover{ background:#f8f9fa !important; }
#secDash .sd-right{ text-align:right !important; }
#secDash .sd-loading{ padding:18px !important; text-align:center !important; color:#6c757d !important; }

#secDash .sd-spark{
  width: 140px !important;
  height: 38px !important;
  border-radius: 10px !important;
  background:#fff !important;
  border:1px solid var(--stroke) !important;
  padding:4px 6px !important;
}

/* ✅ TOAST: mặc định ẩn + không chặn scroll */
#secDash .sd-toast{
  position: fixed !important;
  right: 18px !important;
  bottom: 18px !important;
  width: 340px !important;
  background:#fff !important;
  border:1px solid var(--stroke) !important;
  border-radius: 12px !important;
  box-shadow: var(--shadow) !important;
  gap:12px !important;
  padding:12px !important;
  z-index: 9999 !important;

  display:none !important;          /* ✅ FIX luôn hiện */
  pointer-events:none !important;   /* ✅ FIX chặn kéo */
}
#secDash .sd-toast.show{
  display:flex !important;
  pointer-events:auto !important;
}

#secDash .sd-toast-ic{
  width:40px !important;
  height:40px !important;
  border-radius: 10px !important;
  display:grid !important;
  place-items:center !important;
  background:#f8f9fa !important;
  border:1px solid var(--stroke) !important;
  color: var(--muted) !important;
}
#secDash .sd-toast-title{ font-weight:950 !important; }
#secDash .sd-toast-text{ margin-top:2px !important; color: var(--muted) !important; font-size:13px !important; }

@media (max-width: 1100px){
  #secDash .sd-grid-3{ grid-template-columns: 1fr !important; }
  #secDash .sd-grid-2{ grid-template-columns: 1fr !important; }
  #secDash .sd-search input{ width: 220px !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const URL_DATA    = @json(route('admin.security.data'));
  const URL_UNBLOCK = @json(route('admin.security.unblock'));
  const URL_PANIC   = @json(route('admin.security.panic'));

  const POLL_MS = 3000;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if(csrf) axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf;

  const $ = (id) => document.getElementById(id);

  let chart = null;
  let sparkTraffic = null;
  let sparkBlocked = null;

  let lastTraffic = null;
  let panicActive = false;

  let blockRows = [];
  let logRows = [];

  const sparkMax = 24;
  let trafficHist = [];
  let blockedHist = [];

  function setTime() {
    const d = new Date();
    const hh = String(d.getHours()).padStart(2,'0');
    const mm = String(d.getMinutes()).padStart(2,'0');
    const ss = String(d.getSeconds()).padStart(2,'0');
    $('nowTime').innerText = `${hh}:${mm}:${ss}`;
  }
  setInterval(setTime, 1000); setTime();

  function toast(type, title, text){
    const box = $('toastx');
    const icon = $('toastIcon');
    const t = $('toastTitle');
    const s = $('toastText');

    let ico = 'info', bg = '#f8f9fa', bd = '#e9ecef';
    if(type==='good'){ ico='check'; bg='rgba(25,135,84,.10)'; bd='rgba(25,135,84,.20)'; }
    if(type==='warn'){ ico='warning'; bg='rgba(255,193,7,.18)'; bd='rgba(255,193,7,.30)'; }
    if(type==='bad'){ ico='exclamation-triangle'; bg='rgba(220,53,69,.10)'; bd='rgba(220,53,69,.22)'; }

    icon.innerHTML = `<i class="fa fa-${ico}"></i>`;
    icon.style.background = bg;
    icon.style.borderColor = bd;

    t.innerText = title;
    s.innerText = text;

    box.classList.add('show');
    clearTimeout(box._t);
    box._t = setTimeout(()=> box.classList.remove('show'), 2600);
  }

  function escapeHtml(s){
    return String(s ?? '')
      .replaceAll('&','&amp;').replaceAll('<','&lt;')
      .replaceAll('>','&gt;').replaceAll('"','&quot;')
      .replaceAll("'","&#039;");
  }
  function escapeAttr(s){ return escapeHtml(s).replaceAll('\n',' '); }

  // tabs
  function openTab(tabId){
    document.querySelectorAll('#secDash .sd-tab').forEach(b=> b.classList.remove('active'));
    document.querySelectorAll('#secDash .sd-pane').forEach(p=> p.classList.remove('active'));
    const btn = document.querySelector(`#secDash .sd-tab[data-tab="${tabId}"]`);
    const pane = document.getElementById(tabId);
    if(btn) btn.classList.add('active');
    if(pane) pane.classList.add('active');
  }
  document.querySelectorAll('#secDash .sd-tab').forEach(btn=>{
    btn.addEventListener('click', ()=> openTab(btn.dataset.tab));
  });
  $('gotoBlocks')?.addEventListener('click', ()=> openTab('tabBlocks'));
  $('gotoLogs')?.addEventListener('click', ()=> openTab('tabLogs'));

  // chart
  function renderChart(chartData){
    const labels = Object.keys(chartData || {});
    const values = Object.values(chartData || {});
    const ctx = $('trafficChart').getContext('2d');

    if(!chart){
      chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Request/phút',
            data: values,
            fill: true,
            tension: 0.35,
            pointRadius: 2,
            pointHoverRadius: 4
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display:false }, tooltip:{ mode:'index', intersect:false } },
          scales: {
            x: { grid: { display:false } },
            y: { beginAtZero:true }
          }
        }
      });
    } else {
      chart.data.labels = labels;
      chart.data.datasets[0].data = values;
      chart.update();
    }
  }

  // sparklines
  function pushHist(arr, val){
    arr.push(Number(val ?? 0));
    while(arr.length > sparkMax) arr.shift();
  }

  function renderSpark(chartRef, canvasId, dataArr){
    const ctx = document.getElementById(canvasId).getContext('2d');
    const labels = dataArr.map((_,i)=> i+1);

    if(!chartRef){
      chartRef = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets: [{ data: dataArr, fill: true, tension: 0.35, pointRadius: 0, borderWidth: 2 }] },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display:false }, tooltip:{ enabled:false } },
          scales: { x: { display:false }, y: { display:false } }
        }
      });
    } else {
      chartRef.data.labels = labels;
      chartRef.data.datasets[0].data = dataArr;
      chartRef.update();
    }
    return chartRef;
  }

  function getFilteredBlocks(){
    const q = ($('blockSearch')?.value || '').toLowerCase();
    return blockRows.filter(r => {
      if(!q) return true;
      return (r.ip + ' ' + (r.reason||'') + ' ' + (r.expire_at||'')).toLowerCase().includes(q);
    });
  }
  function getFilteredLogs(){
    const q = ($('logSearch')?.value || '').toLowerCase();
    return logRows.filter(r => {
      if(!q) return true;
      return (`${r.type} ${r.ip} ${r.info} ${r.time}`).toLowerCase().includes(q);
    });
  }

  function renderBlockTable(){
    const tbody = $('blockTbody');
    if(!tbody) return;

    const filtered = getFilteredBlocks();
    $('blockCountPill').innerText = filtered.length;

    tbody.innerHTML = '';
    if(!filtered.length){
      tbody.innerHTML = `<tr><td colspan="4" class="sd-loading">Không có IP bị chặn</td></tr>`;
      return;
    }

    filtered.forEach(r => {
      tbody.insertAdjacentHTML('beforeend', `
        <tr>
          <td><i class="fa fa-globe"></i> ${escapeHtml(r.ip)}</td>
          <td>${escapeHtml(r.reason || '—')}</td>
          <td style="color:#6c757d">${escapeHtml(r.expire_at || '—')}</td>
          <td class="sd-right">
            <button class="sd-btn sd-btn-outline btn-unblock" data-ip="${escapeAttr(r.ip)}">
              <i class="fa fa-unlock"></i> Gỡ chặn
            </button>
          </td>
        </tr>
      `);
    });

    tbody.querySelectorAll('.btn-unblock').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        const ip = e.currentTarget.getAttribute('data-ip');
        const ok = await Swal.fire({
          icon:'question',
          title:'Gỡ chặn IP?',
          text:`Bạn có muốn gỡ chặn IP ${ip} không?`,
          showCancelButton:true,
          confirmButtonText:'Gỡ chặn',
          cancelButtonText:'Hủy'
        });
        if(!ok.isConfirmed) return;

        try{
          await axios.post(URL_UNBLOCK, { ip_address: ip });
          toast('good','Đã gỡ chặn', `IP ${ip} đã được gỡ chặn`);
          await loadData(false);
        }catch(err){
          console.error(err);
          toast('bad','Lỗi', 'Không gỡ chặn được IP');
        }
      });
    });
  }

  function renderLogTable(){
    const tbody = $('logTbody');
    if(!tbody) return;

    const filtered = getFilteredLogs();
    $('logCountPill').innerText = filtered.length;

    tbody.innerHTML = '';
    if(!filtered.length){
      tbody.innerHTML = `<tr><td colspan="4" class="sd-loading">Không có nhật ký</td></tr>`;
      return;
    }

    filtered.forEach(r => {
      tbody.insertAdjacentHTML('beforeend', `
        <tr>
          <td><b>${escapeHtml((r.type||'').toUpperCase())}</b></td>
          <td>${escapeHtml(r.ip)}</td>
          <td style="color:#495057">${escapeHtml(r.info)}</td>
          <td style="color:#6c757d">${escapeHtml(r.time)}</td>
        </tr>
      `);
    });
  }

  function renderOverviewTables(){
    const bTbody = $('blockTbody_over');
    const lTbody = $('logTbody_over');

    $('blockCountPill_over').innerText = blockRows.length;
    $('logCountPill_over').innerText = logRows.length;

    if(bTbody){
      bTbody.innerHTML = '';
      const topB = blockRows.slice(0, 8);
      if(!topB.length){
        bTbody.innerHTML = `<tr><td colspan="3" class="sd-loading">Không có IP bị chặn</td></tr>`;
      } else {
        topB.forEach(r=>{
          bTbody.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${escapeHtml(r.ip)}</td>
              <td style="color:#495057">${escapeHtml(r.reason || '—')}</td>
              <td style="color:#6c757d">${escapeHtml(r.expire_at || '—')}</td>
            </tr>
          `);
        });
      }
    }

    if(lTbody){
      lTbody.innerHTML = '';
      const topL = logRows.slice(0, 8);
      if(!topL.length){
        lTbody.innerHTML = `<tr><td colspan="3" class="sd-loading">Không có nhật ký</td></tr>`;
      } else {
        topL.forEach(r=>{
          lTbody.insertAdjacentHTML('beforeend', `
            <tr>
              <td><b>${escapeHtml((r.type||'').toUpperCase())}</b></td>
              <td>${escapeHtml(r.ip)}</td>
              <td style="color:#6c757d">${escapeHtml(r.info)}</td>
            </tr>
          `);
        });
      }
    }
  }

  async function setPanic(enabled){
    const label = enabled ? 'BẬT' : 'TẮT';
    const ok = await Swal.fire({
      icon: enabled ? 'warning' : 'question',
      title: `${label} CHẾ ĐỘ KHẨN?`,
      text: enabled ? 'Bật chế độ khẩn sẽ khóa server.' : 'Tắt chế độ khẩn để hệ thống hoạt động bình thường.',
      showCancelButton: true,
      confirmButtonText: label,
      cancelButtonText: 'Hủy'
    });
    if(!ok.isConfirmed) return;

    try{
      await axios.post(URL_PANIC, { enabled: !!enabled });
      toast('good','Thành công', `Đã ${enabled ? 'bật' : 'tắt'} chế độ khẩn`);
      await loadData(false);
    }catch(err){
      console.error(err);
      toast('bad','Lỗi', 'Không điều khiển được chế độ khẩn (kiểm tra API route admin.security.panic)');
    }
  }

  $('btnPanicOn')?.addEventListener('click', ()=> setPanic(true));
  $('btnPanicOff')?.addEventListener('click', ()=> setPanic(false));
  $('btnRefresh')?.addEventListener('click', ()=> loadData(true));
  $('blockSearch')?.addEventListener('input', ()=> renderBlockTable());
  $('logSearch')?.addEventListener('input', ()=> renderLogTable());

  // load data
  async function loadData(forceToast=false){
    try{
      const res = await axios.get(URL_DATA);
      const data = res.data || {};

      const isPanic = !!data.panic;
      $('panicText').innerText = isPanic ? 'BẬT' : 'TẮT';
      $('panicBadge').style.display = isPanic ? 'inline-flex' : 'none';
      $('btnPanicOn').disabled = isPanic;
      $('btnPanicOff').disabled = !isPanic;

      const meta = [];
      if(data.server_off) meta.push(`server_off: BẬT`);
      if(data.server_off_time) meta.push(`từ: ${data.server_off_time}`);
      $('panicMeta').innerText = meta.length ? meta.join(' • ') : '';

      const trafficVal = Number(data.traffic ?? 0);
      $('trafficVal').innerText = trafficVal;

      const blockedObj = data.blocked || {};
      const bcount = Object.keys(blockedObj).length;
      $('blockedCount').innerText = bcount;
      $('blockedBig').innerText = bcount;

      $('lastUpdate').innerText = data.time || '--';

      if(lastTraffic !== null){
        const diff = trafficVal - lastTraffic;
        $('trafficTrend').innerText = diff===0 ? 'ỔN ĐỊNH' : (diff>0 ? `+${diff}` : `${diff}`);
      }
      lastTraffic = trafficVal;

      if(isPanic && !panicActive){
        panicActive = true;
        Swal.fire({ icon:'error', title:'CHẾ ĐỘ KHẨN ĐANG BẬT', text:'Server đang bị khóa.' });
      }
      if(!isPanic && panicActive){
        panicActive = false;
      }

      blockRows = Object.entries(blockedObj).map(([ip, v]) => ({
        ip,
        reason: v?.reason ?? v?.threat_type ?? '—',
        expire_at: v?.expire_at ?? '—'
      }));

      logRows = (data.logs || []).map(l => ({
        type: l.type || 'info',
        ip: l.ip || '-',
        info: l.info || '-',
        time: l.time || '-'
      }));

      renderChart(data.chart || {});
      pushHist(trafficHist, trafficVal);
      pushHist(blockedHist, bcount);

      sparkTraffic = renderSpark(sparkTraffic, 'sparkTraffic', trafficHist);
      sparkBlocked = renderSpark(sparkBlocked, 'sparkBlocked', blockedHist);

      renderBlockTable();
      renderLogTable();
      renderOverviewTables();

      if(forceToast) toast('good','Cập nhật', 'Dữ liệu đã được làm mới');
    }catch(err){
      console.error(err);
      toast('bad','Lỗi', 'Không tải được dữ liệu realtime');
    }
  }

  loadData();
  setInterval(loadData, POLL_MS);
});
</script>
@endpush
