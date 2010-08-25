<?php use_javascript('gameboard.js') ?>

<h2>Game Board</h2>

<table class="board">
	<?php for ($y = 0; $y < $board->getWidth(); $y++): ?>
	<tr>
		<?php for ($x = 0; $x < $board->getWidth(); $x++): ?>
		<td>
			<img id="tile_<?php echo($x+$y*$board->getWidth()) ?>" src="/images/up.png" />
		</td>
		<?php endfor; ?>
	</tr>
	<?php endfor; ?>
</table>

<ul class="message">
 <li>Press F to flag or unflag tiles: <strong id="flag_on">On</strong><span id="flag_off">Off</span></li>
 <li>Press Q to put or remove question marks on tiles: <strong id="question_on">On</strong><span id="question_off">Off</span></li>
</ul>

<p class="legend" id="loading"><img src="/images/ajax-loader.gif" /><br />Loading...</p>
