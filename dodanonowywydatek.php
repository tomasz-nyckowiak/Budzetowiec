<?php

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
	}
	
	if (isset($_POST['kwotaWydatku']))
	{
		$ID_uzytkownika = $_SESSION['id'];
		$kwota = $_POST['kwotaWydatku'];
		$data = $_POST['dataWydatku'];
		$sposob_platnosci = $_POST['sposobyPlatnosci'];
		$wybrana_kategoria = $_POST['inlineRadioOptions'];
		
		if (isset($_POST['komentarzDoWydatku']))
		{
			$komentarz = $_POST['komentarzDoWydatku'];
			$istnieje_komentarz = true;
		}
		else
		{
			$istnieje_komentarz = false;
		}
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			
			if ($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{				
				$id_kategorii = $polaczenie->query("SELECT id FROM wydatki_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$ID_uzytkownika' AND kategoria = '$wybrana_kategoria'")->fetch_object()->id;
				
				$id_platnosci = $polaczenie->query("SELECT id FROM sposoby_platnosci_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$ID_uzytkownika' AND platnosc = '$sposob_platnosci'")->fetch_object()->id;
				
				if ($istnieje_komentarz == true)
				{
					if ($polaczenie->query("INSERT INTO wydatki (ID_uzytkownika, kwota, data, sposob_platnosci_przypisany_do_danego_uzytkownika, kategoria_przypisana_do_danego_uzytkownika, komentarz) VALUES ('$ID_uzytkownika', '$kwota', '$data', '$id_platnosci', '$id_kategorii', '$komentarz')"))
					{
						;								
					}
					else
					{
						throw new Exception($polaczenie->error);
					}
				}
				else
				{
					if ($polaczenie->query("INSERT INTO wydatki (ID_uzytkownika, kwota, data, sposob_platnosci_przypisany_do_danego_uzytkownika, kategoria_przypisana_do_danego_uzytkownika) VALUES ('$ID_uzytkownika', '$kwota', '$data', '$id_platnosci', '$id_kategorii')"))
					{
						;						
					}
					else
					{
						throw new Exception($polaczenie->error);
					}						
				}
				
				$polaczenie->close();				
			}
			
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}				
		
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
						<a class="nav-link" href="dodajprzychod.php"> Dodaj przychód </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="dodajwydatek.php"> Dodaj wydatek </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="przegladajbilans.php"> Przeglądaj bilans </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="ustawienia.php"> Ustawienia </a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="logout.php"> Wyloguj </a>
					</li>
				</ol>
			
			</div>
		
		</nav>
	
	</header>
	
	<main>	
		<div class="row">
			<div class="col-auto">
				<p class="greetings p-2">Dodano nowy wydatek!</p>
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