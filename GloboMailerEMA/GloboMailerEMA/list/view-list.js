function _gmema_addemail()
{
	if(document.form_addemail.gmema_g_name.value=="")
	{
		alert("Please enter list name.")
		document.form_addemail.gmema_g_name.focus();
		return false;
	}
	else if(document.form_addemail.gmema_g_desc.value=="")
	{
		alert("Please enter list description.")
		document.form_addemail.gmema_g_desc.focus();
		return false;
	}
	else if(document.form_addemail.gmema_d_fname.value == "")
	{
		alert("Please enter from name.")
		document.form_addemail.gmema_d_fname.focus();
		return false;
	}
	else if(document.form_addemail.gmema_d_femail.value == "")
	{
		alert("Please enter from email.")
		document.form_addemail.gmema_d_femail.focus();
		return false;
	}
	else if( document.form_addemail.gmema_d_replyto.value == "")
	{
		alert("Please enter reply to email.")
		document.form_addemail.gmema_d_replyto.focus();
		return false;
	}
	else if( document.form_addemail.gmema_c_name.value == "")
	{
		alert("Please enter comapny name.")
		document.form_addemail.gmema_c_name.focus();
		return false;
	}
	else if( document.form_addemail.gmema_c_country.value == "")
	{
		alert("Please enter country name.")
		document.form_addemail.gmema_c_country.focus();
		return false;
	}
	else if(document.form_addemail.gmema_c_add1.value == "")
	{
		alert("Please enter Address.")
		document.form_addemail.gmema_c_add1.focus();
		return false;
	}
	else if(document.form_addemail.gmema_c_city.value == "")
	{
		alert("Please enter city.")
		document.form_addemail.gmema_c_city.focus();
		return false;
	}
	else if(document.form_addemail.gmema_c_zip.value == "")
	{
		alert("Please enter zip code.")
		document.form_addemail.gmema_c_zip.focus();
		return false;
	}
}

function _gmema_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.frm_gmema_display.action="admin.php?page=gmema-view-list&ac=del&lid="+id;
		document.frm_gmema_display.submit();
	}
}

function _gmema_redirect()
{
	window.location = "admin.php?page=gmema-view-list";
}