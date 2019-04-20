<?php

include('../include/config.php');
include('../include/tools.php');
include('../include/db.php');

if(!connect()) {
	redirect('../login?r=questions/2d');
}

$HEAD_TITLE = 'Participants auteurs';

$sql = "SELECT matricule, nom, prenom
		FROM
			(
				SELECT T3.matricule, T3.nom, T3.prenom, T3.nom_conference, T3.annee_conference, T4.url
				FROM
					(
						SELECT matricule, nom, prenom, nom_conference, annee_conference
						FROM
							(
								SELECT matricule, nom_conference, annee_conference
								FROM participations_conferences
							) AS T1
							NATURAL JOIN
							(
								SELECT matricule, nom, prenom
								FROM auteurs
							) AS T2
						ORDER BY matricule ASC
					) AS T3
					LEFT JOIN
					(
						SELECT url, matricule, nom_conference, annee_conference
						FROM
							(
								SELECT url, matricule_premier_auteur AS matricule
								FROM articles
							) AS T1
							NATURAL JOIN
							(
								SELECT url, nom_conference, annee_conference
							FROM articles_conferences
							) AS T2
						ORDER BY matricule ASC
					) AS T4
					ON T3.matricule = T4.matricule AND T3.nom_conference = T4.nom_conference AND T3.annee_conference = T4.annee_conference
			) AS T5
		GROUP BY matricule
		HAVING COUNT(nom_conference) = COUNT(url)
		ORDER BY matricule ASC";
$request = $connect->query($sql);
$authors = $request->fetchAll(PDO::FETCH_NUM);

include('../include/sections/header.php');
include('../include/sections/navbar.php');

?>

<div class="container">
	<p class="lead">Participants qui sont premiers auteurs</p>
	<div class="alert alert-primary">
		<span class="badge badge-pill badge-light mr-2 align-middle">Question 2.d</span> <span class="align-middle">Cette page permet de retrouver les chercheurs qui ont publié au moins un article en tant que premier auteur à chaque conférence à laquelle ils ont assisté.</span>
	</div>
	<table class="table table-sm table-borderless table-hover mb-0">
		<thead>
			<tr>
				<th scope="col">Matricule</th>
				<th scope="col">Nom</th>
				<th scope="col">Prénom</th>
			</tr>
		</thead>
		<tbody>
			<?php

			foreach($authors as $author) {
				echo '<tr><td>'.$author[0].'</td><td>'.$author[1].'</td><td>'.$author[2].'</td></tr>';
			}

			?>
		</tbody>
	</table>
</div>

<?php

include('../include/sections/footer.php');

?>
