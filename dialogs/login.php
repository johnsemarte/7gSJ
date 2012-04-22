<!-- The content of the login form -->
<form class="login" onsubmit="return false;">
<label for="username">Brukernavn</label><input type="text" name="username" title="For studenter og ansatte ved HiG, bruk ditt påloggingsnavn ved HiG.\nFor eksterne, bruk den e-post adressen du brukte når du registrerte konto på systemet."><br/>
<label for="password">Passord</label><input type="password" name="password" title="For studenter og ansatte ved HiG, bruk ditt passord for pålogging ved HiG.\nFor eksterne, bruk det passordet du oppga når du registrerte konto på systemet."><br/>
<div class="error">Feil brukernavn/passord, prøv igjen</div>
<input type="button" value="Logg på" onclick="javascript:login(this.form)"/>
</form>