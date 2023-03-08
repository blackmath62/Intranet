#Get-Content -Path "C:\wamp64\www\Intranet\bin\ean.pdf" | Out-Printer "\\SRVAD\IMP_EAN_PRODUIT"
#Get-WmiObject win32_printer | WHERE {$_.Default -eq $True}
#Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True}
#Start-Process -FilePath "C:\wamp64\www\Intranet\bin\ean.pdf" -Verb print

Get-WmiObject win32_printer | WHERE {$_.Default -eq $True}
Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True}