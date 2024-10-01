# D�finition de l'imprimante par d�faut et du chemin du fichier de sortie
$printerName = "\\IMP_EAN_PRODUIT"
$outputPath = "C:\wamp64\www\Intranet\bin\file_attente.txt"

# Ex�cute la commande Get-PrintJob pour r�cup�rer les travaux d'impression de l'imprimante sp�cifi�e
$printJobs = Get-PrintJob -PrinterName $printerName

# Convertit la sortie en format texte
$textOutput = $printJobs | Out-String

# Enregistre le contenu dans un fichier texte
$textOutput | Out-File -FilePath $outputPath -Encoding UTF8

