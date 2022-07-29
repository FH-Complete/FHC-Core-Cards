<?php

$query = 'SELECT *
			FROM extension.tbl_cards_terminal
			';

$filterWidgetArray = array(
	'query' => $query,
	'app' => 'international',
	'tableUniqueId' => 'terminalOverview',
	'datasetName' => 'terminalOverview',
	'filter_id' => $this->input->get('filter_id'),
	'requiredPermissions' => 'admin',
	'datasetRepresentation' => 'tabulator',
	'additionalColumns' => array(
		'Options'
	),
	'columnsAliases' => array(
		'terminal',
		'Name',
		'Beschreibung',
		'Ort',
		'Aktiv',
		'Typ'
	),
	'datasetRepOptions' => '{
		index: "cardsterminal_id",
		layout: "fitColumns",
		persistantLayout: false,
		headerFilterPlaceholder: " ",
		tableWidgetHeader: false,
		columnVertAlign:"center",
		columnAlign:"center",
		fitColumns:true,
		selectable: false,
		groupClosedShowCalcs:true,
		selectableRangeMode: "click",
		selectablePersistence: false,
		initialSort: [{column: "aktiv", dir: "desc"}]
	}',
	'datasetRepFieldsDefs' => '{
		cardsterminal_id: {visible: false},
		insertvon: {visible: false},
		insertamum: {visible: false},
		updateamum: {visible: false},
		updatevon: {visible: false},
		aktiv: {formatter: "tickCross", editor:true, align:"center"},
		Options: {formatter: form_options},
		name: {editor: "input"},
		beschreibung: {editor: "input"},
		ort: {editor: "input"},
		type: {editor: "select", editorParams: {"student": "Student"}, formatter: form_type}
	}'
);

echo $this->widgetlib->widget('TableWidget', $filterWidgetArray);
?>
