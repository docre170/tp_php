document.addEventListener("DOMContentLoaded", function () {
    const scanBtn = document.getElementById("start-scan");
    const scannerContainer = document.getElementById("scanner-container");
    const codeInput = document.getElementById("code-input");

    if (!scanBtn || !scannerContainer || !codeInput) {
        return;
    }

    let scanner = null;

    scanBtn.addEventListener("click", async function () {
        scannerContainer.classList.remove("hidden");

        if (typeof Html5Qrcode === "undefined") {
            alert("Bibliotheque scanner indisponible. Entrez le code manuellement.");
            return;
        }

        if (scanner) {
            return;
        }

        scanner = new Html5Qrcode("scanner-video");

        try {
            await scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                function (decodedText) {
                    codeInput.value = decodedText;
                    scanner.stop();
                    scanner = null;
                    scannerContainer.classList.add("hidden");
                },
                function () {}
            );
        } catch (error) {
            scanner = null;
            alert("Impossible de demarrer la camera. Saisissez le code-barres manuellement.");
        }
    });
});

