import './bootstrap';

document.querySelectorAll('[data-file-input]').forEach(input => {
    input.addEventListener('change', () => {
        input.closest('label').querySelector('[data-file-name]').textContent = input.files[0]?.name || 'Belum ada file dipilih';
    });
});
document.querySelectorAll('[data-copy-text]').forEach(button => {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(button.dataset.copyText);
            button.setAttribute('aria-label', 'Nomor rekening tersalin');
            button.innerHTML = '<iconify-icon icon="lucide:check"></iconify-icon>';
        } catch {
            button.setAttribute('aria-label', 'Salin nomor rekening secara manual');
        }
    });
});
const bookingForm = document.querySelector('[data-booking-form]');
bookingForm?.querySelector('[data-booking-duration]')?.addEventListener('change', event => {
    const months = Number(event.target.value);
    document.querySelector('[data-summary-duration]').textContent = `${months} bulan`;
    document.querySelector('[data-summary-total]').textContent = new Intl.NumberFormat('id-ID', {style:'currency',currency:'IDR',maximumFractionDigits:0}).format(Number(bookingForm.dataset.monthlyPrice) * months);
});

document.querySelector('[data-password-toggle]')?.addEventListener('click', event => {
    const button = event.currentTarget;
    const input = document.getElementById(button.getAttribute('aria-controls'));
    const visible = input.type === 'password';
    input.type = visible ? 'text' : 'password';
    button.setAttribute('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
    button.setAttribute('aria-pressed', String(visible));
});

const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
function closeSidebar() {
    document.body.classList.remove('menu-open');
    sidebarToggle?.setAttribute('aria-expanded', 'false');
}
sidebarToggle?.addEventListener('click', () => {
    const open = document.body.classList.toggle('menu-open');
    sidebarToggle.setAttribute('aria-expanded', String(open));
});
document.querySelector('[data-sidebar-close]')?.addEventListener('click', closeSidebar);
document.addEventListener('keydown', event => { if (event.key === 'Escape') closeSidebar(); });
