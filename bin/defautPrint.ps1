#Get-Content -Path C:\wamp64\www\Intranet\bin\ean.pdf | Out-Printer \\SRVAD\IMP_EAN_PRODUIT
#Get-WmiObject win32_printer | WHERE {$_.Default -eq $True}
#Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True}
#Start-Process -FilePath "C:\wamp64\www\Intranet\bin\ean.pdf" -Verb print

$MYFILE = "C:\wamp64\www\Intranet\bin\ean.jpg"
$MYPRINTER = "\\SRVAD\IMP_EAN_PRODUIT"
$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Name -eq $MYPRINTER}[0])
$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

# afficher l'imprimante par defaut
Get-WmiObject win32_printer | WHERE {$_.Default -eq $True}
Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True}

# lister les imprimantes
#Get-Printer
#Get-WmiObject Win32_Printer | select Name

Start-Process -FilePath $MYFILE -Verb print -PassThru

Start-Sleep -Seconds 1
Remove-item C:\wamp64\www\Intranet\bin\ean.jpg