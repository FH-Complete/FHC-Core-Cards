<?php
$this->load->view(
	'templates/FHC-Header',
	array(
		'title' => 'Card-Terminal',
		'jquery3' => true,
		'jqueryui1' => true,
		'bootstrap3' => true,
		'fontawesome4' => true,
		'sbadmintemplate3' => true,
		'navigationwidget' => true,
		'ajaxlib' => true,
		'dialoglib' => true,
		'jquerycheckboxes1' => true,
		'tablesorter2' => true,
		'phrases' => array(
		),
		'customJSs' =>
			array(
				'public/extensions/FHC-Core-Cards/js/cardsCreation.js',
				'public/extensions/FHC-Core-Cards/js/terminalOverview.js',
				'public/js/tablesort/tablesort.js'
			),
	)
);
?>
<body>
	<div id="wrapper">

		<?php echo $this->widgetlib->widget('NavigationWidget'); ?>

		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h3 class="page-header">
							Terminal Overview
						</h3>
					</div>
				</div>
				<div>
					<?php $this->load->view('extensions/FHC-Core-Cards/cis/terminalOverviewData.php'); ?>
					<?php $this->load->view('extensions/FHC-Core-Cards/cis/terminalCreation.php'); ?>

				</div>
			</div>
		</div>
	</div>
</body>

<?php $this->load->view('templates/FHC-Footer'); ?>
