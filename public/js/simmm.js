/**
 * SIMMM BSI - Script Pendukung Antarmuka & Interaksi
 */

function showConfirmModal(options) {
    const modal = document.getElementById('custom-confirm-modal');
    if (!modal) {
        if (confirm(options.message || 'Apakah Anda yakin ingin melanjutkan?')) {
            if (options.onConfirm) options.onConfirm();
        } else {
            if (options.onCancel) options.onCancel();
        }
        return;
    }

    const titleEl = document.getElementById('modal-confirm-title');
    const messageEl = document.getElementById('modal-confirm-message');
    const okBtn = document.getElementById('modal-ok-btn');
    const cancelBtn = document.getElementById('modal-cancel-btn');

    if (titleEl) titleEl.textContent = options.title || 'Konfirmasi Tindakan';
    if (messageEl) messageEl.textContent = options.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
    if (okBtn) okBtn.textContent = options.confirmText || 'Ya, Lanjutkan';

    if (okBtn) {
        if (options.variant === 'warning') {
            okBtn.className = 'btn btn-warning';
        } else if (options.variant === 'primary') {
            okBtn.className = 'btn btn-solid';
        } else {
            okBtn.className = 'btn btn-danger';
        }
    }

    modal.classList.add('active');

    const handleOk = () => {
        cleanup();
        if (options.onConfirm) options.onConfirm();
    };

    const handleCancel = () => {
        cleanup();
        if (options.onCancel) options.onCancel();
    };

    const cleanup = () => {
        modal.classList.remove('active');
        if (okBtn) okBtn.removeEventListener('click', handleOk);
        if (cancelBtn) cancelBtn.removeEventListener('click', handleCancel);
    };

    if (okBtn) okBtn.addEventListener('click', handleOk);
    if (cancelBtn) cancelBtn.addEventListener('click', handleCancel);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        const input = document.getElementById(toggle.dataset.passwordToggle);
        if (!input) return;

        toggle.addEventListener('click', () => {
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            toggle.setAttribute('aria-label', isVisible ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
            toggle.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
            toggle.classList.toggle('is-visible', !isVisible);
        });
    });

    const termsModal = document.getElementById('terms-modal');
    const termsLink = document.querySelector('[data-open-terms]');
    const termsClose = termsModal?.querySelectorAll('[data-close-terms]');
    const setTermsModal = (isOpen) => {
        if (!termsModal) return;
        termsModal.classList.toggle('active', isOpen);
        termsModal.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        document.body.classList.toggle('modal-open', isOpen);
    };

    termsLink?.addEventListener('click', (e) => {
        e.preventDefault();
        setTermsModal(true);
    });
    termsClose?.forEach((element) => element.addEventListener('click', () => setTermsModal(false)));
    termsModal?.addEventListener('click', (e) => {
        if (e.target === termsModal) setTermsModal(false);
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setTermsModal(false);
    });

    // Global Custom HTML5 Validation Messages in Indonesian
    document.addEventListener('invalid', (e) => {
        const el = e.target;
        if (el.validity.valueMissing) {
            if (el.type === 'file') {
                const fieldTitle = el.closest('.file-dropzone')?.querySelector('.dz-title')?.innerText?.replace(/\*.*$/, '').trim() || 'file';
                el.setCustomValidity(`Silakan pilih file ${fieldTitle} terlebih dahulu.`);
            } else if (el.tagName === 'SELECT') {
                el.setCustomValidity('Silakan pilih salah satu pilihan pada daftar ini.');
            } else if (el.type === 'checkbox') {
                el.setCustomValidity('Silakan centang kotak ini untuk melanjutkan.');
            } else if (el.type === 'radio') {
                el.setCustomValidity('Silakan pilih salah satu opsi.');
            } else {
                el.setCustomValidity('Kolom ini wajib diisi.');
            }
        } else if (el.validity.typeMismatch) {
            if (el.type === 'email') {
                el.setCustomValidity('Silakan masukkan alamat email yang valid.');
            } else if (el.type === 'url') {
                el.setCustomValidity('Silakan masukkan alamat web URL yang valid.');
            }
        } else if (el.validity.tooShort) {
            el.setCustomValidity(`Isian terlalu pendek (minimal ${el.minLength} karakter).`);
        } else if (el.validity.tooLong) {
            el.setCustomValidity(`Isian terlalu panjang (maksimal ${el.maxLength} karakter).`);
        }
    }, true);

    document.addEventListener('input', (e) => {
        e.target.setCustomValidity('');
    }, true);

    document.addEventListener('change', (e) => {
        e.target.setCustomValidity('');
    }, true);

    // Form submit confirmation dialog
    document.addEventListener('submit', (e) => {
        const form = e.target;
        const confirmMsg = form.getAttribute('data-confirm');
        if (confirmMsg && !form.dataset.confirmed) {
            e.preventDefault();
            showConfirmModal({
                title: form.getAttribute('data-title') || 'Konfirmasi Hapus',
                message: confirmMsg,
                confirmText: form.getAttribute('data-button') || 'Ya, Hapus',
                variant: form.getAttribute('data-variant') || 'danger',
                onConfirm: () => {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        }
    });

    // Mobile sidebar toggle
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('app-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (toggleBtn && sidebar && overlay) {
        const toggleSidebar = () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        };
        toggleBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    }

    // Global Navbar Profile Dropdown
    const profileBtn = document.getElementById('gnav-profile-btn');
    const profileDropdown = document.getElementById('gnav-dropdown');
    const profileChevron = document.getElementById('gnav-chevron');
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = profileDropdown.classList.toggle('active');
            profileBtn.setAttribute('aria-expanded', isActive ? 'true' : 'false');
            if (profileChevron) profileChevron.style.transform = isActive ? 'rotate(180deg)' : 'rotate(0deg)';
        });

        document.addEventListener('click', (e) => {
            if (profileDropdown.classList.contains('active') && !profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.remove('active');
                profileBtn.setAttribute('aria-expanded', 'false');
                if (profileChevron) profileChevron.style.transform = 'rotate(0deg)';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && profileDropdown.classList.contains('active')) {
                profileDropdown.classList.remove('active');
                profileBtn.setAttribute('aria-expanded', 'false');
                if (profileChevron) profileChevron.style.transform = 'rotate(0deg)';
                profileBtn.focus();
            }
        });
    }

    // Interactive File Dropzones
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        const parent = input.closest('.file-dropzone') || input.parentElement;
        if (!parent) return;

        input.addEventListener('change', (e) => {
            const files = e.target.files;
            if (files && files.length > 0) {
                const fileName = files[0].name;
                let filenameEl = parent.querySelector('.dz-filename');
                if (!filenameEl) {
                    filenameEl = document.createElement('div');
                    filenameEl.className = 'dz-filename';
                    parent.appendChild(filenameEl);
                }
                filenameEl.innerHTML = `✓ ${fileName}`;
            }
        });

        if (parent.classList.contains('file-dropzone')) {
            ['dragenter', 'dragover'].forEach(eventName => {
                parent.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    parent.classList.add('dragover');
                }, false);
            });
            ['dragleave', 'drop'].forEach(eventName => {
                parent.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    parent.classList.remove('dragover');
                }, false);
            });
        }
    });
});
