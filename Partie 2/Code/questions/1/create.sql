CREATE TABLE IF NOT EXISTS institutions (
	nom VARCHAR(128) NOT NULL,
	rue VARCHAR(128) NOT NULL,
	numero SMALLINT NOT NULL,
	ville VARCHAR(128) NOT NULL,
	pays VARCHAR(128) NOT NULL,
	PRIMARY KEY (nom)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS auteurs (
	matricule INT NOT NULL,
	nom VARCHAR(64) NOT NULL,
	prenom VARCHAR(64) NOT NULL,
	debut_doctorat SMALLINT NOT NULL,
	nom_institution VARCHAR(128) NOT NULL,
	PRIMARY KEY (matricule),
	FOREIGN KEY (nom_institution) REFERENCES institutions(nom)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS articles (
	url VARCHAR(255) NOT NULL,
	doi BIGINT NOT NULL,
	titre VARCHAR(255) NOT NULL,
	date_publication DATE NOT NULL,
	matricule_premier_auteur INT NOT NULL,
	PRIMARY KEY (url),
	FOREIGN KEY (matricule_premier_auteur) REFERENCES auteurs(matricule)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS sujets_articles (
	url VARCHAR(255) NOT NULL,
	sujet VARCHAR(128) NOT NULL,
	PRIMARY KEY (url, sujet),
	FOREIGN KEY (url) REFERENCES articles(url)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS seconds_auteurs (
	url VARCHAR(255) NOT NULL,
	matricule INT NOT NULL,
	PRIMARY KEY (url, matricule),
	FOREIGN KEY (url) REFERENCES articles(url),
	FOREIGN KEY (matricule) REFERENCES auteurs(matricule)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS revues (
	nom VARCHAR(128) NOT NULL,
	impact SMALLINT NOT NULL,
	PRIMARY KEY (nom)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS articles_journaux (
	url VARCHAR(255) NOT NULL,
	pg_debut SMALLINT NOT NULL,
	pg_fin SMALLINT NOT NULL,
	nom_revue VARCHAR(128) NOT NULL,
	n_journal INT NOT NULL,
	PRIMARY KEY (url),
	FOREIGN KEY (url) REFERENCES articles(url),
	FOREIGN KEY (nom_revue) REFERENCES revues(nom)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS conferences (
	nom VARCHAR(128) NOT NULL,
	annee SMALLINT NOT NULL,
	rue VARCHAR(128) NOT NULL,
	numero SMALLINT NOT NULL,
	ville VARCHAR(128) NOT NULL,
	pays VARCHAR(128) NOT NULL,
	PRIMARY KEY (nom, annee)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS articles_conferences (
	url VARCHAR(255) NOT NULL,
	presentation VARCHAR(128) NOT NULL,
	nom_conference VARCHAR(128) NOT NULL,
	annee_conference SMALLINT NOT NULL,
	PRIMARY KEY (url),
	FOREIGN KEY (url) REFERENCES articles(url),
	FOREIGN KEY (nom_conference, annee_conference) REFERENCES conferences(nom, annee)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS participations_conferences (
	matricule INT NOT NULL,
	nom_conference VARCHAR(128) NOT NULL,
	annee_conference SMALLINT NOT NULL,
	tarif VARCHAR(128) NOT NULL,
	PRIMARY KEY (matricule, nom_conference, annee_conference),
	FOREIGN KEY (matricule) REFERENCES auteurs(matricule),
	FOREIGN KEY (nom_conference, annee_conference) REFERENCES conferences(nom, annee)
) ENGINE = InnoDB;
