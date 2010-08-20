<h2>Game Board</h2>

<p class="message">The game board will be displayed here</p>

<table class="board">
	 <?php for ($y = 1; $y <= 10; $y++): ?>
	 <tr>
	 <?php for ($x = 1; $x <= 10; $x++): ?>
   <td>O</td>
	 <?php endfor; ?>
	 </tr>
	 <?php endfor; ?>
</table>