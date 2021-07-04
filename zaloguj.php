<?php

	session_start();
	
	if ((!isset($_POST['login'])) || (!isset($_POST['haslo'])))
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
			$login = $_POST['login'];
			$pass = $_POST['haslo'];
			
			$login = htmlentities($login, ENT_QUOTES, "UTF-8");		
		
			if ($result = $conn->query(
			sprintf("SELECT * FROM uzytkownicy WHERE email='%s'",
			mysqli_real_escape_string($conn,$login))))
			{
				$how_many_users = $result->num_rows;
				if ($how_many_users > 0)
				{
					$row = $result->fetch_assoc();
					
					if (password_verify($pass, $row['haslo']))
					{					
						$_SESSION['logged_in'] = true;					
						$_SESSION['id'] = $row['id'];
						$_SESSION['imie'] = $row['imie'];				
						$_SESSION['email'] = $row['email'];
						
						$_SESSION['first_time'] = true;
						
						unset($_SESSION['error']);
						$result->free_result();
						header('Location: menuglowne.php');				
					}
					else
					{					
						$_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';						
						header('Location: logowanie.php');
					}					
				}
				else
				{					
					$_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					header('Location: logowanie.php');
				}
			}
			else	throw new Exception($conn->error);			
			
			$conn->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}

?>