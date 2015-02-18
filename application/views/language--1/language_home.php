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
				<h2 class="title"><a>This is language home.</a></h2>
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
	
</div>
	<?php
	include('footer.php');
	?>
	
</body>
</html>
