	<form id="import_form" class="center" method="post">
	<?php if (!$this->current_class->ExternalAuth) {?>
            
            <div class="lable02">Email:</div>
            <div class="field01"><input type="text" name="email" value="" /></div>
            
            <div class="lable02">Password:</div>
            <div class="field01"><input type="password" name="pswd" value=""  /></div>
            
		
		<?php if ($this->captcha_required && $this->captcha_url) {
				echo "<img src='{$this->captcha_url}'/>"; ?><br/>
				Enter text: <input type="text" name="captcha" value=""/>
		<?php }	?>
	<?php } ?>
		<input type="hidden" name="state" value=""/>
		<input type="hidden" name="contacts_option" value="<?php echo $selected_option; ?>"/>
	<?php if ($this->error_returned && $this->error_message) {?>
                <br class="clear"/>
                <div class="lable02">&nbsp;</div>
                <div class="field01"><span style="color:red;"><?php echo $this->error_message; ?></span></div>
                <br class="clear"/>
	<?php } ?>
                <div class="lable02">&nbsp;</div>
                <button class="mail-request-btn" type="submit" id="btnContactsForm" value="import"><?php echo $this->current_class->ExternalAuth? "Authorize Externally" : "Import Contacts"; ?></button>
	</form>
