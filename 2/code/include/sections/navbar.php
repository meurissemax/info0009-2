<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
	<div class="container">
		<a class="navbar-brand" href="<?php echo $SERVER_NAME; ?>/">
			Articles scientifiques
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Navigation basculée">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbar">
			<div class="navbar-nav">
				<a class="nav-item nav-link <?php if($HEAD_TITLE == 'Recherche') { echo 'active'; } ?>" href="<?php echo $SERVER_NAME; ?>/questions/2a">Recherche</a>
				<a class="nav-item nav-link <?php if($HEAD_TITLE == 'Publications d\'un chercheur') { echo 'active'; } ?>" href="<?php echo $SERVER_NAME; ?>/questions/2b">Publications</a>
				<a class="nav-item nav-link <?php if($HEAD_TITLE == 'Ajouter un nouvel article') { echo 'active'; } ?>" href="<?php echo $SERVER_NAME; ?>/questions/2c">Nouvel article</a>
				<a class="nav-item nav-link <?php if($HEAD_TITLE == 'Participants auteurs') { echo 'active'; } ?>" href="<?php echo $SERVER_NAME; ?>/questions/2d">Participants auteurs</a>
				<a class="nav-item nav-link <?php if($HEAD_TITLE == 'Sujets de recherche populaires') { echo 'active'; } ?>" href="<?php echo $SERVER_NAME; ?>/questions/2e">Sujets populaires</a>
			</div>
		</div>
		<div class="collapse navbar-collapse justify-content-end" id="navbar">
			<a href="<?php echo $SERVER_NAME; ?>/logout" role="button" class="btn btn-outline-danger">Déconnexion</a>
		</div>
	</div>
</nav>