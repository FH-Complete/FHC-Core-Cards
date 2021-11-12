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
		'customJSs' => array('public/extensions/FHC-Core-Cards/js/cardsCreation.js')
	)
);
?>
<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header">QR Code erstellen</h3>
		</div>
	</div>

	<div class="input-group">
		<span class="input-group-btn">
				<input type="submit" value="QR Code erstellen" id="qrCreation" class="btn btn-default"/>
		</span>
	</div>

	<div class="infos input-group hidden">
		<div id="qrCode"></div>
		<div id="pinCode" class="alert-success"></div>

		<a href="<?php echo site_url('extensions/FHC-Core-Cards/cis/Cards/downloadQRCode'); ?>">
			<input type="button" class="btn btn-default" value="QR Code runterladen"/>
		</a>

	</div>

</div>
</body>


