let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Show custom install button
    const installButton = document.createElement('div');
    installButton.className = 'pwa-install-prompt';
    installButton.innerHTML = `
        <div class="prompt-content">
            <i class="fas fa-download"></i>
            <span>Install UAC Inspection App</span>
            <button id="installPWA" class="btn btn-primary btn-sm">Install</button>
            <button id="closePWAPrompt" class="btn btn-link btn-sm">Not now</button>
        </div>
    `;
    
    document.body.appendChild(installButton);
    
    // Handle install button click
    document.getElementById('installPWA').addEventListener('click', async () => {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            const result = await deferredPrompt.userChoice;
            if (result.outcome === 'accepted') {
                console.log('PWA installed');
            }
            deferredPrompt = null;
            installButton.remove();
        }
    });
    
    // Handle close button click
    document.getElementById('closePWAPrompt').addEventListener('click', () => {
        installButton.remove();
    });
});