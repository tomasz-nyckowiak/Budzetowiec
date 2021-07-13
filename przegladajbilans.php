<?php

	session_start();
	
	if (!isset($_SESSION['logged_in']))
	{
		header('Location: index.php');
		exit();
	}	
	
	//Ustawiamy domyślną wartość zmiennej na bieżący miesiąc / SETTING DEFAULT VALUE
	$temp = "showCM";
	
	if (isset($_POST['selectedCM'])) $temp = "showCM";
	if (isset($_POST['selectedPM'])) $temp = "showPM";
	if (isset($_POST['selectedCY'])) $temp = "showCY";
	if (isset($_POST['selectedCU']))
	{		
		$temp1 = $_POST['date1'];
		$temp2 = $_POST['date2'];
		$first_date = date_create("$temp1");
		$second_date = date_create("$temp2");
		
		$temp = "showCU";
		
		//Jeśli kolejność dat jest nieprawidłowa, to wyświetlamy komunikat o błędzie
		if ($first_date > $second_date)
		{			
			$_SESSION['e_wrongDateOrder'] = "Pierwsza data musi być wcześniejsza od drugiej!";
			header('Location: menuglowne.php');
			exit();			
		}		
	}	
	
	require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	try
	{
		$conn = new mysqli($host, $db_user, $db_password, $db_name);
		mysqli_set_charset($conn, "utf8");
		
		if ($conn->connect_errno!=0)
		{
			throw new Exception(mysqli_connect_errno());
		}
		else
		{			
			$user_ID = $_SESSION['id'];
			
			//Ustawiamy odpowiedni okres czasu
			$current_date = date('Y-m-d');
			$year = date('Y');
			$month = date('m');
			
			//Bieżący miesiąc / CURRENT MONTH
			if ($temp == "showCM")
			{
				$temp_date = date_create("$current_date");
				$proper_date =  date_format($temp_date, "Y-m");
				$days = cal_days_in_month(CAL_GREGORIAN, "$month", "$year");
				$date_one = "$proper_date-01";
				$date_two = "$proper_date-$days";				
			}			
			
			//Poprzedni miesiąc / PREVIOUS MONTH
			if ($temp == "showPM")
			{
				$temp_date = date_create("$current_date");
				date_modify($temp_date,"-1 month");
				$proper_month =  date_format($temp_date, "m");				
				$days = cal_days_in_month(CAL_GREGORIAN, "$proper_month", "$year");
				$date_one = "$year-$proper_month-01";
				$date_two = "$year-$proper_month-$days";				
			}
						
			//Bieżący rok / CURRENT YEAR
			if ($temp == "showCY")
			{
				$date_one = "$year-01-01";
				$date_two = "$year-12-31";				
			}				
			
			//Niestandardowy / CUSTOM
			if ($temp == "showCU")
			{
				$date_one =  date_format($first_date, "Y-m-d");
				$date_two =  date_format($second_date, "Y-m-d");							
			}			
			
			$show_message = "Okres od $date_one do $date_two";
			
			$incomes_total_amount = 0;
			$expenses_total_amount = 0;
			$no_incomes = false;
			$no_expenses = false;
			
			//Przychody / INCOMES
			$i = 0;
			$incomes_array = array();
			$tab_id_incomes = array();
			$incomes_categories = array();

			//Wyciągamy kategorie przychodów i odpowiadające im numery id dla danego użytkownika i wstawiamy do osobnych tablic
			$incomes_result = $conn->query("SELECT id, kategoria FROM przychody_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$user_ID'");			
			while ($row_incomes = $incomes_result->fetch_assoc())
			{
				$incomes_array[$i]['id'] = $row_incomes['id'];
				$incomes_array[$i]['kategoria'] = $row_incomes['kategoria'];
				$temp_id_inc = $incomes_array[$i]['id'];
				$temp_incomes = $incomes_array[$i]['kategoria'];
				array_push($tab_id_incomes, "$temp_id_inc");
				array_push($incomes_categories, "$temp_incomes");
				$i++;
			}
			
			$tab_size_incomes = count($tab_id_incomes);
						
			//Zwrócenie tablicy numerów id, odpowiadających danej kategorii przychodów (jeśli znajdują się w tabeli) dla danego użytkownika z tabeli "przychody"
			$number_id_income_cat = $conn->query("SELECT DISTINCT kategoria_przypisana_do_danego_uzytkownika FROM przychody WHERE ID_uzytkownika = '$user_ID' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$existing_numbers_id_of_cat_in_incomes = array();
			while ($row_single_id_incomes = $number_id_income_cat->fetch_assoc())
			{
				$temp2_id_incomes = $row_single_id_incomes['kategoria_przypisana_do_danego_uzytkownika'];				
				array_push($existing_numbers_id_of_cat_in_incomes, "$temp2_id_incomes");
			}

			//Wyliczamy sumy kwot dla wszystkich kategorii dla zadanego okresu czasu oraz sumy całkowitej przychodów i wstawiamy do tablicy
			$sum_incomes = $conn->query("SELECT kategoria_przypisana_do_danego_uzytkownika, SUM(kwota) AS suma FROM przychody WHERE data BETWEEN '$date_one' AND '$date_two' AND ID_uzytkownika = '$user_ID' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$tab_sum_incomes = array();
			while ($row_sum_incomes = $sum_incomes->fetch_assoc())
			{				
				$temp_sum_incomes = $row_sum_incomes['suma'];
				$incomes_total_amount += $temp_sum_incomes;
				array_push($tab_sum_incomes, "$temp_sum_incomes");								
			}			
			
			//Jeśli kategoria (jej nr id) znajduje się w tablicy "przychody", to odpowiadająca numerowi id kwota zostanie wyciągnięta z tablicy sum i przypisana do nowej zmiennej, a następnie wstawiona do finalnej tablicy; jeśli jej nie ma, to przypisujemy jej wartość 0
			for ($x = 0; $x < $tab_size_incomes; $x++)
			{				
				if (in_array("$tab_id_incomes[$x]",  $existing_numbers_id_of_cat_in_incomes))
				{
					$pulled_out_amount_inc = array_shift($tab_sum_incomes);
					if (is_null($pulled_out_amount_inc)) $pulled_out_amount_inc = 0;
					$tab_incomes[$x] = $pulled_out_amount_inc;
				}
				else $tab_incomes[$x] = 0;				
			}
			
			//Wydatki / EXPENSES
			$j = 0;
			$expenses_array = array();
			$tab_id_expenses = array();
			$expenses_categories = array();			
			
			//Wyciągamy kategorie wydatków i odpowiadające im numery id dla danego użytkownika i wstawiamy do osobnych tablic
			$expenses_result = $conn->query("SELECT id, kategoria FROM wydatki_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$user_ID'");			
			while ($row_expenses = $expenses_result->fetch_assoc())
			{
				$expenses_array[$j]['id'] = $row_expenses['id'];
				$expenses_array[$j]['kategoria'] = $row_expenses['kategoria'];
				$temp_id_exp = $expenses_array[$j]['id'];
				$temp_expenses = $expenses_array[$j]['kategoria'];
				array_push($tab_id_expenses, "$temp_id_exp");
				array_push($expenses_categories, "$temp_expenses");
				$j++;
			}			
			
			$tab_size_expenses = count($tab_id_expenses);
			
			//Zwrócenie tablicy numerów id, odpowiadających danej kategorii wydatków (jeśli znajdują się w tabeli) dla danego użytkownika z tabeli "wydatki"
			$number_id_expense_cat = $conn->query("SELECT DISTINCT kategoria_przypisana_do_danego_uzytkownika FROM wydatki WHERE ID_uzytkownika = '$user_ID' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$existing_numbers_id_of_cat_in_expenses = array();
			while ($row_single_id_expenses = $number_id_expense_cat->fetch_assoc())
			{
				$temp2_id_expenses = $row_single_id_expenses['kategoria_przypisana_do_danego_uzytkownika'];				
				array_push($existing_numbers_id_of_cat_in_expenses, "$temp2_id_expenses");
			}
			
			//Wyliczamy sumy kwot dla wszystkich kategorii dla zadanego okresu czasu oraz sumy całkowitej wydatków i wstawiamy do tablicy
			$sum_expenses = $conn->query("SELECT kategoria_przypisana_do_danego_uzytkownika, SUM(kwota) AS suma FROM wydatki WHERE data BETWEEN '$date_one' AND '$date_two' AND ID_uzytkownika = '$user_ID' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$tab_sum_expenses = array();
			while ($row_sum_expenses = $sum_expenses->fetch_assoc())
			{				
				$temp_sum_expenses = $row_sum_expenses['suma'];
				$expenses_total_amount += $temp_sum_expenses;
				array_push($tab_sum_expenses, "$temp_sum_expenses");
			}
			
			//Jeśli kategoria (jej nr id) znajduje się w tablicy "wydatki", to odpowiadająca numerowi id kwota zostanie wyciągnięta z tablicy sum i przypisana do nowej zmiennej, a następnie wstawiona do finalnej tablicy; jeśli jej nie ma, to przypisujemy jej wartość 0
			for ($x = 0; $x < $tab_size_expenses; $x++)
			{								
				if (in_array("$tab_id_expenses[$x]",  $existing_numbers_id_of_cat_in_expenses))
				{
					$pulled_out_amount_exp = array_shift($tab_sum_expenses);
					if (is_null($pulled_out_amount_exp)) $pulled_out_amount_exp = 0;
					$tab_expenses[$x] = $pulled_out_amount_exp;
				}
				else	$tab_expenses[$x] = 0;				
			}

			//Bilans końcowy (przychody - wydatki)
			$balance_sheet = $incomes_total_amount - $expenses_total_amount;
			
			if ($balance_sheet > 0)
			{
				$final_message = "Gratulacje. Świetnie zarządzasz finansami!";
			}
			else if ($balance_sheet < 0)
			{
				$final_message = "Uważaj, wpadasz w długi!";
			}
			else if ($balance_sheet == 0)
			{
				$final_message = "Wyszedłeś na 0!";
			}
			
			$conn->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
	
	//Wykres / CHART
	$expenses = array();
	$expensesOnChart = array();
	
	for ($y = 0; $y < $tab_size_expenses; $y++)
	{								
		if ($tab_expenses[$y] != 0)
		{			
			$forChart['category'] = "$expenses_categories[$y]";
			$forChart['amount'] = "$tab_expenses[$y]";
			$expenses[] = $forChart;
		}			
	}
	
	foreach ($expenses as $chartPie)
	{					
		array_push($expensesOnChart, array("label"=>$chartPie['category'], "y"=>$chartPie['amount']));		
	}	

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">	
	
	<title>Przeglądaj bilans</title>	
	
	<meta name="description" content="Moja pierwsza aplikacja internetowa">
	<meta name="keywords" content="prowadzenie budżetu, domowy budżet, budżet, jak oszczędzać, oszczędzanie, finanse, kontrola wydatków, przychody, wydatki, bilans, bilans finansowy">
	<meta name="author" content="Tomasz Nyćkowiak">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/fontello.css" type="text/css">
	<link rel="stylesheet" href="style.css" type="text/css">	
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Arimo:wght@400;700&display=swap" rel="stylesheet">
	
	<script src="js/showHide.js"></script>
	<script>
		window.onload = function()
		{		 
			CanvasJS.addColorSet("customColorSet",
                [//colorSet Array
                "#007FFF",
                "#E6E600",
                "#DC143C",
                "#6E6F2F",
                "#800080",                
                "#8AA4B7",                
                "#F23B1C",                
                "#000080",                
                "#FFC0CB",                
                "#CADC79",                
                "#4D1F1C",                
                "#FAFAE7",                
                "#9966CC",                
                "#FFC000",                
                "#7FFFD4",                
                "#FAFFFA",                
                "#9D5B03"                
                ]);
			
			var chart = new CanvasJS.Chart("chartContainer", {
				colorSet: "customColorSet",
				animationEnabled: true,
				backgroundColor: "#2E8B57",
				title: {					
					fontFamily: "Arimo",
					fontColor: "#C0C0C0",					
					fontSize: 25,
				},
				data: [{
					type: "pie",
					indexLabel: "#percent%",
					yValueFormatString: "#0.##",
					toolTipContent: "{label} - (#percent%)",
					indexLabelPlacement: "inside",
					indexLabelFontColor: "#36454F",
					indexLabelFontSize: 15,
					indexLabelFontWeight: "bolder",
					dataPoints: <?php echo json_encode($expensesOnChart, JSON_NUMERIC_CHECK); ?>
				}]
			});
			chart.render();			 
		}
	</script>	
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
						<a class="nav-link onSite" href="przegladajbilans.php"> Przeglądaj bilans </a>
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
			<div class="row mt-5">				
				<div class="d-flex justify-content-end">				
					<div class="dropdown">				  
						<button class="btn btn-secondary choosingRange dropdown-toggle" type="button" id="submenu" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Wybierz okres
						</button>					  
						<div class="dropdown-menu" aria-labelledby="submenu">					
							<form method="POST">
								<button class="dropdown-item" type="submit" name="selectedCM" onclick="location.href='przegladajbilans.php'">bieżący miesiąc</button>
								<button class="dropdown-item" type="submit" name="selectedPM" onclick="location.href='przegladajbilans.php'">poprzedni miesiąc</button>
								<button class="dropdown-item" type="submit" name="selectedCY" onclick="location.href='przegladajbilans.php'">bieżący rok</button>				
								<div class="dropdown-divider" style="border-color:#C0C0C0;"></div>				
								<button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#myModal">niestandardowy</button>
							</form>					
						</div>					
					</div>				
				</div>			
			</div>
			
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="someModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="someModalLabel">Wybierz własny zakres dat:</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">					
							<form id="modalForm" method="POST">					  						
								<div class="row d-flex justify-content-center align-items-center">								
									<div class="col-auto">
										<label for="date1" class="col-form-label">Od:</label>
									</div>
									<div class="col-auto">
										<input type="date" class="form-control" name="date1" id="date1" style="width:165px;" required>
									</div>
								</div>
								<div class="row d-flex justify-content-center align-items-center">
									<div class="col-auto">
										<label for="date2" class="col-form-label">do:</label>
									</div>
									<div class="col-auto">
										<input type="date" class="form-control" name="date2" id="date2" style="width:165px;" required>
									</div>								  
								</div>								
								<div class="row d-flex justify-content-end align-items-center">
									<div class="col-auto">
										<button type="submit" name="selectedCU" id="save" class="btn btn-primary">Zatwierdź</button>
									</div>
								</div>				  
							</form>					
						</div>
						<div class="modal-footer justify-content-center">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetFunction()">Zamknij</button>						
						</div>
					</div>
				</div>
			</div>
			<!-- End of modal -->

			<div class="d-flex justify-content-center">			
				<div class="col-sm-6">				
					<p style="text-align: center; background-image: url('img/what-the-hex-dark.png');"><?php echo "$show_message";?></p>			
				</div>			
			</div>

			<div class="row d-flex justify-content-center gx-5 gy-3">				
				<div class="col-auto myTabs">				
					<h2 class="h2 my-3"><span style="color: green">Przychody</span></h2>					
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled p-1 float-left">
							<?php								
								for ($x = 0; $x < $tab_size_incomes; $x++)
								{
									if ($tab_incomes[$x] != 0)
									{
										echo '<li>';
										echo "$incomes_categories[$x] : $tab_incomes[$x]";										
										echo '</li>';										
									}									
								}
								
								if ($incomes_total_amount == 0)
								{
									$no_incomes = true;
									echo '<li>';
									echo "Brak przychodów!";
									echo '</li>';
								}
								else
								{
									echo '<li>';
									echo "Suma : $incomes_total_amount";										
									echo '</li>';
								}								
							?>							
						</ul>
					</div>				
				</div>				
			
				<div class="col-auto myTabs">				
					<h2 class="h2 my-3"><span style="color: #e60000">Wydatki</span></h2>					
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled p-1 float-left">							
							<?php
								for ($x = 0; $x < $tab_size_expenses; $x++)
								{
									if ($tab_expenses[$x] != 0)
									{
										echo '<li>';
										echo "$expenses_categories[$x] : $tab_expenses[$x]";										
										echo '</li>';										
									}									
								}
								
								if ($expenses_total_amount == 0)
								{
									$no_expenses = true;
									echo '<li>';
									echo "Brak wydatków!";
									echo '</li>';
								}
								else
								{
									echo '<li>';
									echo "Suma : $expenses_total_amount";										
									echo '</li>';
								}								
							?>						
						</ul>
					</div>				
				</div>
				
				<div class="col-auto">						
					<div id="chartContainer" style="height: 350px; width: 350px;"></div>						
				</div>			
			</div>
			
			<div class="d-flex justify-content-center mt-5 p-1">			
				<div class="col-auto balance p-1 text-center">				
					<h2 class="h2 my-3"><span style="color: #FFD700">Bilans końcowy</span></h2>					
					<div class="summary">									
						<p>
						<?php
							if (($no_incomes == true) && ($no_expenses == true))
							{
								echo "Brak przychodów i wydatków!";
							}
							else echo nl2br("$balance_sheet\n$final_message");							
						?>
						</p>														
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
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>	
</body>
</html>