SELECT matricule, nom, prenom
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
ORDER BY matricule AS;
