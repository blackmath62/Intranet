cd %~dp0

REM Default timer to give some time for Adobe Reader to print the file
timeout 4

REM Kill Adobe Reader after printing
taskkill /F /IM AcroRd32.exe