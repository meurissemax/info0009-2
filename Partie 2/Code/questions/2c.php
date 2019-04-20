<?php

include('../include/config.php');
include('../include/tools.php');
include('../include/db.php');

if(!connect()) {
	redirect('../login?r=questions/2c');
}

$HEAD_TITLE = 'Ajouter un nouvel article';

/// On récupère toutes les données supposées connues dans la base de données
$sql = "SELECT matricule, nom, prenom FROM auteurs ORDER BY matricule ASC";
$request = $connect->query($sql);
$db_researchers = $request->fetchAll(PDO::FETCH_NUM);

$sql = "SELECT nom FROM revues";
$request = $connect->query($sql);
$db_reviews = $request->fetchAll(PDO::FETCH_NUM);

$sql = "SELECT nom, annee FROM conferences";
$request = $connect->query($sql);
$db_conferences = $request->fetchAll(PDO::FETCH_NUM);

$sql = "SELECT DISTINCT n_journal FROM articles_journaux ORDER BY n_journal ASC";
$request = $connect->query($sql);
$db_njournals = $request->fetchAll(PDO::FETCH_NUM);

if($_POST) {
	$i = 0;
	$error = array();

	/// Récupération des entrées de l'utilisateur
	foreach($_POST as $key => $value) {
		$$key = htmlspecialchars($value);
	}

	/// Vérification que les champs obligatoires ont été renseignés
	$common_fields = array('url', 'doi', 'titre', 'date_publication', 'premier_auteur', 'sujets', 'type');

	foreach($common_fields as $e) {
		if(empty($$e)) {
			$error[$i++] = 'Merci de renseigner tous les champs généraux.';
		}
	}

	if($type != 'journal' && $type != 'conference') {
		$error[$i++] = 'Le type de l\'article est invalide.';
	}

	/// Vérification que les champs obligatoires ont été renseignés - article de journal
	if($type == 'journal') {
		$journal_fields = array('page_debut', 'page_fin', 'revue', 'n_journal');

		foreach($journal_fields as $e) {
			if(empty($$e)) {
				$error[$i++] = 'Merci de renseigner le champ \''.$e.'\'';
			}
		}
	}

	/// Vérification que les champs obligatoires ont été renseignés - article de conference
	if($type == 'conference') {
		$conference_fields = array('presentation', 'nom_conference');

		foreach($conference_fields as $e) {
			if(empty($$e)) {
				$error[$i++] = 'Merci de renseigner le champ \''.$e.'\'';
			}
		}	
	}

	/// Si tous les champs nécessaires ont bien été renseignés
	if(empty($error)) {
		/* -------------------------------------------- */
		/* Vérification de la validité de chaque entrée */
		/* -------------------------------------------- */

		/* Article en général */

		/// URL
		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			$error[$i++] = 'L\'URL n\'est pas valide.';
		}

		/// DOI
		if(!is_numeric($doi)) {
			$error[$i++] = 'Le DOI n\'est pas valide.';
		}

		/// Unicité de l'URL et du DOI
		$sql = "SELECT url, doi FROM articles WHERE url = '$url' OR doi = $doi";
		$request = $connect->query($sql);
		$check = $request->rowCount();

		if($check > 0) {
			$error[$i++] = 'L\'URL ou le DOI existe déjà dans la base de données.';
		}

		/// Matricule du premier auteur
		if(!is_numeric($premier_auteur)) {
			$error[$i++] = 'Le matricule du premier auteur est invalide.';
		}

		$sql = "SELECT matricule FROM auteurs WHERE matricule = $premier_auteur";
		$request = $connect->query($sql);
		$check = $request->rowCount();

		if($check != 1) {
			$error[$i++] = 'Le matricule du premier auteur est invalide.';
		}

		/// Matricule(s) du(des) second(s) auteur(s)
		if(!empty($seconds_auteurs)) {
			$seconds_auteurs = explode(',', $seconds_auteurs);

			foreach($seconds_auteurs as &$e) {
				$e = trim($e);

				if($e == $premier_auteur) {
					$error[$i++] = 'Le premier auteur ne peut pas être également second auteur.';
				}

				$sql = "SELECT matricule FROM auteurs WHERE matricule = $e";
				$request = $connect->query($sql);
				$check = $request->rowCount();

				if($check != 1) {
					$error[$i++] = 'Un matricule de second auteur n\'est pas valide.';
				}
			}
		}

		/// Sujets de recherche
		$sujets = explode(',', $sujets);

		foreach($sujets as &$sujet) {
			$sujet = trim($sujet);
		}

		unset($sujet);

		/* Article de journal */
		if($type == 'journal') {
			/// Page de début et page de fin
			if(!is_numeric($page_debut) || !is_numeric($page_fin)) {
				$error[$i++] = 'Les pages de début et de fin ne sont pas valides.';
			}

			if($page_debut > $page_fin) {
				$error[$i++] = 'La page de début ne peut pas être plus grande que la page de fin.';
			}

			/// Revue
			$sql = "SELECT * FROM revues WHERE nom = '$revue'";
			$request = $connect->query($sql);
			$check = $request->rowCount();

			if($check != 1) {
				$error[$i++] = 'La revue n\'est pas valide.';
			}

			/// Numéro de journal
			if(!is_numeric($n_journal)) {
				$error[$i++] = 'Le numéro de journal n\'est pas valide';
			}

			/// Vérification que la combinaison revue - numéro de journal existe
			$sql = "SELECT DISTINCT nom_revue, n_journal FROM articles_journaux WHERE nom_revue = '$revue' AND n_journal = $n_journal";
			$request = $connect->query($sql);
			$check = $request->rowCount();

			if($check != 1) {
				$error[$i++] = 'Cette revue n\'a pas publié de journal avec ce numéro.';
			}
		}

		/* Article de conférence */
		if($type == 'conference') {
			// Nom de la conférence
			$sql = "SELECT nom, annee FROM conferences WHERE nom = '$nom_conference'";
			$request = $connect->query($sql);
			$response = $request->fetchAll(PDO::FETCH_ASSOC);

			if(count($response) > 1) {
				$error[$i++] = 'Le nom de la conférence n\'est pas valide.';
			}

			// Récupération de l'année de la conférence
			$annee_conference = $response[0]['annee'];
		}

		/* ---------------------------------------- */
		/* Vérification des contraintes d'intégrité */
		/* ---------------------------------------- */
		$check_annee = date('Y', strtotime($date_publication));

		// Les articles d'une conférence doivent avoir été publiés l'année de la conférence
		if($type == 'conference') {
			if($check_annee != $annee_conference) {
				$error[$i++] = 'L\'article n\'a pas été publié l\'année de la conférence.';
			}
		}

		// Les articles publiés dans un même journal doivent avoir été publiés la même année
		if($type == 'journal') {
			$sql = "SELECT date_publication
					FROM
						(
							SELECT url, date_publication
							FROM articles
						) AS T1
						NATURAL JOIN
						(
							SELECT url
							FROM articles_journaux
							WHERE n_journal = 51
							LIMIT 1
						) AS T2";
			$request = $connect->query($sql);
			$response = $request->fetchAll(PDO::FETCH_ASSOC);

			$date_journal = date('Y', strtotime($response[0]['date_publication']));

			if($check_annee != $date_journal) {
				$error[$i++] = 'Les articles publiés dans un même journal doivent avoir été publiés la même année.';
			}
		}

		/// À ce stade, toutes les entrées sont valides et les contraintes d'intégritées respectées.

		/* --------------------------------------------- */
		/* Insertion des données dans la base de données */
		/* --------------------------------------------- */
		if(empty($error)) {
			$sql = "";

			// articles
			$sql .= "INSERT INTO articles (url, doi, titre, date_publication, matricule_premier_auteur) VALUES ('$url', $doi, '$titre', '$date_publication', $premier_auteur);";

			// sujets_articles
			foreach($sujets as $sujet) {
				$sql .= "INSERT INTO sujets_articles (url, sujet) VALUES ('$url', '$sujet');";
			}

			// seconds_auteurs
			if(!empty($seconds_auteurs)) {
				foreach($seconds_auteurs as $second_auteur) {
					$sql .= "INSERT INTO seconds_auteurs (url, matricule) VALUES ('$url', $second_auteur);";
				}
			}

			// articles_journaux
			if($type == 'journal') {
				$sql .= "INSERT INTO articles_journaux (url, pg_debut, pg_fin, nom_revue, n_journal) VALUES ('$url', $page_debut, $page_fin, '$revue', $n_journal);";
			}

			// articles_conferences
			if($type == 'conference') {
				$sql .= "INSERT INTO articles_conferences (url, presentation, nom_conference, annee_conference) VALUES ('$url', '$presentation', '$nom_conference', $annee_conference);";
			}

			$connect->query($sql);

			$success = 'L\'article a été ajouté à la base de données !';
		}
	}
}

