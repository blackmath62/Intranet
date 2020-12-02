<?php

            $serverName = "LOCALHOST";
            $connectionInfo = array("Database" => "LHERMITTE", "UID" => "INTRANET", "PWD" => "123456");
            $conn = sqlsrv_connect($serverName, $connectionInfo);
            if ($conn === false) {
              die(print_r(sqlsrv_errors(), true));
            } else {
              echo "Connecté "; }
            $sql = "SELECT TOP 5        MOUV.FANO, ENT.TIERS, CLI.STAT_0002, ART.FAM_0003, ENT.PIDT,SUM(MOUV.MONT) AS TOTAL,ENT.REM1,ENT.REMPIETOT, ENT.HTPDTMT, MOUV.SENS
            FROM            ENT INNER JOIN
                                     MOUV ON ENT.TICOD = MOUV.TICOD AND ENT.PICOD = MOUV.PICOD AND ENT.PINO = MOUV.FANO AND ENT.DOS = MOUV.DOS INNER JOIN
                                     CLI ON ENT.TIERS = CLI.TIERS AND ENT.DOS = CLI.DOS INNER JOIN
                                     ART ON MOUV.REF = ART.REF AND ENT.DOS = ART.DOS
            WHERE        (ENT.DOS = '1') AND (ENT.PIDT > CONVERT(DATETIME, '2019-01-01', 102)) AND MOUV.PICOD = 4
            GROUP BY MOUV.FANO, ENT.TIERS, CLI.STAT_0002, ART.FAM_0003, ENT.PIDT, ENT.HTPDTMT, MOUV.SENS,ENT.REM1, ENT.REMPIETOT";


            $stmt = sqlsrv_query($conn, $sql);
            if ($stmt === false) {
              die(print_r(sqlsrv_errors(), true));
            }
            while ($row = sqlsrv_fetch_array($stmt)) { // cette requête fonctionne, ne pas modifier
              print_r($row['HTPDTMT']) . print_r($row['SENS']) . print_r($row['REM1']) . print_r($row['STAT_0002']);

            }