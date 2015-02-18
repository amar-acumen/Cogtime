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
				<h2 class="title"><a href="<?=base_url().'language/language_home/translations/'.base64_encode($translation_page)?>">Translation for the page <?=$translation_page?></a></h2>
				
				
				<p>
				<?php
				if( is_array($languages) && count($languages) ) :
				?>
				<form method="post" action="">
				<table class="translations" cellpadding="5" cellspacing="1">
				<tbody>
				<tr class="top">
				<?php
					foreach($languages as $language) :
				?>
					<th><?=$language?></th>
				<?php
					endforeach;
				?>
				</tr>

				<?php
					$i = 0;
					foreach($translations as $id=>$translation) :
				?>
					<tr>
						<?php
							foreach($languages as $key=>$language) :
						?>
							<td>
							<textarea name="text_<?=$i.'_'.$key?>" rows="3" style="background-color:#fbfbfb;border:5px; solid #fbfbfb;width:100%"><?=(isset($translation[$key]))?$translation[$key]:''?></textarea>
							</td>
						<?php
							endforeach;
						?>
					</tr>
						<input type="hidden" name="tuid_<?=$i?>" value="<?=base64_encode($id)?>" />
				<?php
						$i++;
					endforeach;
				?>
					<tr>
						<td colspan="<?=count($languages)?>" style="height:30px;;">
						<input type="hidden" name="counter" value="<?=$i?>" />
						<input name="submit_translations" type="submit" value="Submit" />
						</td>
					</tr>
				</tbody>
				</table>
				</form>
				<?php
				endif;
				?>
				</p>
				

			</div>
			
			
		<div style="clear: both;">&nbsp;</div>
		</div>

		<?php
			include('left_panel.php');
		?>

		<div style="clear:both;">&nbsp;</div>
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
