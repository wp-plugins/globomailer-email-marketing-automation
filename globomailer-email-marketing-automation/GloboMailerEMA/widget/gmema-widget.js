function gmema_submit_page(url)
{
	gmema_email = document.getElementById("gmema_txt_email");
	gmema_name_first = document.getElementById("gmema_txt_first_name");
	gmema_name_last = document.getElementById("gmema_txt_last_name");
	gmema_group = document.getElementById("gmema_txt_group");
    if( gmema_email.value == "" )
    {
        alert("Please enter email address.");
        gmema_email.focus();
        return false;    
    }
	if( gmema_email.value!="" && ( gmema_email.value.indexOf("@",0) == -1 || gmema_email.value.indexOf(".",0) == -1 ))
    {
        alert("Please provide a valid email address.")
        gmema_email.focus();
        gmema_email.select();
        return false;
    }
	document.getElementById("gmema_msg").innerHTML = "loading...";
	var date_now = "";
    var mynumber = Math.random();
	var str= "gmema_email="+ encodeURI(gmema_email.value) + "&gmema_name_first=" + encodeURI(gmema_name_first.value) + "&gmema_name_last=" + encodeURI(gmema_name_last.value) + "&gmema_group=" + encodeURI(gmema_group.value) + "&timestamp=" + encodeURI(date_now) + "&action=" + encodeURI(mynumber);
	gmema_submit_request(url+'/?gmema=subscribe', str);
}

var http_req = false;
function gmema_submit_request(url, parameters) 
{
	http_req = false;
	if (window.XMLHttpRequest) 
	{
		http_req = new XMLHttpRequest();
		if (http_req.overrideMimeType) 
		{
			http_req.overrideMimeType('text/html');
		}
	} 
	else if (window.ActiveXObject) 
	{
		try 
		{
			http_req = new ActiveXObject("Msxml2.XMLHTTP");
		} 
		catch (e) 
		{
			try 
			{
				http_req = new ActiveXObject("Microsoft.XMLHTTP");
			} 
			catch (e) 
			{
				
			}
		}
	}
	if (!http_req) 
	{
		alert('Cannot create XMLHTTP instance');
		return false;
	}
	http_req.onreadystatechange = eemail_submitresult;
	http_req.open('POST', url, true);
	http_req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_req.setRequestHeader("Content-length", parameters.length);
	http_req.setRequestHeader("Connection", "close");
	http_req.send(parameters);
}

function eemail_submitresult() 
{
	if (http_req.readyState == 4) 
	{
		if (http_req.status == 200) 
		{
		 	if (http_req.readyState==4 || http_req.readyState=="complete")
			{ 
				var response = JSON.parse(http_req.responseText);
				if(response['status'] == "success")
				{
					document.getElementById("gmema_msg").innerHTML = response['message'];
					document.getElementById("gmema_txt_email").value="";
					document.getElementById("gmema_txt_first_name").value="";
					document.getElementById("gmema_txt_last_name").value="";
				}
				else if(response['status'] == "error")
				{
					document.getElementById("gmema_msg").innerHTML = response['message'];
					document.getElementById("gmema_txt_email").value="";
					document.getElementById("gmema_txt_first_name").value="";
					document.getElementById("gmema_txt_last_name").value="";
				}
			} 
		}
		else 
		{
			alert('There was a problem with the request.');
		}
	}
}