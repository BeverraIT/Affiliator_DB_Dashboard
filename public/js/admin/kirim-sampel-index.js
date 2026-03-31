(function () {
  'use strict';

  function ensureBootstrap() {
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap JS belum ter-load (bootstrap is undefined). Pastikan bootstrap.bundle.min.js ter-load.');
      return false;
    }
    return true;
  }

  function toast(msg) {
    if (!ensureBootstrap()) return;

    const toastEl = document.getElementById('copyToast');
    const textEl  = document.getElementById('copyToastText');
    if (!toastEl || !textEl) return;

    textEl.textContent = msg || 'OK';
    bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 1600 }).show();
  }

  // ✅ bisa dipanggil dari onclick="openDeleteModal(this)"
  window.openDeleteModal = function (btn) {
    if (!btn) return;
    if (!ensureBootstrap()) return;

    const url  = btn.getAttribute('data-url');
    const name = btn.getAttribute('data-name') || 'data ini';

    const form = document.getElementById('deleteForm');
    if (form && url) form.setAttribute('action', url);

    const nameEl = document.getElementById('modalDeleteName'); // pastikan id ini sama di blade
    if (nameEl) nameEl.textContent = name;

    const modalEl = document.getElementById('deleteModal');
    if (!modalEl) return;

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  };

  window.copyIdPesanan = function (btn) {
    if (!btn) return;

    const text = btn.getAttribute('data-copy') || '';
    if (!text) return;

    const done = () => toast('Berhasil tersalin.');
    const fail = () => toast('Gagal menyalin.');

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(done).catch(fail);
      return;
    }

    // fallback
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.top = '-9999px';
    document.body.appendChild(ta);
    ta.select();

    try {
      document.execCommand('copy');
      done();
    } catch (e) {
      fail();
    } finally {
      document.body.removeChild(ta);
    }
  };

})();