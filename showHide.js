function showCM()
{	
	var x = document.getElementById("currentMonth");	
	var a = document.getElementById("previousMonth");
	var b = document.getElementById("currentYear");
	var c = document.getElementById("customizedRange");	
	
	if (x.style.display == "none" || x.style.display == "")
	{
		x.style.display = "block";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
    }	
	else
	{
		x.style.display = "none";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
	}
}

function showPM()
{	
	var x = document.getElementById("previousMonth");	
	var a = document.getElementById("currentMonth");	
	var b = document.getElementById("currentYear");
	var c = document.getElementById("customizedRange");	
	
	if (x.style.display == "none" || x.style.display == "")
	{
		x.style.display = "block";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
    }	
	else
	{
		x.style.display = "none";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
	}
}

function showCY()
{	
	var x = document.getElementById("currentYear");	
	var a = document.getElementById("currentMonth");
	var b = document.getElementById("previousMonth");	
	var c = document.getElementById("customizedRange");	
	
	if (x.style.display == "none" || x.style.display == "")
	{
		x.style.display = "block";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
    }	
	else
	{
		x.style.display = "none";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
	}
}

function showC()
{	
	var x = document.getElementById("customizedRange");
	var a = document.getElementById("currentMonth");
	var b = document.getElementById("previousMonth");
	var c = document.getElementById("currentYear");
	
	if (x.style.display == "none" || x.style.display == "")
	{
		x.style.display = "block";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
    }
	else
	{
		x.style.display = "none";
		a.style.display = "none";
		b.style.display = "none";
		c.style.display = "none";
	}	
}

function resetFunction()
{
	document.getElementById("modalForm").reset();
}

function testResults()
{
    var data1 = document.getElementById("date1").value;
	//window.location.href = "przegladajbilans4.php";
	//if (Date.parse("data1")) document.getElementById("wynik").innerHTML="To jest prawidłowa data!";
	//else document.getElementById("wynik").innerHTML="To nie jest prawidłowa data!";
    //document.write(TestVar);
	//alert ("You typed: " + TestVar);
}

// parse a date in yyyy-mm-dd format
function parseDate(input) {

  let parts = input.split('-');

  // new Date(year, month [, day [, hours[, minutes[, seconds[, ms]]]]])
  return new Date(parts[0], parts[1]-1, parts[2]); // Note: months are 0-based
}