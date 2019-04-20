<?php

include('../../include/config.php');
include('../../include/tools.php');
include('../../include/db.php');

$HEAD_TITLE = 'Initialiser la base de données';

/// On récupère le nom de toutes les tables qui existent
$sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbname'";
$request = $connect->query($sql);
$table_names = $request->fetchAll(PDO::FETCH_NUM);

if($_GET) {
	if(isset($_GET['action'])) {
		$action = htmlspecialchars($_GET['action']);

		/// Action de créer les tables et charger les données
		if($action == 'create') {
			$sql = file_get_contents('create.sql');
			$sql .= file_get_contents('load.sql');
			$connect->query($sql);

			redirect('?success=create');
		}

		/// Action de supprimer les tables
		if($action == 'drop') {
			$sql = file_get_contents('drop.sql');
			$connect->query($sql);

			redirect('?success=drop');
		}
	}
}

include('../../include/sections/header.php');

?>

<div class="container mt-5">
	<?php

	if($_GET) {
		if(isset($_GET['success'])) {
			$success = htmlspecialchars($_GET['success']);

			if($success == 'create') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Base de données initialisée avec succès !<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
			}

			if($success == 'drop') {
				echo '<div class="alert alert-success alert-dismissible fade show" role="alert">Tables de la base de données effacées avec succès !<button type="button" class="close" data-dismiss="alert" aria-label="Fermer"><span aria-hidden="true">&times;</span></button></div>';
			}
		}
	}

	?>
	<div class="row mb-3">
		<div class="col-sm">
			<div class="card">
				<div class="card-body">
					<p class="lead">Initialiser la base de données</p>
					<div class="alert alert-light">
						<span class="badge badge-pill badge-primary mr-2 align-middle">Question 1</span> <span class="align-middle">Ce bouton permet d'initialiser la base de données, c.-à.-d. créer de nouvelles tables sur base des fichiers fournis :</span>
						<?php

						if($content = scandir('../../resources/csv/')) {
							echo '<ul>';

							foreach($content as $file) {
								if(!is_dir($file) && $file != '.' && $file != '..') {
									echo '<li>'.$file.'</li>';
								}
							}

							echo '</ul>';
						} else {
							echo 'Erreur d\'affichage des fichiers.';
						}
						
						?>
					</div>
					<a href="?action=create" class="btn btn-success" role="button">Initialiser la base de données</a>
				</div>
			</div>
		</div>
		<div class="col-sm">
			<div class="card">
				<div class="card-body">
					<p class="lead">Effacer les tables de la base de données</p>
					<div class="alert alert-light">
						Ce bouton permet de supprimer toutes les tables de la base de données. Actuellement, la base de données contient les tables suivantes :
						<?php

						if(empty($table_names)) {
							echo ' <b>aucune table créée.</b>';
						} else {
							echo '<ul>';

							foreach($table_names as $name) {
								echo '<li>'.$name[0].'</li>';
							}

							echo '</ul>';
						}

						?>
					</div>
					<a href="?action=drop" class="btn btn-danger" role="button">Effacer les tables de la base de données</a>
				</div>
			</div>
		</div>
	</div>

	<a href="../../" class="btn btn-outline-primary" role="button">Revenir au site</a>
</div>

<?php

include('../../include/sections/footer.php');

?>
