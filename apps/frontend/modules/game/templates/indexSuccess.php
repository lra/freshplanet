<?php use_javascript('gameboard.js') ?>

<h2>Game Board</h2>

<table class="board">
	<?php for ($y = 0; $y < $board->getWidth(); $y++): ?>
	<tr>
		<?php for ($x = 0; $x < $board->getWidth(); $x++): ?>
		<td>
			<div id="tile_<?php echo($x+$y*$board->getWidth()) ?>">
				<a href="">
					<img src="/images/up.png" />
				</a>
			</div>
		</td>
		<?php endfor; ?>
	</tr>
	<?php endfor; ?>
</table>

<p class="legend" id="loading"><img src="/images/ajax-loader.gif" /><br />Loading...</p>

<p class="legend">Board size: <?php echo $board->getSize(); ?></p>
