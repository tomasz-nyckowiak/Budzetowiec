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
			$ID_uzytkownika = $_SESSION['id'];
			
			//Ustawiamy odpowiedni okres czasu (poprzedni miesiąc)
			$aktualna_data = date('Y-m-d');
			$rok = date('Y');
			$poprzedni_miesiac = date_create("$aktualna_data");
			date_modify($poprzedni_miesiac,"-1 month");
			$wlasciwy_miesiac =  date_format($poprzedni_miesiac, "m");
			$wlasciwa_data =  date_format($poprzedni_miesiac, "Y-m");
			$number = cal_days_in_month(CAL_GREGORIAN, "$wlasciwy_miesiac", "$rok");	
			$poprzedni = "Od $rok-$wlasciwy_miesiac-01 do $rok-$wlasciwy_miesiac-$number";
			
			$suma_calkowita_przychodow = 0;
			$suma_calkowita_wydatkow = 0;
			
			//PRZYCHODY
			
			//Wyciągamy kategorie przychodów przypisanych do danego użytkownika i wstawiamy do tablicy
			$kategorie_przychodow = $polaczenie->query("SELECT kategoria FROM przychody_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$ID_uzytkownika'");
			$tablica_przychody = array();
			while ($row_przychody = $kategorie_przychodow->fetch_assoc()) {
			$temp_przychody = $row_przychody['kategoria'];				
			array_push($tablica_przychody, "$temp_przychody");
			}

			//Wyciągamy numery id przychodów przypisanych do danego użytkownika i wstawiamy do tablicy
			$id_kategorii_przychodow = $polaczenie->query("SELECT id FROM przychody_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$ID_uzytkownika'");
			$tablica_numerow_id = array();
			while ($row_id = $id_kategorii_przychodow->fetch_assoc()) {
			$temp_id = $row_id['id'];				
			array_push($tablica_numerow_id, "$temp_id");
			}
			
			$tab_size = count($tablica_numerow_id);			
						
			//Zwrócenie tablicy numerów id, odpowiadających danej kategorii przychodów (jeśli znajdują się w tabeli) dla danego użytkownika z tabeli "przychody"
			$numer_id_kategorii_przychodow = $polaczenie->query("SELECT DISTINCT kategoria_przypisana_do_danego_uzytkownika FROM przychody WHERE ID_uzytkownika = '$ID_uzytkownika' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$istniejace_numery_id_kategorii = array();
			while ($row_single_id = $numer_id_kategorii_przychodow->fetch_assoc()) {
			$temp2_id = $row_single_id['kategoria_przypisana_do_danego_uzytkownika'];				
			array_push($istniejace_numery_id_kategorii, "$temp2_id");
			}

			//Wyliczamy sumy kwot dla wszystkich kategorii (przychodów) dla poprzedniego miesiąca oraz sumy całkowitej przychodów i wstawiamy do tablicy
			$sumy_przychodow = $polaczenie->query("SELECT kategoria_przypisana_do_danego_uzytkownika, SUM(kwota) AS suma FROM przychody WHERE data LIKE '$wlasciwa_data%' AND ID_uzytkownika = '$ID_uzytkownika' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$tablica_sum_przychodow = array();
			while ($row_sum_przychody = $sumy_przychodow->fetch_assoc()) {				
			$temp_sumy_przychodow = $row_sum_przychody['suma'];
			$suma_calkowita_przychodow += $temp_sumy_przychodow;
			array_push($tablica_sum_przychodow, "$temp_sumy_przychodow");
			}			
			
			//Jeśli kategoria (jej nr id) znajduje się w tablicy "przychody", to odpowiadająca numerowi id kwota zostanie wyciągnięta z tablicy sum i przypisana do nowej zmiennej, a następnie wstawiona do finalnej tablicy; jeśli jej nie ma, to przypisujemy jej wartość 0
			for ($x = 0; $x < $tab_size; $x++)
			{								
				if (in_array("$tablica_numerow_id[$x]",  $istniejace_numery_id_kategorii))
				{
					$wyciagnieta_suma = array_shift($tablica_sum_przychodow);
					$tab_incomes[$x] = $wyciagnieta_suma;
				}
				else
				{
					$tab_incomes[$x] = 0;
				}				
			}
			
			//WYDATKI
			
			//Wyciągamy kategorie wydatków przypisanych do danego użytkownika i wstawiamy do tablicy
			$kategorie_wydatkow = $polaczenie->query("SELECT kategoria FROM wydatki_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$ID_uzytkownika'");
			$tablica_wydatki = array();
			while ($row_wydatki = $kategorie_wydatkow->fetch_assoc()) {
			$temp_wydatki = $row_wydatki['kategoria'];				
			array_push($tablica_wydatki, "$temp_wydatki");
			}
			
			//Wyciągamy numery id wydatków przypisanych do danego użytkownika i wstawiamy do tablicy
			$id_kategorii_wydatkow = $polaczenie->query("SELECT id FROM wydatki_kategorie_przypisane_do_uzytkownika WHERE ID_uzytkownika = '$ID_uzytkownika'");
			$tablica_numerow_id_wydatki = array();
			while ($row_id_wydatki = $id_kategorii_wydatkow->fetch_assoc()) {
			$temp_id_wydatki = $row_id_wydatki['id'];				
			array_push($tablica_numerow_id_wydatki, "$temp_id_wydatki");
			}
			
			$tab_size_wydatki = count($tablica_numerow_id_wydatki);
			
			//Zwrócenie tablicy numerów id, odpowiadających danej kategorii wydatków (jeśli znajdują się w tabeli) dla danego użytkownika z tabeli "wydatki"
			$numer_id_kategorii_wydatkow = $polaczenie->query("SELECT DISTINCT kategoria_przypisana_do_danego_uzytkownika FROM wydatki WHERE ID_uzytkownika = '$ID_uzytkownika' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$istniejace_numery_id_kategorii_wydatkow = array();
			while ($row_single_id_wydatki = $numer_id_kategorii_wydatkow->fetch_assoc()) {
			$temp2_id_wydatki = $row_single_id_wydatki['kategoria_przypisana_do_danego_uzytkownika'];				
			array_push($istniejace_numery_id_kategorii_wydatkow, "$temp2_id_wydatki");
			}
			
			//Wyliczamy sumy kwot dla wszystkich kategorii (wydatków) dla poprzedniego miesiąca oraz sumy całkowitej wydatków i wstawiamy do tablicy
			$sumy_wydatkow = $polaczenie->query("SELECT kategoria_przypisana_do_danego_uzytkownika, SUM(kwota) AS suma FROM wydatki WHERE data LIKE '$wlasciwa_data%' AND ID_uzytkownika = '$ID_uzytkownika' GROUP BY kategoria_przypisana_do_danego_uzytkownika");
			$tablica_sum_wydatkow = array();
			while ($row_sum_wydatki = $sumy_wydatkow->fetch_assoc()) {				
			$temp_sumy_wydatkow = $row_sum_wydatki['suma'];
			$suma_calkowita_wydatkow += $temp_sumy_wydatkow;
			array_push($tablica_sum_wydatkow, "$temp_sumy_wydatkow");
			}
			
			//Jeśli kategoria (jej nr id) znajduje się w tablicy "wydatki", to odpowiadająca numerowi id kwota zostanie wyciągnięta z tablicy sum i przypisana do nowej zmiennej, a następnie wstawiona do finalnej tablicy; jeśli jej nie ma, to przypisujemy jej wartość 0
			for ($x = 0; $x < $tab_size_wydatki; $x++)
			{								
				if (in_array("$tablica_numerow_id_wydatki[$x]",  $istniejace_numery_id_kategorii_wydatkow))
				{
					$wyciagnieta_suma_wydatkow = array_shift($tablica_sum_wydatkow);
					$tab_expenses[$x] = $wyciagnieta_suma_wydatkow;
				}
				else
				{
					$tab_expenses[$x] = 0;
				}				
			}

			//Bilans końcowy (przychody - wydatki)
			$bilans_koncowy = $suma_calkowita_przychodow - $suma_calkowita_wydatkow;
			
			if ($bilans_koncowy > 0)
			{
				$komunikat_koncowy = "Gratulacje. Świetnie zarządzasz finansami!";
			}
			else if ($bilans_koncowy < 0)
			{
				$komunikat_koncowy = "Uważaj, wpadasz w długi!";
			}
			
			$polaczenie->close();
		}
	}
	catch(Exception $e)
	{
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
		echo '<br />Informacja developerska: '.$e;
	}
	
	//WYKRES
	$expenses = array();
	$expensesOnChart = array();
	
	for ($y = 0; $y < $tab_size_wydatki; $y++)
	{								
		if ($tab_expenses[$y] != 0)
		{			
			$forChart['category'] = "$tablica_wydatki[$y]";
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
					//text: "Wydatki",
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
						<a class="nav-link onSite" href="przegladajbilanswybor.php"> Przeglądaj bilans </a>
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
					
							<button class="dropdown-item" type="button" onclick="location.href='przegladajbilans.php'">bieżący miesiąc</button>
							<button class="dropdown-item" type="button" onclick="location.href='przegladajbilans2.php'">poprzedni miesiąc</button>
							<button class="dropdown-item" type="button" onclick="location.href='przegladajbilans3.php'">bieżący rok</button>				
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
					<button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="resetFunction(); location.href='przegladajbilans4.php';">Zatwierdź</button>
				  </div>
				</div>
			  </div>
			</div>
			<!-- End of modal -->

			<div class="d-flex justify-content-center">			
				<div class="col-sm-6">					
					<p style="text-align: center; background-image: url('img/what-the-hex-dark.png');">Wybrano poprzedni miesiąc:</br>
					(<?php echo "$poprzedni";?>)</p>
				</div>			
			</div>

			<div class="row d-flex justify-content-center gx-5 gy-3">
				
				<div class="col-auto myTabs">
				
					<h2 class="h2 my-3"><span style="color: green">Przychody</span></h2>
					
					<div class="d-flex justify-content-center">

						<ul class="list-unstyled p-1 float-left">
							<li><?php echo "$tablica_przychody[0]";?> : <span><?php echo "$tab_incomes[0]";?></span></li>
							<li><?php echo "$tablica_przychody[1]";?> : <span><?php echo "$tab_incomes[1]";?></span></li>
							<li><?php echo "$tablica_przychody[2]";?> : <span><?php echo "$tab_incomes[2]";?></span></li>
							<li><?php echo "$tablica_przychody[3]";?> : <span><?php echo "$tab_incomes[3]";?></span></li>
							<li>Suma : <span><?php echo "$suma_calkowita_przychodow";?></span></li>
						</ul>								

					</div>					
				
				</div>				
			
				<div class="col-auto myTabs">
				
					<h2 class="h2 my-3"><span style="color: #e60000">Wydatki</span></h2>
					
					<div class="d-flex justify-content-center">
						<ul class="list-unstyled p-1 float-left">							
							<li><?php echo "$tablica_wydatki[0]";?> : <span><?php echo "$tab_expenses[0]";?></span></li>
							<li><?php echo "$tablica_wydatki[1]";?> : <span><?php echo "$tab_expenses[1]";?></span></li>
							<li><?php echo "$tablica_wydatki[2]";?> : <span><?php echo "$tab_expenses[2]";?></span></li>
							<li><?php echo "$tablica_wydatki[3]";?> : <span><?php echo "$tab_expenses[3]";?></span></li>
							<li><?php echo "$tablica_wydatki[4]";?> : <span><?php echo "$tab_expenses[4]";?></span></li>
							<li><?php echo "$tablica_wydatki[5]";?> : <span><?php echo "$tab_expenses[5]";?></span></li>
							<li><?php echo "$tablica_wydatki[6]";?> : <span><?php echo "$tab_expenses[6]";?></span></li>
							<li><?php echo "$tablica_wydatki[7]";?> : <span><?php echo "$tab_expenses[7]";?></span></li>
							<li><?php echo "$tablica_wydatki[8]";?> : <span><?php echo "$tab_expenses[8]";?></span></li>
							<li><?php echo "$tablica_wydatki[9]";?> : <span><?php echo "$tab_expenses[9]";?></span></li>
							<li><?php echo "$tablica_wydatki[10]";?> : <span><?php echo "$tab_expenses[10]";?></span></li>
							<li><?php echo "$tablica_wydatki[11]";?> : <span><?php echo "$tab_expenses[11]";?></span></li>
							<li><?php echo "$tablica_wydatki[12]";?> : <span><?php echo "$tab_expenses[12]";?></span></li>
							<li><?php echo "$tablica_wydatki[13]";?> : <span><?php echo "$tab_expenses[13]";?></span></li>
							<li><?php echo "$tablica_wydatki[14]";?> : <span><?php echo "$tab_expenses[14]";?></span></li>
							<li><?php echo "$tablica_wydatki[15]";?> : <span><?php echo "$tab_expenses[15]";?></span></li>
							<li><?php echo "$tablica_wydatki[16]";?> : <span><?php echo "$tab_expenses[16]";?></span></li>
							<li>Suma : <span><?php echo "$suma_calkowita_wydatkow";?></span></li>						
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
						<p><?php echo "$bilans_koncowy";?></p>
						<p><?php echo "$komunikat_koncowy";?></p>								
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