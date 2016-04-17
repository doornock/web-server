<?php

$root = __DIR__ . '/../../vendor/dg/adminer-custom';

if (!is_file($root . '/index.php')) {
	echo "Install Adminer using `composer install`\n";
	exit(1);
}


require $root . '/index.php';
