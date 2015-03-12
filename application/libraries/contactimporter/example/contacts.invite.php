	<form id="invite_form" class="center" method="post">
    <div class="email-list-heading">
    <ul >
    	<li>
        	<input type="checkbox" class="check-email" id="ToggleSelectedAll" checked="checked" title="Toggle Selected" />
             <span class="Names" id="NameColumn">Name</span>
              <span class="email-Names" id="EmailColumn">Email</span>
              <br class="clear"/>
        </li>
       
    </ul>
    </div>
    <div class="email-overflow">
    <ul id="mailidUl">
    
    <?php foreach($this->contacts as $contact) {?>
    	<li>
        	<input type="checkbox" class="check-email" name="contacts[<?php echo $contact->name; ?>]" value="<?php echo $contact->email; ?>" checked="checked" />
				<span class="Names"><?php echo $contact->name; ?></span>
				<span class="email-Names"><?php echo $contact->email; ?></span>
                <br class="clear"/>
        </li>
        <?php } ?>
    </ul>
    </div>
			
	<div class="btnContacts-section">
		<button type="button" class="mail-request-btn " id="btnContactsForm" value="invite" onclick="showInTextarea();">Send Mail</button>
        </div>
        
	</form>
