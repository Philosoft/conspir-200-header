<?php

$code = $_GET['code'] ?? 200;
$bypass = (string)($_GET['bypass'] ?? 'n');

if ($bypass === 'y') {
    header("Status: {$code} ololo", true, $code);
} else {
    header("HTTP/1.1 {$code} ololo", true, $code);
}

echo "content\n";
