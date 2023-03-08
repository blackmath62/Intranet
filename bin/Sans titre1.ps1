$MYFILE = "C:\wamp64\www\Intranet\bin\ean.pdf"
$MYPRINTER = "\\SRVAD\IMP_EAN_PRODUIT" 

$DEFAULTPRINTER = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True}[0])
$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.NAme -eq $MYPRINTER}[0])
$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null
Start-Process -FilePath $MYFILE -Verb print -PassThru -Wait
$DEFAULTPRINTER | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null