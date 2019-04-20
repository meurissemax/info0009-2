<?php

include('include/config.php');
include('include/tools.php');

$errors = array('401', '403', '404', '500', 'bdd');

if($_GET) {
	if(isset($_GET['e'])) {
		$e = htmlspecialchars($_GET['e']);

		if(!in_array($e, $errors)) {
			redirect($SERVER_NAME.'/');
		}
	} else {
		redirect($SERVER_NAME.'/');
	}
} else {
	redirect($SERVER_NAME.'/');
}

$HEAD_TITLE = 'Erreur '.$e;

include('include/sections/header.php');

?>

<div class="container mt-5">
	<div class="alert alert-danger" role="alert">
		<?php

		switch($e) {
			case '401':
				echo '<b>Erreur 401</b> : accès non autorisé.';
				break;

			case '403':
				echo '<b>Erreur 403</b> : accès interdit.';
				break;

			case '404':
				echo '<b>Erreur 404</b> : cette page n\'existe pas.';
				break;

			case '500':
				echo '<b>Erreur 500</b> : erreur interne.';
				break;

			case 'bdd':
				echo '<b>Base de données</b> : impossible de se connecter à la base de données.';
				break;

			default:
				echo 'Erreur inconnue.';
		}

		?>
	</div>

	<a href="<?php echo $SERVER_NAME; ?>/" class="btn btn-outline-danger" role="button">Revenir au site</a>
</div>

<?php

include('include/sections/footer.php');

?>
