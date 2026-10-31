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
            // .submit() bypasses the submit event, so the overlay is
            // started manually here (see the loading overlay section).
            pendingForm.submit();
            showLoading();
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

/*
|--------------------------------------------------------------------------
| Loading overlay (slow database round-trips)
|--------------------------------------------------------------------------
| Every page view waits on the remote database, so navigations can take a
| moment. Show the maimai loader once a same-origin navigation starts —
| after a short delay, so fast pages never flash it — and hide it again
| on pageshow (fires on load and on back/forward cache restores).
*/
const loadingOverlay = document.getElementById('loading-overlay');
let loadingTimer = null;

function showLoading() {
    if (!loadingOverlay) {
        return;
    }

    clearTimeout(loadingTimer);
    // 250ms grace period: instant navigations never show the overlay.
    loadingTimer = setTimeout(() => {
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.classList.add('flex');
    }, 250);
}

function hideLoading() {
    if (!loadingOverlay) {
        return;
    }

    clearTimeout(loadingTimer);
    loadingOverlay.classList.add('hidden');
    loadingOverlay.classList.remove('flex');
}

document.addEventListener('click', (event) => {
    // Only plain left-clicks; modified clicks open new tabs.
    if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    const link = event.target.closest('a[href]');
    if (!link || link.target === '_blank' || link.hasAttribute('download')) {
        return;
    }

    const url = new URL(link.href, window.location.href);

    if (url.origin !== window.location.origin) {
        return; // external link
    }

    // Same-page anchors do not navigate.
    if (url.hash !== '' && url.pathname === window.location.pathname && url.search === window.location.search) {
        return;
    }

    showLoading();
});

document.addEventListener('submit', (event) => {
    // Forms stopped by other listeners (e.g. the delete confirmation
    // modal) never navigate, so they must not open the overlay either.
    if (event.defaultPrevented) {
        return;
    }

    showLoading();
});

window.addEventListener('pageshow', hideLoading);
