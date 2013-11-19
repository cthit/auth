<!DOCTYPE html>
<html>
<head>
	<title>Skapa användare</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>
	<div id="main">
		<header>
			<h1>Skapa användare</h1>
		</header>
		<div id="content">
			<aside class="half">
				<p>Har du ingen användare på chalmers.it än?</p>
				<p>Fyll i dina uppgifter i tabellen till höger för att skapa en.
				Med en användarprofil på chalmers.it kan du ta del av de tjänster som vi erbjuder.</p>
			</aside>
			<article class="half">
				<form method="post" action="createUser.php?redirect_to=<?=urlencode($redirect)?>">
				<table>
					<tr><td><label>CID:</label></td><td><input name="username" type="text" value="<?=$username?>"></td></tr>
					<tr><td><label>Chalmerslösenord:</label></td><td><input name="password" type="password" placeholder="Ditt chalmerslösenord" value="<?=$password?>"></td></tr>
					<tr><td><label>Nick:</label></td><td><input name="nick" type="text"></td></tr>
					<tr><td><label>Mail:</label></td><td><input name="email" type="email" value="<?=$email?>"></td></tr> 
					<tr><td><label>Välj nytt lösenord:</label></td><td><input name="new_password" type="password"></td></tr>
					<tr><td></td><td><input class="button" type="submit" value="Skapa användare"></td></tr>
				</table>
				</form>
				<img src="digit2.png" alt="digITsmurfen">
			</article>
		</div>
	</div>
</body>
</html>

