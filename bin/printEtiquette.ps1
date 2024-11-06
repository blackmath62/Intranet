$Path = "C:\wamp64\www\Intranet\bin"
$files = Get-ChildItem "$Path\*.pdf"
$AdobeReaderPath = "C:\Program Files\Adobe\Acrobat DC\Acrobat\Acrobat.exe"  # Mettez à jour le chemin si nécessaire

$MYPRINTER = "IMP_EAN_PRODUIT"
$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | Where-Object { $_.Name -eq $MYPRINTER })
$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

ForEach ($file in $files) { 
    try {
        $process = Start-Process -FilePath $AdobeReaderPath -ArgumentList "/t $($file.FullName)" -PassThru
        Write-Host "Fichier imprimé : $($file.FullName)"

        # Attendre un certain temps pour permettre à l'impression de se terminer
        Start-Sleep -Seconds 2

        # Vérifier si le processus est encore en cours d'exécution
        if (!$process.HasExited) {
            Write-Host "Le processus Adobe Reader n'est pas encore terminé, tentative de fermeture forcée..."
            $process | ForEach-Object { $_.Kill() }
        }
    }
    catch {
        Write-Host "Une erreur s'est produite lors de l'impression : $_"
    }
    Remove-Item $file -Force
}
