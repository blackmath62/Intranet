cd %~dp0

REM Fermer les instances ouvertes d'Adobe
taskkill /F /IM AcroRd32.exe

REM démarrer le fichier ean.pdf
for /r %%I in (*.pdf) do (
start "" "C:\Program Files (x86)\Adobe\Acrobat Reader DC\Reader\AcroRd32.exe" /H /P "%%I" 

REM Fermer les instances ouvertes d'Adobe
taskkill /F /IM AcroRd32.exe

pause

REM supprimer le fichier ean.pdf
::del ean.pdf