const BASE_URL = FHC_JS_DATA_STORAGE_OBJECT.app_root + FHC_JS_DATA_STORAGE_OBJECT.ci_router;
const CALLED_PATH = FHC_JS_DATA_STORAGE_OBJECT.called_path;
const CONTROLLER_URL = BASE_URL + "/"+CALLED_PATH;

const TABLE = '[tableuniqueid = terminalOverview] #tableWidgetTabulator';

function form_options(cell, formatterParams)
{
	var div = $("<div></div>");

	var saveTerminal = $("<button class='saveTerminal btn btn-default'>" +
							"<i class='fa fa-floppy-o fa-1x' aria-hidden='true'></i>"+
						"</button>");

	saveTerminal.on('click', function()
	{
		var data = {
			'id' : cell.getData().cardsterminal_id,
			'name' : cell.getData().name,
			'beschreibung' : cell.getData().beschreibung,
			'ort' : cell.getData().ort,
			'type' : cell.getData().type,
			'aktiv' : cell.getData().aktiv,
		}
		TerminalOverview.updateTerminal(data);
	});

	div.append(saveTerminal);

	var deleteTerminal = $("<button class='deleteTerminal btn btn-default'>" +
		"<i class='fa fa-trash fa-1x' aria-hidden='true'></i>"+
		"</button>");

	deleteTerminal.on('click', function()
	{
		var data = {
			'id' : cell.getData().cardsterminal_id,
		}
		TerminalOverview.deleteTerminal(data);
	});

	div.append(deleteTerminal);
	return div[0];
}

function form_type(cell, formatterParams)
{
	return cell.getData().type.charAt(0).toUpperCase() + cell.getData().type.slice(1);
}

function resortTable()
{
	$(TABLE).tabulator('setSort',
		[
			{column: 'aktiv', dir: 'desc'}
		]
	);
}

$(document).ready(function() {

	$('.hinzufuegen').click(function()
	{
		$('.terminalForm').slideToggle(500);
	});

	$('#addTerminal').click(function()
	{
		var name = $('#terminalName').val();
		var beschreibung = $('#terminalBeschreibung').val();
		var ort = $('#terminalOrt').val();
		var type = $('#terminalType').val();
		var aktiv = $('#terminalAktiv').prop('checked');

		if (name === '' || beschreibung === '' || ort === '' || type === '')
		{
			FHC_DialogLib.alertWarning('Bitte alle Felder ausfüllen!');
			return false;
		}

		var data = {
			name : name,
			beschreibung : beschreibung,
			ort : ort,
			type : type,
			aktiv : aktiv
		}
		TerminalOverview.addTerminal(data);
	})
});

var TerminalOverview = {

	addTerminal: function(data)
	{
		FHC_AjaxClient.ajaxCallPost(
			CALLED_PATH + "/addTerminal",
				data,
			{
				successCallback: function(data, textStatus, jqXHR)
				{
					if (FHC_AjaxClient.isError(data))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(response));
					}

					if (FHC_AjaxClient.isSuccess(data))
					{
						data = FHC_AjaxClient.getData(data);

						$(TABLE).tabulator(
							'addRow',
							JSON.stringify({
								cardsterminal_id: data.cardsterminal_id,
								name: data.name,
								beschreibung: data.beschreibung,
								aktiv: data.aktiv,
								ort: data.ort,
								type: data.type
							})
						);
						resortTable();
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	updateTerminal: function(data)
	{
		FHC_AjaxClient.ajaxCallPost(
			CALLED_PATH + "/updateTerminal",
				data,
			{
				successCallback: function(data, textStatus, jqXHR)
				{
					if (FHC_AjaxClient.isError(data))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(data));
					}

					if (FHC_AjaxClient.isSuccess(data))
					{
						resortTable();
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	deleteTerminal: function(data)
	{
		FHC_AjaxClient.ajaxCallPost(
			CALLED_PATH + "/deleteTerminal",
			data,
			{
				successCallback: function(data, textStatus, jqXHR) {
					if (FHC_AjaxClient.isError(data))
					{
						FHC_AjaxClient.hideVeil();
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(data))
					}

					if (FHC_AjaxClient.isSuccess(data))
					{
						data = FHC_AjaxClient.getData(data);
						$(TABLE).tabulator('deleteRow', data.cardsterminal_id);
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},
}

