<?php

	session_start();
	
	if (isset($_POST['REmail']))
	{
		//Udana walidacja!
		$all_OK = true;
		
		//Sprawdź poprawność imienia
		$imie = $_POST['RImie'];

		//Sprawdzenie długości imienia
		if ((strlen($imie)<3) || (strlen($imie)>20))
		{
			$all_OK = false;
			$_SESSION['e_imie'] = "Imię musi posiadać od 3 do 20 znaków!";
		}		
		
		// Sprawdź poprawność adresu email
		$email = $_POST['REmail'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$all_OK = false;
			$_SESSION['e_email'] = "Podaj poprawny adres e-mail!";
		}
		
		//Sprawdź poprawność hasła
		$haslo1 = $_POST['RHaslo1'];
		$haslo2 = $_POST['RHaslo2'];
		
		if ((strlen($haslo1)<8) || (strlen($haslo1)>20))
		{
			$all_OK = false;
			$_SESSION['e_haslo'] = "Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if ($haslo1!=$haslo2)
		{
			$all_OK = false;
			$_SESSION['e_haslo'] = "Podane hasła nie są identyczne!";
		}
		
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);

		//Czy zaakceptowano regulamin?
		if (!isset($_POST['regulamin']))
		{
			$all_OK = false;
			$_SESSION['e_regulamin'] = "Potwierdź akceptację regulaminu!";
		}
		
		//Czy jesteś Botem?
		$sekret = "6LcFQAwbAAAAAD9wtOzdbsrunzal86gaEamM9l8a";
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if ($odpowiedz->success==false)
		{
			$all_OK = false;
			$_SESSION['e_bot'] = "Potwierdź, że nie jesteś botem!";
		}
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_imie'] = $imie;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_haslo1'] = $haslo1;		
		$_SESSION['fr_haslo2'] = $haslo2;
		if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;		
		
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
				//Czy email już istnieje?
				$rezultat = $polaczenie->query("SELECT id FROM uzytkownicy WHERE email='$email'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$all_OK = false;
					$_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
				}
				
				if ($all_OK == true)
				{
					//Walidacja pomyślna - dodajemy użytkownika do bazy!
					if ($polaczenie->query("INSERT INTO uzytkownicy VALUES (NULL, '$imie', '$haslo_hash', '$email')"))
					{
						$_SESSION['udanarejestracja'] = true;
						header('Location: witamy.php');
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
	
	<script src="https://www.google.com/recaptcha/api.js"></script>
	
</head>

<body>
	
	<header>
	
		<h1 class="mainHeader text-center text-uppercase mt-2 p-1">Budżetowiec</h1>
	
	</header>

	<main>
		
		<div class="container">
		
			<div class="row d-flex justify-content-center">
			
				<div class="col-auto">

					<form method="post" class="inputs p-5">
						
						<div class="mb-3">
							<label for="InputName" class="form-label">Imię:</label>
							<input type="text" value="<?php
								if (isset($_SESSION['fr_imie']))
								{
									echo $_SESSION['fr_imie'];
									unset($_SESSION['fr_imie']);
								}							
							?>" name="RImie" class="form-control" id="InputName" required>							
						 </div>
						 
						 <?php						 
							if (isset($_SESSION['e_imie']))
							{
								echo '<div class="error">'.$_SESSION['e_imie'].'</div>';
								unset($_SESSION['e_imie']);
							}						 
						 ?>							
							
						<div class="mb-3">
							<label for="InputEmail" class="form-label">Adres e-mail:</label>
							<input type="email" value="<?php
								if (isset($_SESSION['fr_email']))
								{
									echo $_SESSION['fr_email'];
									unset($_SESSION['fr_email']);
								}
							?>" name="REmail" class="form-control" id="InputEmail" required>							
						 </div>
						 
						 <?php						 
							if (isset($_SESSION['e_email']))
							{
								echo '<div class="error">'.$_SESSION['e_email'].'</div>';
								unset($_SESSION['e_email']);
							}						 
						 ?>
						 
						<div class="mb-3">
							<label for="InputPassword1" class="form-label">Hasło:</label>
							<input type="password" value="<?php
								if (isset($_SESSION['fr_haslo1']))
								{
									echo $_SESSION['fr_haslo1'];
									unset($_SESSION['fr_haslo1']);
								}
							?>" name="RHaslo1" class="form-control" id="InputPassword1" required>							
						 </div>
						 
						 <?php						 
							if (isset($_SESSION['e_haslo']))
							{
								echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
								unset($_SESSION['e_haslo']);
							}						 
						 ?>
						 
						 <div class="mb-3">
							<label for="InputPassword2" class="form-label">Powtórz hasło:</label>
							<input type="password" value="<?php
								if (isset($_SESSION['fr_haslo2']))
								{
									echo $_SESSION['fr_haslo2'];
									unset($_SESSION['fr_haslo2']);
								}
							?>" name="RHaslo2" class="form-control" id="InputPassword2" required>							
						 </div>
						 
						 <div class="mb-3">
							 <label>
								<input type="checkbox" name="regulamin" <?php
								if (isset($_SESSION['fr_regulamin']))
								{
									echo "checked";
									unset($_SESSION['fr_regulamin']);
								}
									?>> Akceptuję regulamin
							</label>
						</div>
						
						<?php
							if (isset($_SESSION['e_regulamin']))
							{
								echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
								unset($_SESSION['e_regulamin']);
							}
						?>
						
						<div class="mb-3">
							<div class="g-recaptcha" data-sitekey="6LcFQAwbAAAAABDEIdPwu5QWsJGYuGXtNXVyS4qa"></div>
						</div>
						
						<?php
							if (isset($_SESSION['e_bot']))
							{
								echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
								unset($_SESSION['e_bot']);
							}
						?>
						 
						<button type="submit" class="btn customButton mt-5 p-3">Zarejestruj się</button>						
					</form>					
				
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