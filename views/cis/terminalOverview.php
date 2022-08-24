<?php
$this->load->view(
	'templates/FHC-Header',
	array(
		'title' => 'Card-Terminal',
		'jquery' => true,
		'jqueryui' => true,
		'bootstrap' => true,
		'fontawesome' => true,
		'tablewidget' => true,
		'tabulator' => true,
		'ajaxlib' => true,
		'dialoglib' => true,
		'phrases' => array(
			'ui' => array(
				'global'
			)
		),
		'customJSs' => array(
			'public/extensions/FHC-Core-Cards/js/cardsCreation.js',
			'public/extensions/FHC-Core-Cards/js/terminalOverview.js',
			'public/js/tablesort/tablesort.js',
			'public/js/bootstrapper.js',
		)
	)
);
?>
<body>
	<div id="wrapper">
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h3 class="page-header">
							Overview - Terminal
						</h3>
					</div>
				</div>
				<div>
					<?php $this->load->view('extensions/FHC-Core-Cards/cis/terminalOverviewData.php'); ?>
				</div>
				<div>
					<?php $this->load->view('extensions/FHC-Core-Cards/cis/terminalCreation.php'); ?>
				</div>
			</div>
		</div>
	</div>
</body>

<?php $this->load->view('templates/FHC-Footer'); ?>
