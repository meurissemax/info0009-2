<?php
/* ---------------------------------------- */
/* Vérification des contraintes d'intégrité */
/* ---------------------------------------- */

/* [...] */

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

/* [...] */

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
	try {
		$connect->beginTransaction();

		$lock = "LOCK TABLES articles WRITE, sujets_articles WRITE";
		$sql = "";

		// articles
		$sql .= "INSERT INTO articles (url, doi, titre, date_publication, matricule_premier_auteur) VALUES ('$url', $doi, '$titre', '$date_publication', $premier_auteur);";

		// sujets_articles
		foreach($sujets as $sujet) {
			$sql .= "INSERT INTO sujets_articles (url, sujet) VALUES ('$url', '$sujet');";
		}

		// seconds_auteurs
		if(!empty($seconds_auteurs)) {
			$lock .= ", seconds_auteurs WRITE";

			foreach($seconds_auteurs as $second_auteur) {
				$sql .= "INSERT INTO seconds_auteurs (url, matricule) VALUES ('$url', $second_auteur);";
			}
		}

		// articles_journaux
		if($type == 'journal') {
			$lock .= ", articles_journaux WRITE";

			$sql .= "INSERT INTO articles_journaux (url, pg_debut, pg_fin, nom_revue, n_journal) VALUES ('$url', $page_debut, $page_fin, '$revue', $n_journal);";
		}

		// articles_conferences
		if($type == 'conference') {
			$lock .= ", articles_conferences WRITE";

			$sql .= "INSERT INTO articles_conferences (url, presentation, nom_conference, annee_conference) VALUES ('$url', '$presentation', '$nom_conference', $annee_conference);";
		}

		$lock .= ";";

		$connect->query($lock);
		$connect->query($sql);
		$connect->commit();

		$success = 'L\'article a été ajouté à la base de données !';
	} catch(Exception $e) {
		$connect->rollback();

		$error = 'Une erreur est survenue lors de l\'insertion des données dans la base de données.';
	}

	$connect->query('UNLOCK TABLES');
?>
