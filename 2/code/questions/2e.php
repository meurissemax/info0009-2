<?php

include('../include/config.php');
include('../include/tools.php');
include('../include/db.php');

if(!connect()) {
	redirect('../login?r=questions/2e');
}

if($nb_tables == 0) {
	redirect('1/?error=no_table');
}

$HEAD_TITLE = 'Sujets de recherche populaires';

$sql = "SELECT sujet, COUNT(sujet) AS used
		FROM
			(
				SELECT sujet
				FROM
					(
						SELECT *
						FROM sujets_articles
					) AS T3
					NATURAL JOIN
					(
						SELECT url
						FROM
							(
								SELECT nom_conference, annee_conference, COUNT(matricule) AS popularity
								FROM participations_conferences
								WHERE annee_conference >= 2012
								GROUP BY nom_conference, annee_conference
								ORDER BY popularity DESC
								LIMIT 5
							) AS T1
							NATURAL JOIN
							(
								SELECT url, nom_conference, annee_conference
								FROM articles_conferences
							) AS T2
					) AS T4
			) AS T5
		GROUP BY sujet
		ORDER BY used DESC";
$request = $connect->query($sql);
$subjects = $request->fetchAll(PDO::FETCH_NUM);

include('../include/sections/header.php');
include('../include/sections/navbar.php');

?>

<div class="container">
	<p class="lead">Sujets de recherche populaires</p>
	<div class="alert alert-primary">
		<span class="badge badge-pill badge-light mr-2 align-middle">Question 2.e</span> <span class="align-middle">Cette page permet de retrouver les sujets de recherche les plus étudiés (triés par ordre décroissant) au cours des 5 conférences les plus populaires depuis 2012.</span>
	</div>
	<table class="table table-sm table-borderless table-hover mb-0">
		<thead>
			<tr>
				<th scope="col">Sujet</th>
				<th scope="col">Nombre d'articles sur le sujet</th>
			</tr>
		</thead>
		<tbody>
			<?php

			foreach($subjects as $subject) {
				echo '<tr><td>'.$subject[0].'</td><td>'.$subject[1].'</td></tr>';
			}

			?>
		</tbody>
	</table>
</div>

<?php

include('../include/sections/footer.php');

?>
