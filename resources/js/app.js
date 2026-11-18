

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Indikator navigasi halaman yang mulus dan cepat
document.addEventListener('DOMContentLoaded', () => {
    let progressBar = document.getElementById('global-page-progress');
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.id = 'global-page-progress';
        progressBar.className = 'fixed top-0 left-0 right-0 h-[2.5px] bg-gradient-to-r from-emerald-500 via-green-400 to-teal-400 z-[9999] opacity-0 pointer-events-none transition-all duration-300 shadow-sm shadow-emerald-500/30';
        document.body.appendChild(progressBar);
    }

    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link || !link.href || link.target === '_blank' || link.hasAttribute('download') || link.href.startsWith('javascript:') || link.href.includes('#')) return;
        
        try {
            const url = new URL(link.href, window.location.origin);
            if (url.origin === window.location.origin && url.pathname !== window.location.pathname) {
                progressBar.style.opacity = '1';
                progressBar.style.width = '35%';
                setTimeout(() => {
                    if (progressBar.style.opacity === '1') {
                        progressBar.style.width = '75%';
                    }
                }, 150);
            }
        } catch (err) {
            // Ignore invalid URLs
        }
    });

    window.addEventListener('beforeunload', () => {
        if (progressBar) {
            progressBar.style.opacity = '1';
            progressBar.style.width = '98%';
        }
    });
});

