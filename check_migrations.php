<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=bibliotheque', 'root', '');
$pdo->exec('DROP TABLE IF EXISTS doctrine_migration_versions');
echo "Table dropped.\n";
