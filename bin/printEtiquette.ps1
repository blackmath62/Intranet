
$Path = "C:\wamp64\www\Intranet\bin"
$files = Get-ChildItem C:\wamp64\www\Intranet\bin\*.pdf
$MYPRINTER = "\\SRVAD\IMP_EAN_PRODUIT"
$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Name -eq $MYPRINTER}[0])
$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

ForEach ($file in $files) { 
Start-Process $file -Verb Print -PassThru | %{sleep 3;$_} | kill
Remove-item $file
}
