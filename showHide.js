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