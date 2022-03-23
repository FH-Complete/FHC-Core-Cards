<div class="row">
	<div class="col-lg-12">
		<button id="newTerminal" class="btn btn-default">Terminal hinzufügen</button>
	</div>
</div>
<br />
<form class="row terminalForm hidden">
	<div class="form-group row">
		<label for="terminalName" class="col-sm-2 col-form-label">Name</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="terminalName">
		</div>
	</div>

	<div class="form-group row">
		<label for="terminalBeschreibung" class="col-sm-2 col-form-label">Beschreibung</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="terminalBeschreibung">
		</div>
	</div>

	<div class="form-group row">
		<label for="terminalOrt" class="col-sm-2 col-form-label">Ort</label>
		<div class="col-sm-3">
			<input type="text" class="form-control" id="terminalOrt">
		</div>
	</div>

	<div class="form-group row">
		<label for="terminalType" class="col-sm-2 col-form-label">Typ</label>
		<div class="col-sm-3">
			<select id="terminalType" class="form-control">
				<option value="student">Student</option>
				<option value="mitarbeiter">Mitarbeiter</option>
			</select>
		</div>
	</div>

	<div class="form-group row">
		<label for="terminalAktiv" class="col-sm-2 col-form-label">Aktiv</label>
		<div class="col-sm-3">
			<input type="checkbox" class="checkbox" id="terminalAktiv">
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-2">
			<input class="form-control" type="button" value="Speichern" id="addTerminal" />
		</div>
		<div class="col-sm-2">
			<input class="form-control" type="button" value="Abbrechen" id="cancelTerminal" />
		</div>
	</div>
</form>