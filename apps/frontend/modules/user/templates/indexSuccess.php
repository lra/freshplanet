<h2>Authentification</h2>

<h3>Login using an existing email</h3>

<form action="<?php echo url_for('user/login') ?>" method="post">
 <table>
  <?php echo $loginForm ?>
	<tr>
	 <td colspan="2">
	  <input type="submit" />
	 </td>
	</tr>
 </table>
</form>

<h4>or</h4>

<h3>Create a new user</h3>

<form action="<?php echo url_for('user/register') ?>" method="post">
 <table>
  <?php echo $registerForm ?>
	<tr>
	 <td colspan="2">
	  <input type="submit" />
	 </td>
	</tr>
 </table>
</form>
