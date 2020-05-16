<?php

include('include/config.php');
include('include/tools.php');

if(connect()) {
	session_destroy();
}

redirect('login?from=logout');

?>
