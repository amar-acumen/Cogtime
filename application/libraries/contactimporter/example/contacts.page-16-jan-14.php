<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Contacts Importer by Svetlozar.NET (PHP)</title>
<LINK href="style.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
	var form_ready = <?php echo (isset($this->current_class) && $this->current_class->ExternalAuth) ? "false" : "true"; ?>;

	function ImportContacts(state, contacts_option)
	{
	    var import_form = document.getElementById("import_form");
		if (import_form)
		{
			if (state && ("state" in import_form))
			{
				import_form["state"].value = state;
			}

			if (contacts_option && ("contacts_option" in import_form))
			{
				import_form["contacts_option"].value = contacts_option;
			}

			import_form.submit();
		}
	}
</script>
</head>
<body>
<div class="content">
<div id="<?php echo $this->display_menu ? "importform" : "inviteform"; ?>" class="center">
<h1>Address Book<br />
Importer Demo</h1>
<h3 class="center"><?php echo isset($this->current_class) ? "{$this->current_class->ClassName} Contacts Importer" : ""; ?>
</h3>
<br />
<?php require_once $this->include_form; ?></div>
<?php if ($this->display_menu) {?>
<br/>
<form id="changeoption">
<input type="hidden" name="contacts_option" id="contacts_option" value="">
<a href="javascript:void(0)" onClick="$('#contacts_option').val('Gmail');$('#changeoption').submit();">Gmail</a>
<a href="javascript:void(0)" onClick="$('#contacts_option').val('Yahoo');$('#changeoption').submit();">Yahoo</a>
</form>
<?php /*?><ul id="menu">
<?php foreach ($this->contacts_classes as $k => $v) {?>
	<li><a href="<?php echo "{$this->base_url}contacts_option=$k"; ?>"><?php echo $v->ClassName . ($v->ExternalAuth ? "<br/>(External Authentication)" : ""); ?></a></li>
    <li><a href="<?php echo base_url()."find-friends/$k"; ?>"><?php echo $v->ClassName . ($v->ExternalAuth ? "<br/>(External Authentication)" : ""); ?></a></li>
	<?php } ?>
</ul><?php */?>
	<?php } ?>
<div class="clear">&nbsp;</div>
</div>

<div>
<textarea id="mailids"></textarea>
</div>
<script type="text/javascript">
	try
	{
	    var auth_window = window.ExternalAuthentication;
	}
	catch(ignore)
	{
	    var auth_window = undefined;
	}

    (function(){
		var external_auth = "external.php?contacts_option=<?php echo $selected_option; ?>";
        submit_form = function(form){
            return function(){
                if (form_ready){
                    form.submit();
                    return true;
				}
				else if (external_auth != "") {
					if (auth_window)
						auth_window.close();
                    auth_window = window.open(external_auth, "ExternalAuthentication", "top=200,left=200,width=500,height=400,location=yes,status=yes,resizable=yes", true);
                    auth_window.focus();
                    return false;
				}
            }
        }

        toggle_checked = function(check){
            return function(){
                check.checked = !check.checked;
            }
        }

        cancel_propagation = function(e){
            if (!e) {
                var e = window.event;
            }

            e.cancelBubble = true;

            if (e.stopPropagation)
                e.stopPropagation();
        }

        set_checked = function(checkboxes){
            return function(){
                for (i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            }
        }

        var invite_form = document.getElementById("invite_form");
		if (invite_form) {
			var table_rows = invite_form.getElementsByTagName("tr");
			var form_contacts = [];
			for (var i = 1; i < table_rows.length; i++) {
				var input = table_rows[i].getElementsByTagName("input");
				if (!input || input[0].type != "checkbox")
					continue;
				input[0].onclick = cancel_propagation;
				table_rows[i].onclick = toggle_checked(input[0]);
				form_contacts.push(input[0]);
			}

			var toggleAll = document.getElementById("ToggleSelectedAll");
			toggleAll.onclick = set_checked(form_contacts);
		}
		var import_form = document.getElementById("import_form");
		if (import_form)
		{
			import_form.onsubmit = submit_form(import_form);
		}
    })();
function showInTextarea()
{
	var mail_ids = '';
	 $(':checkbox:checked').each(function(i){
	  //alert($(this).attr('id'));
	   mail_ids += $(this).val()+',';
	 });
	 $('#mailids').val(mail_ids);
}
</script>
</body>
</html>