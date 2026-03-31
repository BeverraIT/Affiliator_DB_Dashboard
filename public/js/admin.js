(function () {
  // Biar bisa dipanggil dari onclick=""
  window.openDeleteModal = function (btn) {
    const url  = btn.getAttribute('data-url');
    const name = btn.getAttribute('data-name');

    const form = document.getElementById('deleteForm');
    if (form) form.setAttribute('action', url);

    const nameEl = document.getElementById('modalItemName');
    if (nameEl) nameEl.textContent = name || '';

    const modalEl = document.getElementById('deleteModal');
    if (!modalEl) return;

    // Pastikan bootstrap bundle ter-load
    if (typeof bootstrap === 'undefined') {
      console.error('Bootstrap JS belum ter-load (bootstrap is undefined).');
      return;
    }

    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  };
})();