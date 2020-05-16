<?php

/// On crée la requête SQL
$sql = "SELECT * FROM $table";

/// On y ajoute les contraintes s'il y en a
if(!empty($tab_constraints)) {
	$sql .= " WHERE ";

	foreach($tab_constraints as $constraint) {
		/// contrainte d'égalité
		if(is_numeric($constraint[1]) || $constraint[0] == $bdd_infos['articles'][3]) {
			$sql .= "$constraint[0] = '$constraint[1]' AND ";
		}

		/// contrainte de contenance
		else {
			$sql .= "$constraint[0] COLLATE UTF8_GENERAL_CI LIKE '%$constraint[1]%' AND ";
		}
	}

	$sql .= "1";
}

?>
