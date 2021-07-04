<?php

	session_start();
	
	if (isset($_POST['REmail']))
	{
		//Udana walidacja!
		$all_OK = true;
		
		//Sprawdź poprawność imienia
		$first_name = $_POST['RFirstName'];

		//Sprawdzenie długości imienia
		if ((strlen($first_name)<3) || (strlen($first_name)>20))
		{
			$all_OK = false;
			$_SESSION['e_first_name'] = "Imię musi posiadać od 3 do 20 znaków!";
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
		$password1 = $_POST['RPass1'];
		$password2 = $_POST['RPass2'];
		
		if ((strlen($password1)<8) || (strlen($password1)>20))
		{
			$all_OK = false;
			$_SESSION['e_password'] = "Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if ($password1!=$password2)
		{
			$all_OK = false;
			$_SESSION['e_password'] = "Podane hasła nie są identyczne!";
		}
		
		$pass_hash = password_hash($password1, PASSWORD_DEFAULT);

		//Czy zaakceptowano regulamin?
		if (!isset($_POST['terms']))
		{
			$all_OK = false;
			$_SESSION['e_terms'] = "Potwierdź akceptację regulaminu!";
		}
		
		//Czy jesteś Botem?
		$secret = "6LcFQAwbAAAAAD9wtOzdbsrunzal86gaEamM9l8a";
		
		$check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
		
		$response = json_decode($check);
		
		if ($response->success==false)
		{
			$all_OK = false;
			$_SESSION['e_bot'] = "Potwierdź, że nie jesteś botem!";
		}
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_first_name'] = $first_name;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_pass1'] = $password1;		
		$_SESSION['fr_pass2'] = $password2;
		if (isset($_POST['terms'])) $_SESSION['fr_terms'] = true;		
		
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
				//Czy email już istnieje?
				$result = $conn->query("SELECT id FROM uzytkownicy WHERE email='$email'");
				
				if (!$result) throw new Exception($conn->error);
				
				$how_many_emails = $result->num_rows;
				if ($how_many_emails > 0)
				{
					$all_OK = false;
					$_SESSION['e_email'] = "Istnieje już konto przypisane do tego adresu e-mail!";
				}
				
				if ($all_OK == true)
				{
					//Walidacja pomyślna - dodajemy użytkownika do bazy!
					if ($conn->query("INSERT INTO uzytkownicy VALUES (NULL, '$first_name', '$pass_hash', '$email')"))
					{
						$_SESSION['register_successful'] = true;
						
						//Pobieramy ID zalogowanego użytkownika
						$user_ID = $conn->query("SELECT id FROM uzytkownicy ORDER BY id DESC LIMIT 1")->fetch_object()->id;
						
						/*Po udanej rejestracji kopiujemy odpowiednie rekordy:
						- przychody_kategorie_domyslne do tabeli przychody_kategorie_przypisane_do_uzytkownika
						- wydatki_kategorie_domyslne do tabeli wydatki_kategorie_przypisane_do_uzytkownika
						- sposoby_platnosci_domyslne do tabeli sposoby_platnosci_przypisane_do_uzytkownika						
						*/
						
						if ($conn->query("INSERT INTO przychody_kategorie_przypisane_do_uzytkownika (ID_uzytkownika, kategoria) SELECT uzytkownicy.id, przychody_kategorie_domyslne.kategoria FROM uzytkownicy, przychody_kategorie_domyslne WHERE uzytkownicy.id = '$user_ID'"))
						{
							;								
						}
						else throw new Exception($conn->error);
						
						if ($conn->query("INSERT INTO wydatki_kategorie_przypisane_do_uzytkownika (ID_uzytkownika, kategoria) SELECT uzytkownicy.id, wydatki_kategorie_domyslne.kategoria FROM uzytkownicy, wydatki_kategorie_domyslne WHERE uzytkownicy.id = '$user_ID'"))
						{
							;								
						}
						else throw new Exception($conn->error);
						
						if ($conn->query("INSERT INTO sposoby_platnosci_przypisane_do_uzytkownika (ID_uzytkownika, platnosc) SELECT uzytkownicy.id, sposoby_platnosci_domyslne.platnosc FROM uzytkownicy, sposoby_platnosci_domyslne WHERE uzytkownicy.id = '$user_ID'"))
						{
							header('Location: witamy.php');								
						}
						else	throw new Exception($conn->error);
					}
					else	throw new Exception($conn->error);
				}				
				$conn->close();
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
	
	<title>Rejestracja</title>
	
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
					<form method="POST" class="inputs p-5">						
						<div class="mb-3">
							<label for="InputName" class="form-label">Imię:</label>
							<input type="text" value="<?php
								if (isset($_SESSION['fr_first_name']))
								{
									echo $_SESSION['fr_first_name'];
									unset($_SESSION['fr_first_name']);
								}							
							?>" name="RFirstName" class="form-control" id="InputName" required>							
						 </div>
						 
						 <?php						 
							if (isset($_SESSION['e_first_name']))
							{
								echo '<div class="error">'.$_SESSION['e_first_name'].'</div>';
								unset($_SESSION['e_first_name']);
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
								if (isset($_SESSION['fr_pass1']))
								{
									echo $_SESSION['fr_pass1'];
									unset($_SESSION['fr_pass1']);
								}
							?>" name="RPass1" class="form-control" id="InputPassword1" required>							
						 </div>
						 
						 <?php						 
							if (isset($_SESSION['e_password']))
							{
								echo '<div class="error">'.$_SESSION['e_password'].'</div>';
								unset($_SESSION['e_password']);
							}						 
						 ?>
						 
						 <div class="mb-3">
							<label for="InputPassword2" class="form-label">Powtórz hasło:</label>
							<input type="password" value="<?php
								if (isset($_SESSION['fr_pass2']))
								{
									echo $_SESSION['fr_pass2'];
									unset($_SESSION['fr_pass2']);
								}
							?>" name="RPass2" class="form-control" id="InputPassword2" required>							
						 </div>
						 
						 <div class="mb-3">
							 <label>
								<input type="checkbox" name="terms" <?php
								if (isset($_SESSION['fr_terms']))
								{
									echo "checked";
									unset($_SESSION['fr_terms']);
								}
									?>> Akceptuję regulamin
							</label>
						</div>
						
						<?php
							if (isset($_SESSION['e_terms']))
							{
								echo '<div class="error">'.$_SESSION['e_terms'].'</div>';
								unset($_SESSION['e_terms']);
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