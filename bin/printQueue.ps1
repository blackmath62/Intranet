# Définition de l'imprimante par défaut et du chemin du fichier de sortie
$printerName = "\\IMP_EAN_PRODUIT"
$outputPath = "C:\wamp64\www\Intranet\bin\file_attente.txt"

# Exécute la commande Get-PrintJob pour récupérer les travaux d'impression de l'imprimante spécifiée
$printJobs = Get-PrintJob -PrinterName $printerName

# Convertit la sortie en format texte
$textOutput = $printJobs | Out-String

# Enregistre le contenu dans un fichier texte
$textOutput | Out-File -FilePath $outputPath -Encoding UTF8

