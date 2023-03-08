$MYPRINTER = "\\SRVAD\IMP_EAN_PRODUIT"
$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Name -eq $MYPRINTER}[0])
$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

$PrintDocument = New-Object System.Drawing.Printing.PrintDocument
$PrintDocument.DocumentName = "C:\wamp64\www\Intranet\bin\ean.pdf"
$printDocument.PrinterSettings.PrintToFile = $true
#$printDocument.PrinterSettings.PrintFileName = 'c:\temp\test.txt'
$PrintDocument.Print()