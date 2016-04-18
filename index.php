<?php
    define("URL", "http://".$_SERVER["HTTP_HOST"].str_replace("index.php", "", $_SERVER["PHP_SELF"]));

	require_once 'controller/MainController.php';

	$controller = new MainController();
	$controller->invoke();
?>