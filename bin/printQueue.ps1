$printerName = "\\SRVAD\IMP_EAN_PRODUIT"
$outputPath = "C:\wamp64\www\Intranet\bin\file_attente.txt"

# Exécute la commande Get-PrintJob et enregistre la réponse dans une variable
$printJobs = Get-PrintJob -PrinterName $printerName

# Convertit la sortie en format texte
$textOutput = $printJobs | Out-String

# Enregistre le contenu dans un fichier texte
$textOutput | Out-File -FilePath $outputPath
