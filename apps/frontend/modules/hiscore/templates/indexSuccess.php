<h2>Hiscores</h2>

<table class="hiscores">
 <tr>
  <th>Name</th>
  <th>Board size</th>
  <th>Time spent</th>
 </tr>
 <?php foreach($hiscores as $hiscore): ?>
 <?php if ($hiscore->getUser()->getId() === $sf_user->getAttribute('id')): ?>
 <tr class="bold">
 <?php else: ?>
 <tr>
 <?php endif; ?>
  <td class="right"><?php echo $hiscore->getUser()->getName(); ?></td>
  <td class="right"><?php echo pow($hiscore->getBoardwidth(), 2); ?></td>
  <td class="right"><?php echo $hiscore->getTime(); ?></td>
 </tr>
 <?php endforeach; ?>
</table>
