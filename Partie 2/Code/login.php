<?php

include('include/config.php');
include('include/tools.php');
include('include/db.php');

if(connect()) {
	redirect($SERVER_NAME.'/');
}

$HEAD_TITLE = 'Connexion au site';

if($_POST) {
	if(isset($_POST['username'])) {
		if(isset($_POST['password'])) {
			/// On récupère les données entrées par l'utilisateur
			$username = htmlspecialchars($_POST['username']);
			$password = htmlspecialchars($_POST['password']);
			$session = sha1(uniqid());

			/// On vérifie que le login et le mot de passe sont corrects
			if($username == $dbusername && $password == $dbpassword) {
				$_SESSION['session'] = $session;

				/// On vérifie si on doit rediriger vers une page particulière
				if($_GET) {
					if(isset($_GET['r'])) {
						$r = htmlspecialchars($_GET['r']);

						redirect($SERVER_NAME.'/'.$r);
					} else {
						redirect($SERVER_NAME.'/');
					}
				} else {
					redirect($SERVER_NAME.'/');
				}
			} else {
				$error = 'Les identifiants de connexion sont incorrects.';
			}
		} else {
			$error = 'Merci de renseigner un mot de passe.';
		}
	} else {
		$error = 'Merci de renseigner un nom d\'utilisateur.';
	}
}

include('include/sections/header.php');

?>

<div class="container mt-5">
	<div class="alert alert-light mb-5" role="alert">
		<div class="media">
			<img src="resources/svg/uliege.svg" width="64" height="64" class="align-self-center mr-4" alt="Logo ULiège">
			<img src="resources/svg/logo.svg" width="64" height="64" class="align-self-center mr-4" alt="Logo">
			<div class="media-body">
				<h4 class="mt-0">Données d'articles scientifiques</h4>
				<p>Ce projet, réalisé dans le cadre du cours de <b>Bases de données (organisation générale)</b> durant l'année académique 2018-2019, permet d'explorer et de compléter une base de données d'articles scientifiques.</p>
				<p class="mb-0">Ce projet a été réalisé par <b>Sabrina Bonghi</b> (20161420), <b>Maxime Meurisse</b> (20161278) et <b>Valentin Vermeylen</b> (20162864).</p>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 offset-md-3">
			<div class="card">
				<div class="card-header">
					Connexion au site
				</div>
				<div class="card-body">
					<?php

					if($_GET) {
						if(isset($_GET['from'])) {
							$from = htmlspecialchars($_GET['from']);

							if($from == 'logout') {
								echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Déconnecté du site avec succès !<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
							}
						}

						if(isset($_GET['r'])) {
							echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Connexion au site requise pour accéder à cette page.<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
						}
					}

					if(isset($error)) {
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
					}

					?>
					<form method="post">
						<div class="form-group">
							<label for="inputUser">Nom d'utilisateur</label>
							<input type="text" class="form-control" id="inputUser" name="username" placeholder="<?php echo $dbusername; ?>" required>
						</div>
						<div class="form-group">
							<label for="inputPassword">Mot de passe</label>
							<input type="password" class="form-control" id="inputPassword" name="password" placeholder="<?php echo $dbpassword; ?>" required>
						</div>
						<button type="submit" class="btn btn-primary">Connexion</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

include('include/sections/footer.php');

?>
