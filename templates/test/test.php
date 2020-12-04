<?php

            $serverName = "LOCALHOST";
            $connectionInfo = array("Database" => "LHERMITTE", "UID" => "INTRANET", "PWD" => "123456");
            $conn = sqlsrv_connect($serverName, $connectionInfo);
            if ($conn === false) {
              die(print_r(sqlsrv_errors(), true));
            } else {
              echo "Connecté "; }
            $sql = "SELECT * FROM ART"
            ;


            $stmt = sqlsrv_query($conn, $sql);
            if ($stmt === false) {
              die(print_r(sqlsrv_errors(), true));
            }
            while ($row = sqlsrv_fetch_array($stmt)) { // cette requête fonctionne, ne pas modifier
              print_r($row['REF']) . print_r($row['DES']);

            }