<?php

/**
 * Cette fonction prend une url en entrée et génère un code HTML qui redirige instantanément et automatiquement vers cette URL.
 *
 * @param url L'URL vers laquelle rediriger.
 */
function redirect($url) {
	die('<meta http-equiv="refresh" content="0; '.$url.'">');
}

/**
 * Cette fonction vérifie si un utilisateur est connecté ou non.
 *
 * @return Une valeur booléenne indiquant si l'utilisateur est connecté ou non.
 */
function connect() {
	if(isset($_SESSION['session'])) {
		return true;
	} else {
		return false;
	}
}

?>
