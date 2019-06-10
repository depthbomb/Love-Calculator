<?php

	$visitor = $_GET["_idMember"] ?? null;
	$owner = $_GET["_idProfile"] ?? null;

	if ($visitor ===  null || $owner === null) die();
	
	require_once('./vendor/autoload.php');
	require_once('./inc/helpers.php');
	require_once('./inc/love_calculator.php');

	(int) $love_num = love($owner, $visitor);

?><style><?=file_get_contents("animations.css").file_get_contents("styles.css");?></style>
<script>$(document).ready(function() {document.getElementById('tpm__sLoveCalculator').style = null;})</script>
<module id="love-calculator" class="PageModule SingleRowProfileModule MemberProfileModule">
	<h3>
		<a href="http://gamebanana.com/modules/519" target="_blank">Love 💕 Calculator</a>
	</h3>
	<div class="Content">
		<div class="LoveMeterContainer">
			<div class="LoveContestants">
				<?=headline($owner, $visitor);?>
			</div>
			<progress class="LoveMeter" value="<?=$love_num;?>" max="<?=$love_num > 100 ? 101 : 100;?>" data-label="<?=$love_num;?>%">
				<?=$love_num;?>%
			</progress>
		</div>
		<q class="LoveStatus"><?=(is_owner() ? love_message_self($love_num) : love_message($love_num));?></q>
	</div>
</module>