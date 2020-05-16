<?php

include('include/config.php');
include('include/tools.php');

if(!connect()) {
	redirect('login');
}

$HEAD_TITLE = 'Accueil';

/// Récupération de la date de dernière mise à jour du fichier
$filename = 'index.php';

if(file_exists($filename)) {
	$last = filemtime($filename);
	$last_to_str = 'le '.date('d/m/Y', $last).' à '.date('H:i', $last);
}

include('include/sections/header.php');
include('include/sections/navbar.php');

?>

<div class="container">
	<div class="alert alert-light" role="alert">
		<p class="lead">Données d'articles scientifiques</p>
		<p>
			Projet réalisé par :
			<ul>
				<li><span class="align-middle">Sabrina <b>Bonghi</b></span> <span class="badge badge-pill badge-dark ml-2 align-middle">20161420</span></li>
				<li><span class="align-middle">Maxime <b>Meurisse</b></span> <span class="badge badge-pill badge-dark ml-2 align-middle">20161278</span></li>
				<li><span class="align-middle">Valentin <b>Vermeylen</b></span> <span class="badge badge-pill badge-dark ml-2 align-middle">20162864</span></li>
			</ul>
		</p>
		<p>Pour initialiser la base de données, se rendre sur l'URL : <a href="questions/1/" class="alert-link"><?php echo $SERVER_NAME; ?>/questions/1/</a></p>
	</div>
	<div class="alert alert-light" role="alert">
		Ce site web a été réalisé à l'aide de <a href="https://getbootstrap.com" target="_blank" class="alert-link">bootstrap</a>. Les icônes utilisées proviennent du site web <a href="https://www.flaticon.com" target="_blank" class="alert-link">flaticon</a>.
	</div>
	<div class="alert alert-dark" role="alert">
		Dernière mise à jour : <b><?php echo $last_to_str; ?></b>.
	</div>
</div>

<?php

include('include/sections/footer.php');

?>
