<?php

	session_start();
	
	if ((isset($_SESSION['logged_in'])) && ($_SESSION['logged_in']==true))
	{
		header('Location: menuglowne.php');
		exit();
	}

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Budżetowiec - aplikacja internetowa do prowadzenia budżetu domowego</title>
	
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
			<div class="row text-center mt-5 p-3 about">			
				<div class="col-auto">				
					<p>Czym jest <span style="color: #C0C0C0;">BUDŻETOWIEC</span>?
					To osobista aplikacja internetowa do prowadzenia budżetu domowego.</p>
					<p>Jak zacząć znajomość z <span style="color: #C0C0C0;">BUDŻETOWCEM</span>?
					Wystarczy założyć konto na stronie rejestracji. Po zalogowaniu można dodawać swoje przychody i wydatki oraz przeglądać bilans.</p>
					<p>Dlaczego miałbym/miałabym używać <span style="color: #C0C0C0;">BUDŻETOWCA</span>?
					Za jego pomocą zapanujesz nad wydatkami, co pozwoli zaoszczędzić pięniądze! Dzięki prostemu i wygodnemu w obsłudze interfejsowi kontrolowanie swoich finansowych transakcji nigdy nie było przyjemniejsze!</p>			
				</div>

				<div class="d-flex justify-content-center p-5 mainNav">				
					<div class="col-auto p-1">
						<p><a href="rejestracja.php" title="Załóż konto!">Rejestracja</a></p>				
						<p><a href="logowanie.php" title="Zaloguj się!">Logowanie</a></p>
					</div>	
				</div>
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