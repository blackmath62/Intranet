<?php
// Assurez-vous d'utiliser la bonne syntaxe pour la déclaration de l'espace de noms
namespace App\Service;

class EanScannerService
{
    public function getScannerScript(): string
    {
        // Vous générez du code JavaScript ici, donc retournez simplement le script comme une chaîne
        $javascriptCode = '
            // Vérifie si le protocole est HTTP, puis redirige vers HTTPS
            if (window.location.protocol !== "https:") {
            var confirmation = confirm("Vous allez être redirigé sur l\'adresse HTTPS de cette même page de l\'intranet afin de pouvoir scanner des codes barres. Appuyez sur OK quand vous aurez lu ce message.");
            if (confirmation) {
                window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
            }
        }

        const txt = document.getElementById("text");
        var lastResult;

        function onScanSuccess(decodedText, decodedResult, targetId) {
                console.log(`Scan result ${decodedText}`, decodedResult, targetId);
                txt.textContent = decodedText;
                // Mettre à jour la valeur de l\'input avec le résultat du scan
                $("#" + targetId).val(decodedText);
                // Construire le nom de la fonction à lancer selon le champs processEanproduit ou processEanemplacement
                const functionName = "process" + targetId.charAt(0).toUpperCase() + targetId.slice(1).toLowerCase();
                // Vérifier si la fonction existe avant de l\'appeler
                if (typeof window[functionName] === "function") {
                    window[functionName]();
                } else {
                    console.error("La fonction ${functionName} n\'existe pas.");
                }
                closeScannerModal();
        }

        // Fonction pour démarrer la caméra
        function startScanner(targetId) {
            var html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: 250
            });

            html5QrcodeScanner.render(function (decodedText, decodedResult) {
                onScanSuccess(decodedText, decodedResult, targetId);
            });
        }

        // Gestionnaire d\'événements pour détecter l\'ouverture de la modal
        $("#scannerModal").on("shown.bs.modal", function (event) {
            var targetId = $(event.relatedTarget).data("target-id");
            startScanner(targetId);
        });

        function closeScannerModal() {
            $("#scannerModal").modal("hide").on("hidden.bs.modal", function () {
                // Appelé après la fermeture de la modal
                $("#html5-qrcode-button-camera-stop").click();
            });
        }

        $(document).on("click", ".close", function () {
            closeScannerModal();
        });
        ';
        return $javascriptCode;
    }
}
