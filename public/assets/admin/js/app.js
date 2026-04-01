/* ============================================
   OZAN Admin Panel - JavaScript
   Chart.js + Modal Wizards + Interactions
   ============================================ */

// ---- Chart.js Global Config ----
if (typeof Chart !== 'undefined') {
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 12;
}

// ---- Color Palette ----
const colors = {
  teal: '#14b8a6',
  tealLight: '#5eead4',
  purple: '#a855f7',
  blue: '#3b82f6',
  pink: '#ec4899',
  orange: '#f97316',
  green: '#22c55e',
  red: '#ef4444',
};

function rgba(hex, alpha) {
  const r = parseInt(hex.slice(1, 3), 16);
  const g = parseInt(hex.slice(3, 5), 16);
  const b = parseInt(hex.slice(5, 7), 16);
  return `rgba(${r},${g},${b},${alpha})`;
}

// ---- 1. Revenue Chart (Line + Area) ----
const revenueEl = document.getElementById('revenueChart');
let revenueChart;
if (revenueEl) {
const revenueCtx = revenueEl.getContext('2d');
const revenueGradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
revenueGradient.addColorStop(0, rgba(colors.teal, 0.25));
revenueGradient.addColorStop(1, rgba(colors.teal, 0));

const revenueGradient2 = revenueCtx.createLinearGradient(0, 0, 0, 300);
revenueGradient2.addColorStop(0, rgba(colors.purple, 0.15));
revenueGradient2.addColorStop(1, rgba(colors.purple, 0));

revenueChart = new Chart(revenueCtx, {
  type: 'line',
  data: {
    labels: ['Pzt', 'Sal', 'Car', 'Per', 'Cum', 'Cmt', 'Paz'],
    datasets: [
      {
        label: 'Bu Hafta',
        data: [4200, 5800, 4900, 7200, 6100, 8400, 7600],
        borderColor: colors.teal,
        backgroundColor: revenueGradient,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: colors.teal,
        pointBorderColor: '#1a1f35',
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
        borderWidth: 2.5,
      },
      {
        label: 'Geçen Hafta',
        data: [3800, 4200, 5100, 4800, 5500, 6200, 5900],
        borderColor: colors.purple,
        backgroundColor: revenueGradient2,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: colors.purple,
        pointBorderColor: '#1a1f35',
        pointBorderWidth: 2,
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 2,
        borderDash: [5, 5],
      },
    ],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: {
        position: 'top',
        align: 'end',
        labels: {
          usePointStyle: true,
          pointStyle: 'circle',
          padding: 20,
          font: { size: 12, weight: '500' },
        },
      },
      tooltip: {
        backgroundColor: '#1a1f35',
        borderColor: 'rgba(255,255,255,0.1)',
        borderWidth: 1,
        titleFont: { weight: '600' },
        padding: 12,
        cornerRadius: 8,
        displayColors: true,
        callbacks: {
          label: (ctx) => ` ${ctx.dataset.label}: $${ctx.parsed.y.toLocaleString()}`,
        },
      },
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { font: { weight: '500' } },
      },
      y: {
        grid: { color: 'rgba(255,255,255,0.04)' },
        ticks: {
          callback: (v) => '$' + (v / 1000).toFixed(0) + 'K',
          font: { weight: '500' },
        },
      },
    },
  },
});
} // end revenueChart guard

// ---- 2. Traffic Doughnut Chart ----
const trafficEl = document.getElementById('trafficChart');
if (trafficEl) {
const trafficCtx = trafficEl.getContext('2d');
new Chart(trafficCtx, {
  type: 'doughnut',
  data: {
    labels: ['Organik', 'Direkt', 'Sosyal Medya', 'Referans', 'E-posta'],
    datasets: [{
      data: [35, 25, 20, 12, 8],
      backgroundColor: [colors.teal, colors.blue, colors.purple, colors.orange, colors.pink],
      borderColor: '#1a1f35',
      borderWidth: 3,
      hoverOffset: 8,
    }],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '72%',
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          usePointStyle: true,
          pointStyle: 'circle',
          padding: 16,
          font: { size: 11, weight: '500' },
        },
      },
      tooltip: {
        backgroundColor: '#1a1f35',
        borderColor: 'rgba(255,255,255,0.1)',
        borderWidth: 1,
        padding: 12,
        cornerRadius: 8,
        callbacks: {
          label: (ctx) => ` ${ctx.label}: ${ctx.parsed}%`,
        },
      },
    },
  },
});
} // end trafficChart guard

