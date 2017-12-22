<?php 
		session_start();
		unset($_COOKIE['User']);
		setcookie('User', "", time() - 3600, "/");
		// remove all session variables
		session_unset(); 
		// destroy the session 
		session_destroy(); 
		header ('Location: login.html');	


?>






