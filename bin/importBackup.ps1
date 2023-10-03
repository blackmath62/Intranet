# Configuration MySQL
$MySQLUser = "root"
$MySQLPassword = ""

# Nom de la base de données cible
$TargetDatabase = "test"

# Chemin complet vers mysqldump.exe
$MysqldumpPath = "C:\wamp64\bin\mysql\mysql5.7.31\bin\mysql.exe"

# Chemin complet vers le fichier de sauvegarde
$BackupFilePath = "C:\wamp64\bin\mysql\backup\backup_20231003_144327.sql"

# Lecture du contenu du fichier de sauvegarde en excluant les commentaires et les métadonnées
$BackupContent = Get-Content $BackupFilePath | Where-Object { $_ -notmatch '^--' } | Out-String

# Commande pour importer les données dans la base de données cible
$ImportCommand = "$BackupContent | $MysqldumpPath -u$MySQLUser -p$MySQLPassword $TargetDatabase"

# Exécutez la commande d'importation
Invoke-Expression $ImportCommand

# Fin du script

