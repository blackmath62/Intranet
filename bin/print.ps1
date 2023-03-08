#Set-ExecutionPolicy Unrestricted -Force
#Set-ExecutionPolicy Unrestricted -Scope LocalMachine

#$MYFILE = "C:\wamp64\www\Intranet\bin\ean.pdf"

#$MYPRINTER = "\Users\S-1-5-21-2623906363-797442895-931840007-1612\Printers\^\^\SRVAD^\IMP_LOGISTIQUE - BAC 1"
#$MYPRINTER = "\\SRVAD\IMP_LOGISTIQUE - BAC 1"
#$MYPRINTER = "\\192.168.50.241\192.168.50.202"
#$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Name -eq $MYPRINTER}[0])
#$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

#Start-Process -FilePath "C:\wamp64\www\Intranet\bin\ean.pdf" -Verb print
#Start-Process -FilePath $MYFILE -Verb print -PassThru



            #Get-Content -Path C:\wamp64\www\Intranet\bin\ean.pdf | Out-Printer



#Invoke-Expression -command "rundll32 printui.dll,PrintUIEntry /Xs /n '\\SRVAD\IMP_EAN_PRODUIT' attributes -EnableBidi"
#Get-Content -FilePath C:\wamp64\www\Intranet\bin\ean.pdf | Out-Printer "\\SRVAD\IMP_EAN_PRODUIT"
#Start-Process -FilePath "C:\wamp64\www\Intranet\bin\ean.pdf" -Verb print

#Get-ChildItem -Path C:\wamp64\www\Intranet\bin\* -Include ean.pdf | ForEach-Object {Start-Process $_.FullName -Verb Print}
#Get-ChildItem -Path C:\wamp64\www\Intranet\bin\*.pdf | ForEach-Object {Start-Process $_.FullName -Verb Print}

#Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True} | Format-Table -AutoSize

#Start-Process -FilePath $MYFILE -Verb print -PassThru

#$MYPRINTER = "\\SRVAD\IMP_EAN_PRODUIT"
#$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Name -eq $MYPRINTER}[0])
#$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

#Start-Process -FilePath "C:\wamp64\www\Intranet\bin\ean.pdf" -Verb print

#$PrintDocument = New-Object System.Drawing.Printing.PrintDocument
#$PrintDocument.DocumentName = "C:\wamp64\www\Intranet\bin\ean.pdf"
#$printDocument.PrinterSettings.PrintToFile = $true
#$printDocument.PrinterSettings.PrintFileName = 'c:\temp\test.txt'
#$PrintDocument.Print()

$MYFILE = "C:\wamp64\www\Intranet\bin\ean.pdf"
$MYPRINTER = "\\SRVAD\IMP_EAN_PRODUIT" 

$DEFAULTPRINTER = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.Default -eq $True}[0])
$PRINTERTMP = (Get-CimInstance -ClassName CIM_Printer | WHERE {$_.NAme -eq $MYPRINTER}[0])
#$PRINTERTMP | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null
#Start-Process -FilePath $MYFILE -Verb print -PassThru
#$DEFAULTPRINTER | Invoke-CimMethod -MethodName SetDefaultPrinter | Out-Null

#Get-Content -FilePath C:\wamp64\www\Intranet\bin\ean.pdf | Out-Printer "\\SRVAD\IMP_EAN_PRODUIT"

Start-Sleep -Seconds 1
Remove-item C:\wamp64\www\Intranet\bin\ean.pdf