// ---- 3. Orders Bar Chart ----
const ordersEl = document.getElementById('ordersChart');
if (ordersEl) {
const ordersCtx = ordersEl.getContext('2d');
new Chart(ordersCtx, {
  type: 'bar',
  data: {
    labels: ['Pzt', 'Sal', 'Car', 'Per', 'Cum', 'Cmt', 'Paz'],
    datasets: [{
      label: 'Siparişler',
      data: [45, 62, 38, 71, 55, 89, 42],
      backgroundColor: [
        rgba(colors.teal, 0.7),
        rgba(colors.blue, 0.7),
        rgba(colors.purple, 0.7),
        rgba(colors.teal, 0.7),
        rgba(colors.blue, 0.7),
        rgba(colors.green, 0.7),
        rgba(colors.orange, 0.7),
      ],
      borderRadius: 8,
      borderSkipped: false,
      barThickness: 20,
    }],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#1a1f35',
        borderColor: 'rgba(255,255,255,0.1)',
        borderWidth: 1,
        padding: 12,
        cornerRadius: 8,
      },
    },
    scales: {
      x: { grid: { display: false } },
      y: {
        grid: { color: 'rgba(255,255,255,0.04)' },
        ticks: { stepSize: 20 },
      },
    },
  },
});
} // end ordersChart guard

// ---- 4. Performance Radar Chart ----
const perfEl = document.getElementById('performanceChart');
if (perfEl) {
const perfCtx = perfEl.getContext('2d');
new Chart(perfCtx, {
  type: 'radar',
  data: {
    labels: ['Hız', 'Güvenlik', 'SEO', 'Erişim', 'Performans', 'UX'],
    datasets: [
      {
        label: 'Mevcut',
        data: [88, 92, 76, 85, 90, 82],
        borderColor: colors.teal,
        backgroundColor: rgba(colors.teal, 0.15),
        pointBackgroundColor: colors.teal,
        pointBorderColor: '#1a1f35',
        pointBorderWidth: 2,
        borderWidth: 2,
      },
      {
        label: 'Hedef',
        data: [95, 95, 90, 95, 95, 95],
        borderColor: colors.purple,
        backgroundColor: rgba(colors.purple, 0.08),
        pointBackgroundColor: colors.purple,
        pointBorderColor: '#1a1f35',
        pointBorderWidth: 2,
        borderWidth: 2,
        borderDash: [4, 4],
      },
    ],
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 11 } },
      },
    },
    scales: {
      r: {
        grid: { color: 'rgba(255,255,255,0.06)' },
        angleLines: { color: 'rgba(255,255,255,0.06)' },
        pointLabels: { font: { size: 11, weight: '500' } },
        ticks: { display: false },
        suggestedMin: 0,
        suggestedMax: 100,
      },
    },
  },
});
} // end performanceChart guard

