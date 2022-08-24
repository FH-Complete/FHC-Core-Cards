<?php
	$this->load->view(
		'templates/FHC-Header',
		array(
			'title' => 'Cards',
			'jquery' => true,
			'jqueryui' => true,
			'bootstrap' => true,
			'fontawesome' => true,
			'sbadmintemplate' => true,
			'ajaxlib' => true,
			'dialoglib' => true,
			'customJSs' => array('public/extensions/FHC-Core-Cards/js/cards.js')
		)
	);
?>

<body>
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
				<input type="hidden" class="hidden" id="personid"/>
			</div>
		</div>
		<div class="row" id="cardOutput"></div>
	</div>
</body>


