<?php

$code = $_GET['code'] ?? 200;
header("HTTP/1.1 {$code} ololo", true, $code);
echo "content\n";
