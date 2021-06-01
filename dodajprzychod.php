<?php

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
?>

<!DOCTYPE HTML>
<html lang="pl">

<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>Aplikacja internetowa do prowadzenia budżetu domowego</title>
	
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
		
		<nav class="navbar navbar-dark navMenu navbar-expand-md">

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainmenu" aria-controls="mainmenu" aria-expanded="false" aria-label="Przełącznik nawigacji">
				<span class="navbar-toggler-icon"></span>
			</button>
			
			<div class="collapse navbar-collapse" id="mainmenu">
			
				<ol class="navbar-nav text-sm-center mx-auto">
					<li class="nav-item">
						<a class="nav-link onSite" href="dodajprzychod.html"> Dodaj przychód </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="dodajwydatek.html"> Dodaj wydatek </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="przegladajbilans.html"> Przeglądaj bilans </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="ustawienia.html"> Ustawienia </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="index.html"> Wyloguj </a>
					</li>
				</ol>
			
			</div>
		
		</nav>	
	
	</header>	
		
	<main>		
	
		<div class="container">		
		
			<div class="row mt-5 form">
			
				<form>
					
					<div class="row my-1 p-2">
					
						<div class="col-auto">
							<label for="inputAmount" class="col-form-label">Kwota:</label>
						</div>
					
						<div class="col-auto">
							<input type="number" id="inputAmount" class="form-control" required>
						</div>
					
					</div>
					
					<div class="row my-1 p-2">
					
						<div class="col-auto">
							<label for="inputDate" class="col-form-label">Data:</label>
						</div>
					
						<div class="col-auto">
							<input type="date" id="inputDate" class="form-control" required>
						</div>
					
					</div>
					
					<fieldset class="row my-1 p-2">				
						<legend class="col-form-label">Kategoria:</legend>
						<div class="col-auto">
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="option1" checked>
							<label class="form-check-label" for="gridRadios1">Wynagrodzenie</label>
						  </div>
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="option2">
							<label class="form-check-label" for="gridRadios2">Odsetki bankowe</label>
						  </div>
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="option3">
							<label class="form-check-label" for="gridRadios3">Sprzedaż na allegro</label>
						  </div>
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios4" value="option4">
							<label class="form-check-label" for="gridRadios4">Inne</label>
						  </div>
						</div>
					</fieldset>
					
					<div class="row my-1 p-2">
					
						<div class="col-auto">
							<label for="CommentForIncome" class="col-form-label">Komentarz (opcjonalnie):</label>
							<textarea class="form-control" id="CommentForIncome" rows="3" cols="60" minlength="10"></textarea>
						</div>			
					
					</div>
					
					<div class="row my-1 p-2">								
						<div class="d-flex justify-content-start">						
							<input type="submit" value="Dodaj przychód">
						</div>						
					</div>
					
					<div class="row my-1 p-2">				
						<div class="d-flex justify-content-end">
							<button type="button" name="cancel" class="btn btn-outline-secondary">Anuluj</button>
						</div>				
					</div>
					
				</form>
				
			</div>		
		
		</div>		

	</main>
	
	<footer>
		
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