include('../include/sections/header.php');
include('../include/sections/navbar.php');

?>

<div class="container">
	<p class="lead">Ajouter un nouvel article</p>
	<div class="alert alert-primary">
		<span class="badge badge-pill badge-light mr-2 align-middle">Question 2.c</span> <span class="align-middle">Cette page permet d'ajouter un nouvel article dans la base de données.</span>
	</div>
	<?php

	if(isset($success)) {
		echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'.$success.'<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
	}

	if(!empty($error)) {
		echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">Erreur(s) dans le formulaire :<ul>';

		foreach($error as $e) {
			echo '<li>'.$e.'</li>';
		}

		echo '</ul><button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
	}

	?>
	<form method="post">
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label for="inputURL">URL</label>
					<input type="url" class="form-control" id="inputURL" name="url" placeholder="http://example.com/example_article.pdf" required>
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label for="inputDOI">DOI</label>
					<input type="number" min="0" class="form-control" id="inputDOI" name="doi" placeholder="112358132134" required>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="inputTitre">Titre</label>
			<input type="text" class="form-control" id="inputTitre" name="titre" placeholder="L'équilibre de la vie, vu par les dahus" required>
		</div>
		<div class="form-group">
			<label for="inputDate">Date de publication</label>
			<input type="date" class="form-control" id="inputDate" name="date_publication" required>
		</div>
		<div class="form-group">
			<label for="inputMatricule">Matricule du premier auteur</label>
			<select class="custom-select" id="inputMatricule" name="premier_auteur" required>
				<?php

				foreach($db_researchers as $db_researcher) {
					echo '<option value="'.$db_researcher[0].'">'.$db_researcher[0].' - '.$db_researcher[2].' '.strtoupper($db_researcher[1]).'</option>';
				}

				?>
			</select>
		</div>
		<div class="form-group">
			<label for="inputSecondsAuteurs">Matricule(s) du(des) second(s) auteur(s)</label>
			<input type="text" class="form-control" id="inputSecondsAuteurs" name="seconds_auteurs" placeholder="34, 12 (facultatif)">
		</div>
		<div class="form-group">
			<label for="inputSujet">Sujet(s) de l'article</label>
			<input type="text" class="form-control" id="inputSujet" name="sujets" placeholder="Mathématique, Intelligence artificielle" required>
		</div>
		<hr>
		<div class="form-group">
			<div class="btn-group btn-group-toggle" data-toggle="buttons">
				<label class="btn btn-secondary active">
					<input type="radio" name="type" id="typeJournal" value="journal" onchange="show('journal');" autocomplete="off" checked> Article de journal
				</label>
				<label class="btn btn-secondary">
					<input type="radio" name="type" id="typeConference" value="conference" onchange="show('conference');" autocomplete="off"> Article de conférence
				</label>
			</div>
		</div>
		<div id="journal" style="display: block;">
			<div class="row">
				<div class="col">
					<div class="form-group">
						<label for="inputPageDebut">Page de début</label>
						<input type="number" min="0" class="form-control" id="inputPageDebut" name="page_debut" placeholder="9">
					</div>
				</div>
				<div class="col">
					<div class="form-group">
						<label for="inputPageFin">Page de fin</label>
						<input type="number" min="0" class="form-control" id="inputPageFin" name="page_fin" placeholder="13">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="inputRevue">Nom de la revue</label>
				<select class="custom-select" id="inputRevue" name="revue">
					<?php

					foreach($db_reviews as $db_review) {
						echo '<option value="'.$db_review[0].'">'.$db_review[0].'</option>';
					}

					?>
				</select>
			</div>
			<div class="form-group">
				<label for="inputNJournal">Numéro du journal</label>
				<select class="custom-select" id="inputNJournal" name="n_journal">
					<?php

					foreach($db_njournals as $db_njournal) {
						echo '<option value="'.$db_njournal[0].'">'.$db_njournal[0].'</option>';
					}

					?>
				</select>
			</div>
		</div>
		<div id="conference" style="display: none;">
			<div class="form-group">
				<label for="inputPresentation">Type de présentation</label>
				<input type="text" class="form-control" id="inputPresentation" name="presentation" placeholder="20min presentation">
			</div>
			<div class="form-group">
				<label for="inputNomConference">Nom de la conférence</label>
				<select class="custom-select" id="inputNomConference" name="nom_conference">
					<?php

					foreach($db_conferences as $db_conference) {
						echo '<option value="'.$db_conference[0].'">'.$db_conference[0].' ('.$db_conference[1].')</option>';
					}

					?>
				</select>
			</div>
		</div>
		<button type="submit" class="btn btn-primary">Ajouter</button>
	</form>
	<script type="text/javascript">
		function show(divName) {
			var journal = document.getElementById('journal');
			var conference = document.getElementById('conference');

			if(divName == 'journal') {
				journal.style.display = 'block';
				conference.style.display = 'none';
			}

			if(divName == 'conference') {
				conference.style.display = 'block';
				journal.style.display = 'none';
			}
		}
	</script>
</div>

<?php

include('../include/sections/footer.php');

?>
