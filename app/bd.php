<?php

//Conexão com o banco de dados

$dbServer = "69.6.213.237";
$dbUser = "fern7804_admin";
$dbPassword = "M@st3rk3y";
$dbName = "fern7804_fin";

mysqli_report(MYSQLI_REPORT_OFF);
$bdConexao = mysqli_connect($dbServer, $dbUser, $dbPassword, $dbName);
