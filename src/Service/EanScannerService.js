// Vérifie si le protocole est HTTP, puis redirige vers HTTPS
if (window.location.protocol !== "https:") { // Affiche une boîte de dialogue pour avertir l'utilisateur
    var confirmation = confirm("Vous allez être redirigé sur l'adresse HTTPS de cette même page de l'intranet afin de pouvoir scanner des codes barres. Appuyez sur OK quand vous aurez lu ce message.");
    // Si l'utilisateur appuie sur OK, redirige vers HTTPS
    if (confirmation) {
        window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);
    }
}
const txt = document.getElementById('text');
const eanInput = document.getElementById('ean'); // Assurez-vous que votre champ de saisie a l'ID 'ean'
var lastResult;

function onScanSuccess(decodedText, decodedResult) {
    if (decodedText !== lastResult) {
        lastResult = decodedText;
        console.log(`Scan result ${decodedText}`, decodedResult);
        txt.textContent = decodedText;
        // Mettre à jour la valeur de l'input avec le résultat du scan
        eanInput.value = decodedText;
        processEAN();

        closeScannerModal()
    }
}

var html5QrcodeScanner = new Html5QrcodeScanner("reader", {
    fps: 10,
    qrbox: 250
});

html5QrcodeScanner.render(onScanSuccess);

function closeScannerModal() { // Fermer la modal et arrêter la caméra
    $('#scannerModal').modal('hide');
    // Déclencher le clic sur le bouton pour arrêter la caméra
    $('#html5-qrcode-button-camera-stop').click();
}

// Ajouter un gestionnaire d'événements pour le bouton avec la classe "close"
$('.close').on('click', function () {
    closeScannerModal();
});