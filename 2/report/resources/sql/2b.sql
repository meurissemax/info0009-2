SELECT url, titre, type, date_publication
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
WHERE matricule_premier_auteur = x
ORDER BY date_publication DESC;
