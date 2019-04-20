<?php

include('../include/config.php');
include('../include/tools.php');
include('../include/db.php');

if(!connect()) {
	redirect('../login?r=questions/2b');
}

$HEAD_TITLE = 'Publications d\'un chercheur';

/// On récupère les matricule, nom et prénom de tous les chercheurs
$sql = "SELECT matricule, nom, prenom FROM auteurs ORDER BY matricule ASC";
$request = $connect->query($sql);
$researchers = $request->fetchAll(PDO::FETCH_NUM);

if($_POST) {
	if(isset($_POST['matricule'])) {
		if(is_numeric($_POST['matricule'])) {
			/// On récupère les entrées de l'utilisateur
			$matricule = htmlspecialchars($_POST['matricule']);
			$exist = false;

			/// On vérifie que la matricule du chercheur existe
			foreach($researchers as $researcher) {
				if($researcher[0] == $matricule) {
					$exist = true;
				}
			}

			if($exist) {
				/// On crée la requête SQL
				$sql = "SELECT url, titre, type, date_publication
						FROM
							articles
							NATURAL JOIN
							(
								SELECT url, 'journal' AS type
								FROM articles_journaux
								UNION
								SELECT url, 'conference' AS type
								FROM articles_conferences
							) AS articles_type
						WHERE matricule_premier_auteur = $matricule
						ORDER BY date_publication DESC";
				$request = $connect->query($sql);
				$articles = $request->fetchAll(PDO::FETCH_NUM);

				$i = 0;
				$second_authors = array();

				foreach($articles as $article) {
					$url = $article[0];

					$sql = "SELECT matricule, nom, prenom
							FROM
							auteurs
							NATURAL JOIN
								(
									SELECT matricule
									FROM seconds_auteurs
									WHERE url = '$url'
								) AS matricule_seconds_auteurs";
					$request = $connect->query($sql);
					$response = $request->fetchAll(PDO::FETCH_NUM);

					$second_authors[$i++] = $response;
				}
			} else {
				$error = 'Le numéro de matricule n\'est associé à aucun chercheur.';
			}
		} else {
			$error = 'Le numéro de matricule doit être numérique.';
		}
	} else {
		$error = 'Merci de renseigner un numéro de matricule.';
	}
}

include('../include/sections/header.php');
include('../include/sections/navbar.php');

?>

<div class="container">
	<p class="lead">Publications d'un chercheur</p>
	<div class="alert alert-primary">
		<span class="badge badge-pill badge-light mr-2 align-middle">Question 2.b</span> <span class="align-middle">Cette page permet de retrouver l'ensemble des publications d'un chercheur, triées par ordre décroissant de date de publication.</span>
	</div>
	<?php

	if(isset($error)) {
		echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
	}

	?>
	<form method="post">
		<div class="form-group">
			<div class="input-group">
				<select class="custom-select" id="listMatricules" name="matricule" aria-label="Matricule du chercheur" aria-describedby="button-addon" required>
					<?php

					foreach($researchers as $researcher) {
						echo '<option value="'.$researcher[0].'">'.$researcher[0].' - '.$researcher[2].' '.strtoupper($researcher[1]).'</option>';
					}

					?>
				</select>
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary" id="button-addon">Valider</button>
				</div>
			</div>
		</div>
	</form>
	<?php

	if(isset($articles)) {
		echo '<hr>';

		foreach($researchers as $researcher) {
			if($researcher[0] == $matricule) {
				echo '<p>Publication(s) du chercheur <b>'.$researcher[2].' '.strtoupper($researcher[1]).'</b>, en tant que premier auteur.</p>';
			}
		}

		if(!empty($articles)) {
			echo '<table class="table table-sm table-borderless table-hover mb-0"><thead><tr><th scope="col">Titre</th><th scope="col">Type</th><th scope="col">Date de publication</th><th scope="col">Second(s) auteur(s)</th></tr></thead><tbody>';

			$i = 0;

			foreach($articles as $article) {
				$tmp = "";

				foreach($second_authors[$i++] as $e) {
					$tmp .= $e[2].' '.strtoupper($e[1]).' ('.$e[0].')<br>';
				}

				echo '<tr><td>'.$article[1].'</td><td>'.$article[2].'</td><td>'.date('d/m/Y', strtotime($article[3])).'</td><td>'.$tmp.'</td></tr>';
			}

			echo '</tbody></table>';
		} else {
			echo '<p>Aucun résultat.</p>';
		}
	}
	
	?>
</div>

<?php

include('../include/sections/footer.php');

?>
