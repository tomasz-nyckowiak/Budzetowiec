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
	
	<script src="showHide.js"></script>
	
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
						<a class="nav-link" href="dodajprzychod.html"> Dodaj przychód </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="dodajwydatek.html"> Dodaj wydatek </a>
					</li>
					<li class="nav-item">
						<a class="nav-link onSite" href="przegladajbilans.html"> Przeglądaj bilans </a>
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
		
			<div class="row mt-5">
				
				<div class="d-flex justify-content-end">
				
					<div class="dropdown">
				  
						<button class="btn btn-secondary choosingRange dropdown-toggle" type="button" id="submenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Wybierz okres
						</button>
					  
						<div class="dropdown-menu" aria-labelledby="submenu">
					
							<button class="dropdown-item" type="button" onclick="showCM()">bieżący miesiąc</button>
							<button class="dropdown-item" type="button" onclick="showPM()">poprzedni miesiąc</button>
							<button class="dropdown-item" type="button" onclick="showCY()">bieżący rok</button>				
							<div class="dropdown-divider" style="border-color:#C0C0C0;"></div>				
							<button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#myModal">niestandardowy</button>						
					
						</div>
					
					</div>
				
				</div>
			
			</div>
			
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="someModalLabel" aria-hidden="true">
			  <div class="modal-dialog">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="someModalLabel">Podaj zakres (data musi być w formacie rrrr-mm-dd)</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				  </div>
				  <div class="modal-body">
					
					<form id="modalForm">
					  						
						<div class="row d-flex justify-content-center align-items-center">								
							<div class="col-auto">
								<label for="date1" class="col-form-label">Od:</label>
							</div>
							<div class="col-auto">
								<input type="text" class="form-control" name="date1" maxlength="10" minlength="10">
							</div>																
							<div class="col-auto">
								<label for="date2" class="col-form-label">do:</label>
							</div>
							<div class="col-auto">
								<input type="text" class="form-control" name="date2" maxlength="10" minlength="10">
							</div>								  
						</div>
					  					  
					</form>
					
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetFunction()">Zamknij</button>
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="resetFunction(); showC();">Zatwierdź</button>
				  </div>
				</div>
			  </div>
			</div>
			<!-- End of modal -->			
			
			<div class="d-flex justify-content-center">			
				<div class="col-sm-6">				
					<div id="currentMonth">		
						<p>Wybrano bieżący miesiąc</p>		
					</div>
					<div id="previousMonth">		
						<p>Wybrano poprzedni miesiąc</p>		
					</div>
					<div id="currentYear">		
						<p>Wybrano bieżący rok</p>		
					</div>
					<div id="customizedRange">		
						<p>Wybrano niestandardowy okres</p>		
					</div>
				</div>			
			</div>	

			<div class="row d-flex justify-content-center gx-5 gy-3">
				
				<div class="col-auto myTabs">
				
					<h2 class="h2 my-3"><span style="color: green">Przychody</span></h2>
					
					<div class="d-flex justify-content-center">

						<ul class="list-unstyled p-1 float-left">
							<li>Wynagrodzenie : <span>kwota</span></li>
							<li>Odsetki bankowe : <span>kwota</span></li>
							<li>Sprzedaż na allegro : <span>kwota</span></li>
							<li>Inne : <span>kwota</span></li>
							<li>Suma : <span>kwota</span></li>
						</ul>								

					</div>					
				
				</div>				
			
				<div class="col-auto myTabs">
				
					<h2 class="h2 my-3"><span style="color: #e60000">Wydatki</span></h2>
					
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled p-1 float-left">							
							<li>Jedzenie : <span>kwota</span></li>
							<li>Mieszkanie : <span>kwota</span></li>
							<li style="color: yellow">Transport : <span>250</span></li>
							<li>Telekomunikacja : <span>kwota</span></li>
							<li>Opieka zdrowotna : <span>kwota</span></li>
							<li style="color: purple">Ubranie : <span>400</span></li>
							<li style="color: #47d1d1">Higiena : <span>120</span></li>
							<li>Dzieci : <span>kwota</span></li>
							<li>Rozrywka : <span>kwota</span></li>
							<li>Wycieczka : <span>kwota</span></li>
							<li>Szkolenia : <span>kwota</span></li>
							<li>Książki : <span>kwota</span></li>
							<li style="color: #d9b38c">Oszczędności : <span>520</span></li>
							<li>Na emeryturę : <span>kwota</span></li>
							<li>Spłata długów : <span>kwota</span></li>
							<li>Darowizna : <span>kwota</span></li>
							<li>Inne : <span>kwota</span></li>
							<li>Suma : <span>1290</span></li>						
						</ul>
					</div>
				
				</div>
				
				<div class="col-auto">						
					<div class="chart"></div>						
				</div>
			
			</div>	

			<div class="d-flex justify-content-center mt-5 p-1">
			
				<div class="col-auto balance p-1 text-center">
				
					<h2 class="h2 my-3"><span style="color: #FFD700">Bilans końcowy</span></h2>
					
					<div class="summary">									
						<p>KWOTA (przychody - wydatki)</p>
						<p>"Uważaj, wpadasz w długi!"</p>
						<p>lub</p>
						<p>"Gratulacje. Świetnie zarządzasz finansami!"</p>		
					</div>
					
				</div>
			
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