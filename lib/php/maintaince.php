<?php

if ( $maintance ) {
	$hC = time();
	$hB = $maintance;
	$hE = $maintance + $duration;


	$past = $hE - $hC;
	$total = $duration;
	
	$diff = $past / $total * 100;
	$diff = 100 - $diff;
	
	if ( $diff > 100 ) $diff = 100;
	
	$page_title = "Maintaince";
	$type = "home";
	$content = 
		'<p>
			We are currently in maintance mode. Stay tuned! We will be back soon.</p>
			<div class="progress progress-striped active progress-warning">
				<div class="bar" style="width: '.round($diff).'%;">'.round($diff).'%</div>
			</div>';
	if ( isset($duration) ) $content .= 'Maintaince should be finished at '.date("d-m H:i:s", $hE).'.';
}
else header("Location: /home");

?>