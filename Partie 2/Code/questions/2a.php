<?php

include('../include/config.php');
include('../include/tools.php');
include('../include/db.php');

if(!connect()) {
	redirect('../login?r=questions/2a');
}

$HEAD_TITLE = 'Recherche';

/// On récupère tous les noms de tables et colonnes
$bdd_infos = array();

$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbname'";
$request = $connect->query($sql);
$table_names = $request->fetchAll(PDO::FETCH_NUM);

foreach($table_names as $table_name) {
	$tmp = array();
	$name = $table_name[0];

	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$name'";
	$request = $connect->query($sql);
	$column_names = $request->fetchAll(PDO::FETCH_NUM);

	foreach($column_names as $column_name) {
		array_push($tmp, $column_name[0]);
	}

	$bdd_infos[$name] = $tmp;
}

if($_POST) {
	if(isset($_POST['table'])) {
		/// On récupère les entrées de l'utilisateur
		$table = htmlspecialchars($_POST['table']);

		if(!empty($_POST['constraints'])) {
			/// Si l'utilisateur a renseigné des contraintes, on les traite
			$constraints = htmlspecialchars($_POST['constraints']);
			$tmp = explode(',', $constraints);

			$tab_constraints = array();

			foreach($tmp as $e) {
				$constraint = explode('=', $e);

				$constraint[0] = trim($constraint[0]);
				$constraint[1] = trim($constraint[1]);

				if(in_array($constraint[0], $bdd_infos[$table])) {
					array_push($tab_constraints, $constraint);
				}
			}
		}

		if(!empty($bdd_infos[$table])) {
			/// On crée la requête SQL
			$sql = "SELECT * FROM $table";

			/// On y ajoute les contraintes s'il y en a
			if(!empty($tab_constraints)) {
				$sql .= " WHERE ";

				foreach($tab_constraints as $constraint) {
					if(is_numeric($constraint[1])) {
						$sql .= "$constraint[0] = $constraint[1] AND ";
					} else {
						$sql .= "$constraint[0] LIKE '%$constraint[1]%' AND ";
					}
				}

				$sql .= "1";
			}

			$request = $connect->query($sql);
			$search = $request->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$error = 'Cette table n\'existe pas.';
		}
	} else {
		$error = 'Merci de choisir une table.';
	}
}

include('../include/sections/header.php');
include('../include/sections/navbar.php');

?>

<div class="container">
	<p class="lead">Recherche dans la base de données</p>
	<div class="alert alert-primary clearfix">
		<span class="align-middle">
			<span class="badge badge-pill badge-light mr-2">Question 2.a</span> <span class="align-middle">Cette page permet d'afficher les tuples d'une table, en contraignant ou non la valeur d'un ou plusieurs de ses attributs.</span>
		</span>
		<span class="align-middle">
			<button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#modalSearch">
				Guide pour la recherche
			</button>
		</span>
	</div>
	<!-- Guide pour la recherche -->
	<div class="modal fade" id="modalSearch" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalTitle">Guide pour effectuer une recherche</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<p>Pour effectuer une recherche dans la base de données, il faut tout d'abord sélectionner une <b>table</b> dans la liste déroulante.</p>
					<p>Des <b>contraintes</b> portant sur les attributs de la table peuvent être renseignées dans le champ prévu à cet effet. Si aucune contrainte n'est renseignée, tous les tuples de la table seront affichés.</p>
					<p>Une contrainte s'écrit sous la forme : <code>attribut1=value1, attribut2=value2, ...</code></p>
					<p>Les attributs de chaque table sont présentés dans le tableau suivant.</p>
					<table class="table table-sm table-borderless table-hover mb-0">
						<tbody>
							<?php

							foreach($bdd_infos as $key => $array) {
								echo '<tr><th scope="row">'.$key.'</th>';

								foreach($array as $column_name) {
									echo '<td>'.$column_name.'</td>';
								}

								echo '</tr>';
							}

							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php

	if(isset($error)) {
		echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.$error.'<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
	}

	?>
	<form method="post">
		<div class="form-group">
			<select class="custom-select" name="table" required>
				<?php

				if(!empty($bdd_infos)) {
					foreach($bdd_infos as $key => $array) {
						echo '<option value="'.$key.'">'.$key.'</option>';
					}
				} else {
					echo '<option>Aucune table disponible</option>';
				}

				?>
			</select>
		</div>
		<div class="form-group">
			<div class="input-group mb-3">
				<input type="text" class="form-control" placeholder="Contraintes de la recherche (facultatif)" name="constraints">
				<div class="input-group-append">
					<button type="submit" class="btn btn-primary">Rechercher</button>
				</div>
			</div>
		</div>
	</form>
	<?php

	if(isset($search)) {
		echo '<hr>';

		if(!empty($search)) {
			if(!empty($constraints)) {
				echo '<p>Résultat(s) de la recherche dans la table <b>'.$table.'</b> avec la contrainte <b>'.$constraints.'</b>.</p>';
			} else {
				echo '<p>Résultat(s) de la recherche dans la table <b>'.$table.'</b>.</p>';
			}

			echo '<table class="table table-sm table-borderless table-hover mb-0"><thead><tr>';

			foreach($bdd_infos[$table] as $column_name) {
				echo '<th scope="col">'.$column_name.'</th>';
			}

			echo '</tr></thead>';

			echo '<tbody>';

			foreach($search as $tuple) {
				echo '<tr>';

				foreach($tuple as $key => $value) {
					switch($key) {
						case 'url':
							echo '<td><a href="'.$value.'" target="_blank">'.$value.'</a></td>';
							break;

						case 'date_publication':
							echo '<td>'.date('d/m/Y', strtotime($value)).'</td>';
							break;

						default:
							echo '<td>'.$value.'</td>';
					}
				}

				echo '</tr>';
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
