<?php
try {
	$db = new PDO('mysql:host=127.0.0.1;dbname=bachelor_thesis', 'root', '');
} catch (PDOException $e) {
    die ('Kunne ikke koble til serveren : ' . $e->getMessage());
}
?>