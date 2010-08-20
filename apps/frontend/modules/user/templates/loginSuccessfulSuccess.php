<h1>Your account</h1>

<div class="message">
	 <p>Welcome <?php echo $dbUser->getFirstname(); ?>, you've successfully logged in the game =)</p>

   <ul>
     <li>Firstname: <?php echo $dbUser->getFirstname(); ?></li>
     <li>Lastname: <?php echo $dbUser->getLastname(); ?></li>
     <li>Email: <a href="mailto:<?php echo $dbUser->getEmail(); ?>"><?php echo $dbUser->getEmail(); ?></a></li>
   </ul>

   <p>You can now start playing a game or continue to play your last gameboard.</p>
</div>
