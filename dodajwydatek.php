<?php

	session_start();
	
	if (!isset($_SESSION['zalogowany']))
	{
		header('Location: index.php');
		exit();
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
			$moje_kategorie = $polaczenie->query("SELECT kategoria FROM wydatki_kategorie_przypisane_do_uzytkownika");
			$tablica = array();
			while ($row = $moje_kategorie->fetch_assoc()) {
			$temp = $row['kategoria'];				
			array_push($tablica, "$temp");
			}
			
			$platnosci = $polaczenie->query("SELECT platnosc FROM sposoby_platnosci_przypisane_do_uzytkownika");
			$tablica2 = array();
			while ($wiersz = $platnosci->fetch_assoc()) {
			$temp2 = $wiersz['platnosc'];				
			array_push($tablica2, "$temp2");
			}
			
			$polaczenie->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
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
						<a class="nav-link onSite" href="dodajwydatek.php"> Dodaj wydatek </a>
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
	
		<div class="container">

			<div class="row mt-5 form">				
					
				<form action="dodanonowywydatek.php" method="post">
					
					<div class="row my-1 p-2">
					
						<div class="col-auto">
							<label for="inputAmount" class="col-form-label">Kwota:</label>
						</div>
					
						<div class="col-auto">
							<input type="number" step="0.01" name="kwotaWydatku" id="inputAmount" class="form-control" required>
						</div>
					
					</div>
					
					<div class="row my-1 p-2">
					
						<div class="col-auto">
							<label for="inputDate" class="col-form-label">Data:</label>
						</div>
					
						<div class="col-auto">
							<input type="date" name="dataWydatku" value="<?php echo date('Y-m-d');?>" id="inputDate" class="form-control" required>
						</div>
					
					</div>
					
					<div class="row my-1 p-2">

						<div class="col-auto">
							<label for="payments" class="col-form-label">Sposób płatności:</label>
						</div>							
						
						<div class="col-auto">
							<select class="form-select" id="payments" name="sposobyPlatnosci" aria-label="Sposób płatności">
								<option name="paymentOptions" value="<?php echo "$tablica2[0]";?>"><?php echo "$tablica2[0]";?></option>
								<option name="paymentOptions" value="<?php echo "$tablica2[1]";?>"><?php echo "$tablica2[1]";?></option>
								<option name="paymentOptions" value="<?php echo "$tablica2[2]";?>"><?php echo "$tablica2[2]";?></option>
							</select>							
						</div>						
					
					</div>

					<fieldset class="row my-1 p-2">
					
						<legend class="col-form-label">Kategoria:</legend>
				
						<div class="col-auto">
						
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1" value="<?php echo "$tablica[0]";?>">
							  <label class="form-check-label" for="inlineRadio1"><?php echo "$tablica[0]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="<?php echo "$tablica[1]";?>">
							  <label class="form-check-label" for="inlineRadio2"><?php echo "$tablica[1]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="<?php echo "$tablica[2]";?>">
							  <label class="form-check-label" for="inlineRadio3"><?php echo "$tablica[2]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio4" value="<?php echo "$tablica[3]";?>">
							  <label class="form-check-label" for="inlineRadio4"><?php echo "$tablica[3]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio5" value="<?php echo "$tablica[4]";?>">
							  <label class="form-check-label" for="inlineRadio5"><?php echo "$tablica[4]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio6" value="<?php echo "$tablica[5]";?>">
							  <label class="form-check-label" for="inlineRadio6"><?php echo "$tablica[5]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio7" value="<?php echo "$tablica[6]";?>">
							  <label class="form-check-label" for="inlineRadio7"><?php echo "$tablica[6]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio8" value="<?php echo "$tablica[7]";?>">
							  <label class="form-check-label" for="inlineRadio8"><?php echo "$tablica[7]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio9" value="<?php echo "$tablica[8]";?>">
							  <label class="form-check-label" for="inlineRadio9"><?php echo "$tablica[8]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio10" value="<?php echo "$tablica[9]";?>">
							  <label class="form-check-label" for="inlineRadio10"><?php echo "$tablica[9]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio11" value="<?php echo "$tablica[10]";?>">
							  <label class="form-check-label" for="inlineRadio11"><?php echo "$tablica[10]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio12" value="<?php echo "$tablica[11]";?>">
							  <label class="form-check-label" for="inlineRadio12"><?php echo "$tablica[11]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio13" value="<?php echo "$tablica[12]";?>">
							  <label class="form-check-label" for="inlineRadio13"><?php echo "$tablica[12]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio14" value="<?php echo "$tablica[13]";?>">
							  <label class="form-check-label" for="inlineRadio14"><?php echo "$tablica[13]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio15" value="<?php echo "$tablica[14]";?>">
							  <label class="form-check-label" for="inlineRadio15"><?php echo "$tablica[14]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio16" value="<?php echo "$tablica[15]";?>">
							  <label class="form-check-label" for="inlineRadio16"><?php echo "$tablica[15]";?></label>
							</div>
							<div class="form-check form-check-inline">
							  <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio17" value="<?php echo "$tablica[16]";?>">
							  <label class="form-check-label" for="inlineRadio17"><?php echo "$tablica[16]";?></label>
							</div>						
							
						</div>					
				
					</fieldset>
					
					<div class="row my-1 p-2">				
						<div class="col-auto">						
							<label for="CommentForExpense" class="col-form-label">Komentarz (opcjonalnie):</label>
							<textarea class="form-control" name="komentarzDoWydatku" id="CommentForExpense" rows="3" cols="60" minlength="10"></textarea>
						</div>				
					</div>				
					
					<div class="row my-1 p-2">					
						<div class="d-flex justify-content-start">						
							<input type="submit" value="Dodaj wydatek">
						</div>								
					</div>	

					<div class="row my-1 p-2">				
						<div class="d-flex justify-content-end">						
							<input class="btn btn-light" type="reset" value="Wyczyść">
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