let deferredPrompt;
const installBtn = document.getElementById("installBtn");
const status = document.getElementById("status");

// Détection de l'événement PWA
window.addEventListener("beforeinstallprompt", (e) => {
    e.preventDefault();
    deferredPrompt = e;

    installBtn.disabled = false;
    status.textContent = "L'application peut être installée.";
});

// Clic sur le bouton
installBtn.addEventListener("click", async () => {
    if (!deferredPrompt) {
        status.textContent = "Installation non disponible.";
        return;
    }

    deferredPrompt.prompt();
    const result = await deferredPrompt.userChoice;

    if (result.outcome === "accepted") {
        status.textContent = "Application installée !";
    } else {
        status.textContent = "Installation annulée.";
    }

    deferredPrompt = null;
});
