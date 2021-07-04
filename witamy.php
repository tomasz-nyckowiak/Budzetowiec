<?php

	session_start();
	
	if (!isset($_SESSION['register_successful']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['register_successful']);		
	}
	
	//Usuwanie zmiennych pamiętających wartości wpisane do formularza
	if (isset($_SESSION['fr_first_name'])) unset($_SESSION['fr_first_name']);
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if (isset($_SESSION['fr_pass1'])) unset($_SESSION['fr_pass1']);
	if (isset($_SESSION['fr_pass2'])) unset($_SESSION['fr_pass2']);
	if (isset($_SESSION['fr_terms'])) unset($_SESSION['fr_terms']);
	
	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_first_name'])) unset($_SESSION['e_first_name']);
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_password'])) unset($_SESSION['e_password']);
	if (isset($_SESSION['e_terms'])) unset($_SESSION['e_terms']);
	if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Strona powitania</title>
	
	<meta name="description" content="Moja pierwsza aplikacja internetowa">
	<meta name="keywords" content="prowadzenie budżetu, domowy budżet, budżet, jak oszczędzać, oszczędzanie, finanse, kontrola wydatków, przychody, wydatki, bilans, bilans finansowy">
	<meta name="author" content="Tomasz Nyćkowiak">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css">
	<link rel="stylesheet" href="style.css" type="text/css">	
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Arimo:wght@400;700&display=swap" rel="stylesheet">	
</head>

<body>
	<header>	
		<h1 class="mainHeader text-center text-uppercase mt-2 p-1">Budżetowiec</h1>	
	</header>

	<main>		
		<div class="container my-4">		
			<div class="greetings p-2">
				<p style="text-align: center;">Dziękujemy za rejestrację w serwisie! Możesz już się zalogować na swoje konto!<br /><br />
				<a href="logowanie.php">Zaloguj się na swoje konto!</a></p>
			</div>		
		</div>		
	</main>
	
	<footer class="fixed-bottom">		
		<div class="d-flex justify-content-center footer mt-5 mb-2 p-1">
			<div class="col-auto">
				Wszelkie prawa zastrzeżone &copy; 2021 | <i class="icon-mail"></i> tomasz.nyckowiak.programista@gmail.com
			</div>			
		</div>	
	</footer>
	
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>	
	<script src="js/bootstrap.min.js"></script>
</body>
</html>