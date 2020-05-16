SELECT sujet, COUNT(sujet) AS used
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
ORDER BY used DESC;
