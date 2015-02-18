<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?=base_url()?>" />
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Multilanguage Home</title>
<link href="<?=base_url()?>TMX/css/language_style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div id="wrapper">
	<?php
	include('header.php');
	?>

	<div id="page">
	<div id="page-bgtop">
	<div id="page-bgbtm">
		<div id="content">
			<div class="post">
				<h2 class="title"><a href="#">Create date-wise backup and scan for new pages here</a></h2>
				
				<div style="width:31%;float:left;text-align:center">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="backup" value="Backup XML" onclick="javascript:window.location='<?=base_url()?>language/language_home/backup_this'"/>
				</div>
				
				<div style="width:31%;float:right;text-align:center">
					<input type="button" name="scan" onclick="javascript:window.location='<?=base_url()?>language/language_home/delete_scan'" value="Delete All and Scan" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</div>

				<div style="width:31%;float:right;text-align:center">
					<input type="button" name="scan" onclick="javascript:window.location='<?=base_url()?>language/language_home/scan_this'" value="Scan for new pages" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</div>

				<div style="clear:both;height:100px;"></div>


				<div class="entry" style="">
					<p>
						N.B. Backup will be created with a suffix of date.
					</p>
				</div>
			</div>
			
			
		<div style="clear: both;">&nbsp;</div>
		</div>
		
		<?php
			include('left_panel.php');
		?>

		<div style="clear: both;">&nbsp;</div>
	</div>
	</div>
	</div>
	<!-- end #page -->
</div>
	<?php
	include('footer.php');
	?>
</body>
</html>
