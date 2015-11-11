<?php
	
	require_once("../configglobal.php");
	require_once("User.class.php");
	
	$database = "if15_siim_3";
	
	session_start();
	
	$mysqli = new mysqli($server_name,$server_username,$server_password,$database);
	
	//saadan ühenduse classi ja loon uue classi
	$User = new User($mysqli);
	
	
?>