// ---- 5. Analytics Detail Chart (for modal) ----
document.getElementById('analyticsModal')?.addEventListener('shown.bs.modal', function () {
  const canvas = document.getElementById('analyticsDetailChart');
  if (canvas._chartInitialized) return;
  canvas._chartInitialized = true;

  const ctx = canvas.getContext('2d');
  const grad = ctx.createLinearGradient(0, 0, 0, 350);
  grad.addColorStop(0, rgba(colors.purple, 0.2));
  grad.addColorStop(1, rgba(colors.purple, 0));

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
      datasets: [
        {
          label: 'Sayfa Görüntülenme',
          data: [82000, 91000, 78000, 105000, 112000, 98000, 125000, 118000, 134000, 128000, 142000, 128430],
          borderColor: colors.purple,
          backgroundColor: grad,
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointHoverRadius: 7,
          borderWidth: 2.5,
        },
        {
          label: 'Tekil Ziyaretçi',
          data: [54000, 62000, 51000, 71000, 78000, 65000, 85000, 80000, 92000, 87000, 96000, 84219],
          borderColor: colors.teal,
          backgroundColor: 'transparent',
          tension: 0.4,
          pointRadius: 3,
          borderWidth: 2,
          borderDash: [5, 5],
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          position: 'top',
          align: 'end',
          labels: { usePointStyle: true, pointStyle: 'circle', padding: 20 },
        },
        tooltip: {
          backgroundColor: '#1a1f35',
          borderColor: 'rgba(255,255,255,0.1)',
          borderWidth: 1,
          padding: 12,
          cornerRadius: 8,
          callbacks: {
            label: (ctx) => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString()}`,
          },
        },
      },
      scales: {
        x: { grid: { display: false } },
        y: {
          grid: { color: 'rgba(255,255,255,0.04)' },
          ticks: { callback: (v) => (v / 1000).toFixed(0) + 'K' },
        },
      },
    },
  });
});


// ---- Revenue Chart Period Switcher ----
function updateRevenueChart(period) {
  const datasets = {
    weekly: {
      labels: ['Pzt', 'Sal', 'Car', 'Per', 'Cum', 'Cmt', 'Paz'],
      current: [4200, 5800, 4900, 7200, 6100, 8400, 7600],
      previous: [3800, 4200, 5100, 4800, 5500, 6200, 5900],
    },
    monthly: {
      labels: ['1. Hft', '2. Hft', '3. Hft', '4. Hft'],
      current: [18500, 24300, 21800, 28400],
      previous: [15200, 19800, 22100, 24600],
    },
    yearly: {
      labels: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
      current: [32000, 38000, 35000, 42000, 48000, 45000, 52000, 49000, 55000, 51000, 58000, 48290],
      previous: [28000, 32000, 30000, 36000, 40000, 38000, 44000, 42000, 47000, 45000, 50000, 42000],
    },
  };

  const data = datasets[period];
  if (!revenueChart) return;
  revenueChart.data.labels = data.labels;
  revenueChart.data.datasets[0].data = data.current;
  revenueChart.data.datasets[1].data = data.previous;
  revenueChart.update('active');

  document.querySelectorAll('.card-header-custom .btn-glass').forEach(function (btn) { btn.classList.remove('active'); });
  if (event && event.target) event.target.classList.add('active');
}


// ---- Wizard (Add User Modal) ----
let currentStep = 1;
const totalSteps = 3;

function wizardNext() {
  if (currentStep >= totalSteps) {
    // Submit
    const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
    modal.hide();
    showToast('Kullanıcı başarıyla eklendi!', 'success');
    wizardReset();
    return;
  }

  // If moving to step 3, populate confirmation
  if (currentStep === 2) {
    const inputs = document.querySelectorAll('#wizard-step-1 input');
    const selects = document.querySelectorAll('#wizard-step-2 select');
    document.getElementById('confirm-name').textContent =
      (inputs[0]?.value || '-') + ' ' + (inputs[1]?.value || '-');
    document.getElementById('confirm-email').textContent = inputs[2]?.value || '-';
    document.getElementById('confirm-role').textContent = selects[0]?.value || '-';
    document.getElementById('confirm-dept').textContent = selects[1]?.value || '-';
  }

  var hideEl = document.getElementById('wizard-step-' + currentStep);
  if (hideEl) hideEl.classList.add('d-none');
  currentStep++;
  var showEl = document.getElementById('wizard-step-' + currentStep);
  if (showEl) showEl.classList.remove('d-none');

  updateWizardUI();
}

function wizardPrev() {
  if (currentStep <= 1) return;
  var hideEl = document.getElementById('wizard-step-' + currentStep);
  if (hideEl) hideEl.classList.add('d-none');
  currentStep--;
  var showEl = document.getElementById('wizard-step-' + currentStep);
  if (showEl) showEl.classList.remove('d-none');
  updateWizardUI();
}

function updateWizardUI() {
  // Step indicators
  for (let i = 1; i <= totalSteps; i++) {
    const stepEl = document.getElementById('step' + i + '-ind');
    if (!stepEl) continue;
    stepEl.classList.remove('active', 'completed');
    if (i < currentStep) stepEl.classList.add('completed');
    else if (i === currentStep) stepEl.classList.add('active');
  }

  // Buttons
  var prevBtn = document.getElementById('wizardPrevBtn');
  if (prevBtn) prevBtn.classList.toggle('d-none', currentStep <= 1);

  const nextBtn = document.getElementById('wizardNextBtn');
  if (nextBtn) {
    if (currentStep === totalSteps) {
      nextBtn.innerHTML = '<i class="bi bi-check-lg"></i> Onayla';
    } else {
      nextBtn.innerHTML = 'İleri <i class="bi bi-arrow-right"></i>';
    }
  }
}

function wizardReset() {
  currentStep = 1;
  for (let i = 1; i <= totalSteps; i++) {
    var stepEl = document.getElementById('wizard-step-' + i);
    if (stepEl) stepEl.classList.toggle('d-none', i !== 1);
  }
  updateWizardUI();
}

// Reset wizard when modal is closed
document.getElementById('addUserModal')?.addEventListener('hidden.bs.modal', wizardReset);


// ---- Toast Notification (Global) ----
function showToast(message, type) {
  type = type || 'success';
  var existing = document.querySelector('.ca-toast');
  if (existing) existing.remove();

  var iconMap = {
    success: 'bi-check-circle-fill',
    error: 'bi-exclamation-circle-fill',
    danger: 'bi-exclamation-circle-fill',
    warning: 'bi-exclamation-triangle-fill',
    info: 'bi-info-circle-fill'
  };
  var toast = document.createElement('div');
  toast.className = 'ca-toast ca-toast-' + type;
  toast.innerHTML = '<i class="bi ' + (iconMap[type] || iconMap.info) + '"></i> ' + message;
  document.body.appendChild(toast);

  requestAnimationFrame(function () { toast.classList.add('show'); });
  setTimeout(function () {
    toast.classList.remove('show');
    setTimeout(function () { toast.remove(); }, 300);
  }, 3000);
}

// ---- Status Modal (Global) ----
function showStatusModal(type, message) {
  var modal = document.getElementById('globalStatusModal');
  if (!modal) return;

  var iconEl = document.getElementById('gsm-icon');
  var titleEl = document.getElementById('gsm-title');
  var msgEl = document.getElementById('gsm-message');
  var btnEl = document.getElementById('gsm-btn');

  var config = {
    success: { icon: 'bi-check-lg', title: 'İşlem Başarılı!', btnClass: 'btn-teal', btnText: '<i class="bi bi-check-lg"></i> Tamam' },
    danger:  { icon: 'bi-x-lg', title: 'Hata Oluştu!', btnClass: 'btn-teal avatar-gradient-red', btnText: '<i class="bi bi-x-lg"></i> Kapat' },
    warning: { icon: 'bi-exclamation-lg', title: 'Dikkat!', btnClass: 'btn-teal btn-warning-gradient', btnText: '<i class="bi bi-check-lg"></i> Tamam' },
    info:    { icon: 'bi-info-lg', title: 'Bilgilendirme', btnClass: 'btn-teal btn-info-gradient', btnText: '<i class="bi bi-check-lg"></i> Anladım' }
  };

  var c = config[type] || config.info;

  iconEl.className = 'status-modal-icon ' + type;
  iconEl.innerHTML = '<i class="bi ' + c.icon + '"></i>';
  titleEl.textContent = c.title;
  msgEl.textContent = message;
  btnEl.className = c.btnClass;
  btnEl.innerHTML = c.btnText;

  var bsModal = new bootstrap.Modal(modal);
  bsModal.show();
}

// ---- Delete Modal (Global) ----
function openDeleteModal(id, title, customUrl) {
  var modal = document.getElementById('deleteConfirmModal') || document.getElementById('deleteModal');
  var form = document.getElementById('deleteForm');
  var nameEl = document.getElementById('deleteContentTitle') || document.getElementById('deleteUserName') || document.getElementById('deleteItemName');

  if (nameEl) nameEl.textContent = title || '';
  if (form) {
    form.action = customUrl || (window.location.pathname.replace(/\/$/, '') + '/' + id);
  }

  if (modal) {
    var bsModal = new bootstrap.Modal(modal);
    bsModal.show();
  }
}

// ---- Confirm Modal (Global) ----
function openConfirmModal(options) {
  var modal = document.getElementById('globalConfirmModal');
  if (!modal) return;

  var titleEl = document.getElementById('gcm-title');
  var msgEl = document.getElementById('gcm-message');
  var confirmBtn = document.getElementById('gcm-confirm-btn');
  var iconEl = document.getElementById('gcm-icon');

  if (titleEl) titleEl.textContent = options.title || 'Emin misiniz?';
  if (msgEl) msgEl.textContent = options.message || '';
  if (confirmBtn) confirmBtn.innerHTML = options.btnHtml || '<i class="bi bi-check-lg"></i> Evet, Devam Et';

  if (iconEl && options.iconClass) {
    iconEl.className = 'status-modal-icon ' + (options.type || 'warning');
    iconEl.innerHTML = '<i class="bi ' + options.iconClass + '"></i>';
  }

  var newBtn = confirmBtn.cloneNode(true);
  confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
  newBtn.id = 'gcm-confirm-btn';

  newBtn.addEventListener('click', function () {
    bootstrap.Modal.getInstance(modal).hide();
    if (typeof options.onConfirm === 'function') {
      options.onConfirm();
    }
  });

  var bsModal = new bootstrap.Modal(modal);
  bsModal.show();
}

// ---- Slug Generation (Global) ----
function generateSlug(value, slugElId, previewElId, defaultText) {
  var slug = value
    .toLowerCase()
    .replace(/ğ/g, 'g').replace(/ü/g, 'u').replace(/ş/g, 's')
    .replace(/ı/g, 'i').replace(/ö/g, 'o').replace(/ç/g, 'c')
    .replace(/Ğ/g, 'g').replace(/Ü/g, 'u').replace(/Ş/g, 's')
    .replace(/İ/g, 'i').replace(/Ö/g, 'o').replace(/Ç/g, 'c')
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');

  var slugEl = document.getElementById(slugElId);
  var previewEl = previewElId ? document.getElementById(previewElId) : null;

  if (slugEl) slugEl.value = slug;
  if (previewEl) previewEl.textContent = slug || (defaultText || 'yeni-icerik');
}

// ---- Image Upload Preview (Global) ----
function initImagePreview(inputId, imgId, defaultId, maxSizeKB) {
  var input = document.getElementById(inputId);
  if (!input) return;

  input.addEventListener('change', function (e) {
    var file = e.target.files[0];
    if (!file) return;

    if (file.size > maxSizeKB * 1024) {
      alert('Dosya boyutu ' + (maxSizeKB >= 1024 ? (maxSizeKB / 1024) + ' MB' : maxSizeKB + ' KB') + '\'dan büyük olamaz.');
      e.target.value = '';
      return;
    }

    var reader = new FileReader();
    reader.onload = function (ev) {
      var img = document.getElementById(imgId);
      var def = document.getElementById(defaultId);
      if (img) { img.src = ev.target.result; img.classList.remove('d-none'); }
      if (def) { def.classList.add('d-none'); }
    };
    reader.readAsDataURL(file);
  });
}

// ---- Section Scroll Navigation (Global) ----
function scrollToSection(sectionId, clickedEl) {
  var target = document.getElementById(sectionId);
  if (!target) return;

  target.scrollIntoView({ behavior: 'smooth', block: 'start' });

  if (clickedEl) {
    document.querySelectorAll('.stg-nav-item').forEach(function (item) {
      item.classList.remove('active');
    });
    clickedEl.classList.add('active');
  }
}

// ---- Character Counter (Global) ----
function updateCharCounter(el, max) {
  var counter = document.getElementById(el.id + '-counter');
  if (counter) {
    var len = el.value.length;
    counter.textContent = len;
    counter.style.color = len > max ? 'var(--neon-red)' : '';
  }
}

// ---- Scroll Spy for Section Navigation (Global) ----
(function () {
  var sections = document.querySelectorAll('.card-dark[id^="section-"]');
  var navItems = document.querySelectorAll('.stg-nav-item');

  if (sections.length > 0 && navItems.length > 0) {
    window.addEventListener('scroll', function () {
      var scrollPos = window.scrollY + 140;
      for (var i = sections.length - 1; i >= 0; i--) {
        if (sections[i].offsetTop <= scrollPos) {
          navItems.forEach(function (n) { n.classList.remove('active'); });
          if (navItems[i]) navItems[i].classList.add('active');
          break;
        }
      }
    }, { passive: true });
  }
})();

// ---- Export Format Selection ----
document.querySelectorAll('#exportModal .col-6 > div').forEach((card) => {
  card.addEventListener('click', function () {
    document.querySelectorAll('#exportModal .col-6 > div').forEach((c) => {
      c.style.borderColor = 'var(--border-color)';
    });
    this.style.borderColor = 'var(--teal-primary)';
  });
});


// ---- Theme Selection (Settings Modal) ----
document.querySelectorAll('#settingsAppearance .d-flex.gap-3 > div').forEach((card, i, cards) => {
  if (card.classList.contains('color-swatch')) return;
  card.addEventListener('click', function () {
    cards.forEach((c) => (c.style.borderColor = 'var(--border-color)'));
    this.style.borderColor = 'var(--teal-primary)';
  });
});


// ---- Color Swatch Selection ----
document.querySelectorAll('.color-swatch').forEach((swatch) => {
  swatch.addEventListener('click', function () {
    document.querySelectorAll('.color-swatch').forEach((s) => s.classList.remove('active'));
    this.classList.add('active');
  });
});


// ---- Stat Cards Counter Animation ----
function animateCounters() {
  var duration = 1200;
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (isNaN(target) || target === 0) {
      el.textContent = '0';
      return;
    }

    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString('tr-TR');
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = target.toLocaleString('tr-TR');
      }
    }

    requestAnimationFrame(step);
  });
}

// Run on page load
animateCounters();

// ---- AOS (Animate on Scroll) Init ----
if (typeof AOS !== 'undefined') {
  AOS.init({
    duration: 600,
    easing: 'ease-out-cubic',
    once: true,
    offset: 50
  });
}