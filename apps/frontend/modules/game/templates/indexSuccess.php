<h2>Game Board</h2>

<table class="board">
	<?php for ($y = 1; $y <= 10; $y++): ?>
	<tr>
		<?php for ($x = 1; $x <= 10; $x++): ?>
		<td>
			<a href="">
				<img src="images/up.png" />
			</a>
		</td>
		<?php endfor; ?>
	</tr>
	<?php endfor; ?>
</table>