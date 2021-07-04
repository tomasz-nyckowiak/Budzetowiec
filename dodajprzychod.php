<?php

	session_start();
	
	if (!isset($_SESSION['logged_in']))
	{
		header('Location: index.php');
		exit();
	}
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	try
	{
		$conn = new mysqli($host, $db_user, $db_password, $db_name);
		
		if ($conn->connect_errno!=0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{			
			$categories = $conn->query("SELECT kategoria FROM przychody_kategorie_przypisane_do_uzytkownika");
			$tab = array();
			while ($row = $categories->fetch_assoc())
			{
				$temp = $row['kategoria'];				
				array_push($tab, "$temp");
			}					
			
			$conn->close();
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
	
	<title>Dodaj przychód</title>
	
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
						<a class="nav-link onSite" href="dodajprzychod.php"> Dodaj przychód </a>
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
		<div class="container">		
			<div class="row mt-5 form">			
				<form action="dodanonowyprzychod.php" method="POST">					
					<div class="row my-1 p-2">					
						<div class="col-auto">
							<label for="inputAmount" class="col-form-label">Kwota:</label>
						</div>					
						<div class="col-auto">
							<input type="number" step="0.01" name="incomeAmount" id="inputAmount" class="form-control" required>
						</div>					
					</div>					
					
					<div class="row my-1 p-2">					
						<div class="col-auto">
							<label for="inputDate" class="col-form-label">Data:</label>
						</div>					
						<div class="col-auto">
							<input type="date" name="incomeDate" value="<?php echo date('Y-m-d');?>" id="inputDate" class="form-control" required>
						</div>					
					</div>
					
					<fieldset class="row my-1 p-2">				
						<legend class="col-form-label">Kategoria:</legend>
						<div class="col-auto">
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="<?php echo "$tab[0]";?>" checked>
							<label class="form-check-label" for="gridRadios1"><?php echo "$tab[0]";?></label>
						  </div>
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="<?php echo "$tab[1]";?>">
							<label class="form-check-label" for="gridRadios2"><?php echo "$tab[1]";?></label>
						  </div>
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="<?php echo "$tab[2]";?>">
							<label class="form-check-label" for="gridRadios3"><?php echo "$tab[2]";?></label>
						  </div>
						  <div class="form-check">
							<input class="form-check-input" type="radio" name="gridRadios" id="gridRadios4" value="<?php echo "$tab[3]";?>">
							<label class="form-check-label" for="gridRadios4"><?php echo "$tab[3]";?></label>
						  </div>
						</div>
					</fieldset>
					
					<div class="row my-1 p-2">					
						<div class="col-auto">
							<label for="CommentForIncome" class="col-form-label">Komentarz (opcjonalnie):</label>
							<textarea class="form-control" name="comment" id="CommentForIncome" rows="3" cols="60" minlength="10"></textarea>
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