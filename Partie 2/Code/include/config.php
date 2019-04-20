<?php

$MATRICULE = 's161278';
$SERVER_NAME = 'http://'.$_SERVER['HTTP_HOST'].'/~'.$MATRICULE;

session_name('session_group1');
session_start();

header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set('Europe/Brussels');

?>
