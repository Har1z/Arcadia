// Vanilla JavaScript, no frameworks. Loaded on every page via @vite.

const root = document.documentElement;

/*
|--------------------------------------------------------------------------
| Theme toggle
|--------------------------------------------------------------------------
| The initial theme is applied by an inline <head> script before the CSS
| loads. Here we only handle the user clicking the toggle buttons: flip
| data-theme, remember the choice, and keep the button state in sync.
*/
function currentTheme() {
    return root.dataset.theme === 'dark' ? 'dark' : 'light';
}

function syncThemeToggleButtons() {
    const dark = currentTheme() === 'dark';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(dark));
        button.setAttribute('aria-label', dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    });
}

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const next = currentTheme() === 'dark' ? 'light' : 'dark';
        root.dataset.theme = next;
        localStorage.setItem('theme', next);
        syncThemeToggleButtons();
    });
});

syncThemeToggleButtons();

/*
|--------------------------------------------------------------------------
| Sidebar drawer (mobile)
|--------------------------------------------------------------------------
| Below 1024px the sidebar becomes an off-canvas drawer. Tailwind handles
| the positioning; JS only toggles two classes on open/close.
*/
const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');

function setDrawer(open) {
    if (!sidebar || !backdrop) {
        return;
    }

    sidebar.classList.toggle('-translate-x-full', !open);
    backdrop.classList.toggle('hidden', !open);
}

document.querySelectorAll('[data-drawer-open]').forEach((button) => {
    button.addEventListener('click', () => setDrawer(true));
});

document.querySelectorAll('[data-drawer-close]').forEach((button) => {
    button.addEventListener('click', () => setDrawer(false));
});

if (backdrop) {
    backdrop.addEventListener('click', () => setDrawer(false));
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        setDrawer(false);
    }
});

/*
|--------------------------------------------------------------------------
| Delete confirmation modal
|--------------------------------------------------------------------------
| Every form with data-confirm="message" opens the shared modal before it
| is submitted. The confirm button calls form.submit() directly, which does
| NOT fire a submit event, so the form is not intercepted twice.
*/
const confirmModal = document.getElementById('confirm-delete-modal');
const confirmMessage = document.getElementById('confirm-delete-message');
let pendingForm = null;

function setConfirmModal(open) {
    if (!confirmModal) {
        return;
    }

    confirmModal.classList.toggle('hidden', !open);
    confirmModal.classList.toggle('flex', open);

    if (!open) {
        pendingForm = null;
    }
}

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        pendingForm = form;

        if (confirmMessage) {
            confirmMessage.textContent = form.dataset.confirm;
        }

        setConfirmModal(true);
    });
});

document.querySelectorAll('[data-confirm-cancel]').forEach((button) => {
    button.addEventListener('click', () => setConfirmModal(false));
});

document.querySelectorAll('[data-confirm-yes]').forEach((button) => {
    button.addEventListener('click', () => {
        if (pendingForm) {
            pendingForm.submit();
        }

        setConfirmModal(false);
    });
});

// Clicking the dark backdrop also cancels.
if (confirmModal) {
    confirmModal.addEventListener('click', (event) => {
        if (event.target === confirmModal) {
            setConfirmModal(false);
        }
    });
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && confirmModal && !confirmModal.classList.contains('hidden')) {
        setConfirmModal(false);
    }
});
