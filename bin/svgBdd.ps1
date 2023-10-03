# Configuration MySQL
$MySQLUser = "root"
$MySQLPassword = ""

# Configuration de la sauvegarde
$BackupDir = "C:\wamp64\bin\mysql\backup"
$Date = Get-Date -Format "yyyyMMdd_HHmmss"
$BackupFile = Join-Path -Path $BackupDir -ChildPath ("backup_" + $Date + ".sql")

# Chemin complet vers mysqldump.exe
$MysqldumpPath = "C:\wamp64\bin\mysql\mysql5.7.31\bin\mysqldump.exe"

# Commande mysqldump sans spécifier la base de données source ni les verrous de table
$Arguments = "--user=$MySQLUser", "--password=$MySQLPassword", "--no-create-db", "--no-create-info", "--routines", "--result-file=$BackupFile", "svg20230907"
Start-Process -FilePath $MysqldumpPath -ArgumentList $Arguments -Wait

# Fin du script




