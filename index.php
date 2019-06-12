<?php

	require_once('./env.php');

	$uri = urldecode(
		parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
	);

	if (str_replace('/modules/love/', '', $uri) !== KEY) die('Invalid module key'); 

	$visitor = $_GET["_idMember"] ?? null;
	$owner = $_GET["_idProfile"] ?? null;

	/**
	 * Just kill it all here if either ID above is unavailable.
	 * An example of this happening is if a guest (not logged-in) user views a profile
	 * At the moment, there is no guest view of the module, so let's just not show anything.
	 */
	if (
		$visitor === null ||
		$visitor === '' ||
		$owner === null ||
		$owner === ''
	) die('<!--	No valid IDs available for module to function.	-->');
	
	require_once('./vendor/autoload.php');
	require_once('./inc/helpers.php');
	require_once('./inc/love_calculator.php');

	(int) $love_num = love($owner, $visitor);

?>
<style><?=file_get_contents("animations.css").file_get_contents("styles.css");?></style>
<script type="text/javascript">
	$(document).ready(function() {
		/**
		 * For some reason, third party modules have a hardcoded font size value set that official modules don't have.
		 * This line prevents that. Tom, please fix!
		 */
		document.getElementById('tpm__sLoveCalculator').style = null;
	});
</script>
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
		<?php if(is_owner()): ?>
		<div style="text-align:center;">
			<a href="javascript:;" onclick="$('#lc-members').fadeToggle()">Toggle Your Scores</a>
		</div>
		<div id="lc-members" style="display:none;">
			<?=members_list($owner);?>
		</div>
		<?php endif; ?>
	</div>
</module>