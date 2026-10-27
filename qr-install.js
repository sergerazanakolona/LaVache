document.addEventListener("DOMContentLoaded", () => {
    const appUrl = "http://127.0.0.1/index.html"; // Mets ici l'URL publique finale

    new QRCode(document.getElementById("qrcode"), {
        text: appUrl,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
});
