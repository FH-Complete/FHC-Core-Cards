<?php
$sitesettings = array(
	'title' => 'Cards',
	'jquery3' => true,
	'jqueryui1' => true,
	'bootstrap3' => true,
	'fontawesome4' => true,
	'sbadmintemplate3' => true,
	'ajaxlib' => true,
	'dialoglib' => true,
	'customJSs' => array('public/extensions/FHC-Core-Cards/js/cards.js')
);
$this->load->view(
	'templates/FHC-Header',
	$sitesettings
);
?>

	<div class="container-fluid">
		<div class="row ">
			<div class="col-lg-12">
				<h3 class="page-header">Kartenverlängerung prüfen</h3>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-4">
				<input class="form-control" type="text" id="cardIdentifier" placeholder="Kartennummer" />
			</div>
			<div class="col-xs-4">
				<button id="validation" class="btn btn-default">Validieren</button>
			</div>
		</div>
		<div class="row">
			<div id="validationOutput" class="col-xs-12">
			</div>
		</div>
		<hr />
		<div class="row">
			<div class="col-lg-12">
				<h3 class="page-header">Zutrittskarte sperren</h3>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-4">
				<input class="form-control" type="text" id="searchstudent" placeholder="Vorname/Nachname/UID"/>
			</div>
			<div class="col-xs-4">
				<button id="showing" class="btn btn-default" disabled="disabled">Anzeigen</button>
				<input type="hidden" class="hidden" id="uid"/>
			</div>
		</div>
		<div class="row" id="cardOutput"></div>
	</div>
<?php
$this->load->view(
	'templates/FHC-Footer',
	$sitesettings
);
