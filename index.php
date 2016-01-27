<?php

require_once 'vendor/autoload.php';

header('Access-Control-Allow-Origin: *');

header('Content-Type: application/json');

$ongkir = new Bachor\Ongkir('jakarta', 'padang', 5);

echo $ongkir->tiki();

echo $ongkir->jne();
