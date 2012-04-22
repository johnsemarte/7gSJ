<!-- The content for the new external user dialog -->
<form class="newExternal" onsubmit="return false;">
<label for="name">Navn på bedriften</label><input type="text" name="name" title="Navn på bedriften som vil stå som oppdragsgiver for bacheloroppgaven(e)."><br/>
<label>Kontaktperson : </label><br clear="both"/>
<label for="givenname">Fornavn</label><input class="short" type="text" name="givenname" title="Fornavn på kontaktperson i bedriften.">
<label for="surename">Etternavn</label><input class="short" type="text" name="surename" title="Etternavn på kontaktperson i bedriften."><br clear="both"/>
<label for="email">Epost</label><input type="email" name="email" title="Epost adresse til kontaktperson, vil også være brukernavn ved pålogging."><br/>
<label for="password">Passord</label><input class="short" type="password" name="password" title="Passord for pålogging.">
<label for="password1">Bekreft passord</label><input class="short" type="password" name="password1" title="Bekreft passordet."/><br clear="both"/>
<label for="officephone">Telefon (jobb)</label><input class="short" type="text" name="officephone" title="Telefonnummer til kontaktperson på jobb.">
<label for="cellphone">Telefon (mobil)</label><input class="short" type="text" name="cellphone" title="Telefonnummer til kontaktperson, mobil."><br clear="both"/>
<label for="address1">Adresse</label><input type="text" name="address1" title="Adresse til bedriften."><br/>
<label for="address2">Adresse</label><input type="text" name="address2" title="Adresse til bedriften."><br/>
<label for="postal">Postnr/sted</label><input type="text" name="postal" title="Postnummer og sted for bedriften."><br/>
<label for="bedriftsbeskrivelse">Beskrivelse av bedriften</label><br clear="both"/>
<textarea class="tinymce" name="bedriftsbeskrivelse"></textarea>
<input type="button" value="Opprett ny bruker" onclick="javascript:createNewExternal(this.form)"/>
</form>