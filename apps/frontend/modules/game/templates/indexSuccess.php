<h2>Game Board</h2>

<table class="board">
 <?php for ($y = 0; $y < $board->getWidth(); $y++): ?>
	<tr>
	 <?php for ($x = 0; $x < $board->getWidth(); $x++): ?>
		<td>
	 <?php if (!$board->getTile($x + $y * $x)->isMined()): ?>
			<a href="">
				<img src="/images/empty.png" />
			</a>
	 <?php else: ?>
				<img src="/images/bomb.png" />
	 <?php endif; ?>
		</td>
		<?php endfor; ?>
	</tr>
	<?php endfor; ?>
</table>

<p class="legend">Board size: <?php echo $board->getSize(); ?></p>
