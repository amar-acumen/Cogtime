<div id="header">
		<div id="logo">
			<h1><a href="<?=base_url()?>language/language_home">Multilanguage Home  </a></h1>
			<p> make your translation here</p>
		</div>
	</div>

	<div id="menu">
		<ul>
			<li <?=($page=='index')?'class="current_page_item"':''?>> <a href="<?=base_url()?>language/language_home">Home</a> </li>
			<li <?=($page=='scan')?'class="current_page_item"':''?>><a href="<?=base_url()?>language/language_home/scan">Backup and Scan</a></li>
			<li <?=($page=='translations')?'class="current_page_item"':''?>><a href="<?=base_url()?>language/language_home/translations">Translations</a></li>
		</ul>
	</div> 
