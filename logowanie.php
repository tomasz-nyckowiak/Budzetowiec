<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
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
	
	</header>

	<main>
		
		<div class="container">
		
			<div class="row d-flex justify-content-center">
			
				<div class="col-auto">

					<form action="zaloguj.php" method="post" class="inputs p-5">						
						
						<div class="mb-3">							
							<input type="email" name="login" class="form-control" placeholder="e-mail" onfocus="this.placeholder=''" onblur="this.placeholder='e-mail'" required>							
						</div>
						
						<div class="mb-3">							
							<input type="password" name="haslo" class="form-control" placeholder="hasło" onfocus="this.placeholder=''" onblur="this.placeholder='hasło'" required>							
							 <?php						
								if (isset($_SESSION['blad'])) echo $_SESSION['blad'];				
							 ?>
						</div>						
						
						<button type="submit" class="btn customButton mt-5 p-3">Zaloguj się</button>						
					</form>					
				
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