const BASE_URL = FHC_JS_DATA_STORAGE_OBJECT.app_root + FHC_JS_DATA_STORAGE_OBJECT.ci_router;
const CALLED_PATH = FHC_JS_DATA_STORAGE_OBJECT.called_path;
const CONTROLLER_URL = BASE_URL + "/"+CALLED_PATH;
var Cards = {

	validation: function(cardIdentifier)
	{
		FHC_AjaxClient.ajaxCallPost(
			"extensions/FHC-Core-Cards/cis/Cards/getValidationData",
			{
				cardIdentifier: cardIdentifier
			},
			{
				successCallback: function(response, textStatus, jqXHR) {
					if (FHC_AjaxClient.isError(response))
						Cards._writeError(FHC_AjaxClient.getError(response));
					else
						Cards._writeSuccess('Karte gültig bis ' + FHC_AjaxClient.getData(response))
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	getFromPerson: function(data = null)
	{
		FHC_AjaxClient.ajaxCallPost(
			CALLED_PATH + "/getCards",
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
						var cards = FHC_AjaxClient.getData(data);
						$('#cardOutput div').remove();
						cards.forEach(function(card)
						{
							Cards._addCard(card);
						});

						Cards.appendTableActionsHtml();
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	locking: function(data)
	{
		var id = data.betriebsmittelid;
		FHC_AjaxClient.ajaxCallPost(
			CALLED_PATH + "/cardLocking",
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
						FHC_DialogLib.alertSuccess("Erfolgreich gesperrt!");
						$('#anmerkung_' + id).prop('disabled', true);
						$('#sperren_' + id).prop('disabled', true);
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	_writeSuccess: function(text)
	{
		Cards._writeOutput(text, 'alert-success');
	},

	_writeError: function(text)
	{
		Cards._writeOutput(text, 'alert-warning');
	},

	_writeOutput: function(output, status)
	{
		$('#validationOutput div').remove();
		$("#validationOutput").append("<div class='alert " + status + "'>" + output + "</div>");
	},

	_addCard: function(card)
	{
		$("#cardOutput").append(
			"<div class='row col-xs-12 form-group'>" +
				"<div class='col-xs-4'>" +
					"<div class='alert alert-info'>" +
						"Ausgegeben am: "+ card.ausgegebenam +
					"</div>" +
				"</div>" +
				"<div class='col-xs-4'>" +
					"<textarea class='form-control' placeholder='Anmerkung' rows='1' id='anmerkung_"+ card.betriebsmittel_id + "'" + (card.retouram !== null ? "disabled" : "") +">" +
						(card.anmerkung !== null ? card.anmerkung : "") +
					"</textarea>" +
				"</div>" +
				"<div class='col-xs-4'>" +
					"<button class='btn btn-default sperren' id='sperren_"+ card.betriebsmittel_id + "'" + (card.retouram !== null ? "disabled" : "") + ">" +
						"<i class='fa fa-remove fa-1x' aria-hidden='true'></i>"+
					"</button>" +
				"</div>" +
			"</div>"
		);
	},

	_getIdFromElementID(elementid)
	{
		return elementid.substr(elementid.indexOf("_") + 1);
	},

	appendTableActionsHtml: function()
	{
		$('.sperren').click(function()
		{
			var id = Cards._getIdFromElementID(this.id);
			var anmerkung = $('#anmerkung_' + id).val();
			var uid = $('#uid').val();
			var data = {
				"betriebsmittelid": id,
				"anmerkung" : anmerkung,
				"uid" : uid
			};
			Cards.locking(data);
		});
	}
}

$(document).ready(function() {
	$("#validation").click(function()
	{
		var cardIdentifier = $("#cardIdentifier").val();

		if (cardIdentifier === '')
			return FHC_DialogLib.alertWarning('Bitte alle Felder ausfüllen');

		Cards.validation(cardIdentifier);
	});

	$("#searchstudent").autocomplete({
		source: CONTROLLER_URL + '/searchPerson',
		minLength:2,
		response: function(event, ui)
		{
			//Value und Label fuer die Anzeige setzen
			for(i in ui.content)
			{
				ui.content[i].value = ui.content[i].vorname+' '+ui.content[i].nachname+' ('+ ui.content[i].student_uid +')';
				ui.content[i].label = ui.content[i].vorname+' '+ui.content[i].nachname+' ('+ ui.content[i].student_uid +')';
			}
		},
		select: function(event, ui)
		{
			$('#uid').val(ui.item.student_uid);
			$('#showing').prop('disabled', false);
		}
	});

	$('#showing').click(function()
	{
		var uid = $('#uid').val();

		var data = {
			'uid' : uid
		}
		Cards.getFromPerson(data);
	});



});

