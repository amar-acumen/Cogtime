<div id="sidebar">
			<ul>
				
				<li>
					<h2>Pages</h2>
					<ul>
					<?php
					if(is_array($pages) && count($pages)) :
						$i = 1;
						foreach($pages as $page) :
							if(isset($translation_page) && $translation_page==$page) :
								$class = 'class="selected"';
							else:
								$class = '';
							endif;
					?>
						<li <?=$class?>> <?=$i++?>.&nbsp;&nbsp;<a href="<?=base_url().'language/language_home/translations/'.base64_encode($page)?>"><?=$page?></a></li>
					<?php
						endforeach;
					endif;
				
					?>
					</ul>
				</li>
				
			</ul>
		</div> 
