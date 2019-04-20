<?php

$dbhost = 'ms800.montefiore.ulg.ac.be';
$dbname = 'group1';
$dbusername = 'group1';
$dbpassword = 'co/A/vp7qq';

try {
	$connect = new PDO('mysql:host='.$dbhost.';dbname='.$dbname.'', $dbusername, $dbpassword);
	$connect->exec('set names utf8');
} catch(PDOException $e) {
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/~s161278/error?e=bdd');

	exit();
}

?>
