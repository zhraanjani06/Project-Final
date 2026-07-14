<?php
$json = file_get_contents('https://raw.githubusercontent.com/mayurrawte/searoute-ts/main/dist/ports.json');
if ($json === false) {
    echo "Failed mayurrawte\n";
    exit;
}
$data = json_decode($json, true);
$sample = array_slice(array_values((array)$data), 0, 3);
print_r($sample);
