$(document).ready(function() {

	TerminalOverview.getTerminals();

	$('#newTerminal').click(function()
	{
		TerminalOverview._hiddeForm();
	});

	$('#cancelTerminal').click(function()
	{
		TerminalOverview._hiddeForm();
	});

	$('#addTerminal').click(function()
	{
		TerminalOverview.addTerminal();
	});
});

var TerminalOverview = {

	addTerminal: function()
	{
		var name = $('#terminalName').val();
		var beschreibung = $('#terminalBeschreibung').val();
		var ort = $('#terminalOrt').val();
		var type = $('#terminalType').val();
		var aktiv = $('#terminalAktiv').prop('checked');

		if (name === '' || beschreibung === '' || ort === '')
		{
			FHC_DialogLib.alertWarning('Bitte alle Felder ausfüllen!');
			return false;
		}

		FHC_AjaxClient.ajaxCallPost(
			"extensions/FHC-Core-Cards/cis/Terminal/addTerminal",
			{
				name : name,
				beschreibung : beschreibung,
				ort : ort,
				type : type,
				aktiv : aktiv
			},
			{
				successCallback: function(response, textStatus, jqXHR) {
					if (FHC_AjaxClient.isError(response))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(response));
					}
					else
					{
						TerminalOverview.getTerminals();
						TerminalOverview._clearInputs();
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	updateTerminal: function(terminalID)
	{
		FHC_AjaxClient.ajaxCallPost(
			"extensions/FHC-Core-Cards/cis/Terminal/updateTerminal",
			{
				id : terminalID,
				name : $('#input_cardsName_' + terminalID).val(),
				beschreibung : $('#input_cardsBeschreibung_' + terminalID).val(),
				ort : $('#input_cardsOrt_' + terminalID).val(),
				type : $('#input_cardsType_' + terminalID).val(),
				aktiv : $('#input_cardsAktiv_' + terminalID).prop('checked')
			},
			{
				successCallback: function(response, textStatus, jqXHR) {

					if (FHC_AjaxClient.isError(response))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(response));
					}
					else
					{
						TerminalOverview._setReadOnly(terminalID);
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	delTerminal: function(terminalID)
	{
		FHC_AjaxClient.ajaxCallPost(
			"extensions/FHC-Core-Cards/cis/Terminal/delTerminal",
			{
				id : terminalID
			},
			{
				successCallback: function(response, textStatus, jqXHR) {
					if (FHC_AjaxClient.isError(response))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(response));
					}
					else
					{
						$('#cardsterminalRow_' + terminalID).remove();
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	getTerminals: function()
	{
		FHC_AjaxClient.ajaxCallGet(
			"extensions/FHC-Core-Cards/cis/Terminal/getTerminals",
			{},
			{
				successCallback: function(response, textStatus, jqXHR) {
					if (FHC_AjaxClient.isError(response))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(response));
					}
					else
					{
						$("#terminalTable tbody").html("");
						var terminals = FHC_AjaxClient.getData(response);

						$.each(terminals,
							function() {
								TerminalOverview._createRow(this)
							}
						);

						TerminalOverview._setUpdateEvents();
						TerminalOverview._addTableSorter();
					}
				},
				errorCallback: function(jqXHR, textStatus, errorThrown)
				{
					FHC_DialogLib.alertError(jqXHR);
				}
			}
		);
	},

	_hiddeForm: function()
	{
		if ($('.terminalForm').hasClass('hidden'))
			$('.terminalForm').removeClass('hidden');
		else
			$('.terminalForm').addClass('hidden');

		TerminalOverview._clearInputs();
	},

	_clearInputs: function()
	{
		$('.terminalForm').find('input:text').each(function ()
		{
			$(this).val('');
		});
		$('#terminalAktiv').prop('checked', false);
	},

	_createRow: function(data)
	{
		var tr =
			'<tr id="cardsterminalRow_' + data.cardsterminal_id + '">' +
				'<td id="cardsName_' + data.cardsterminal_id + '">' + data.name + '</td>' +
				'<td id="cardsBeschreibung_' + data.cardsterminal_id + '">' + data.beschreibung + '</td>' +
				'<td id="cardsOrt_' + data.cardsterminal_id + '">' + data.ort + '</td>' +
				'<td id="cardsType_' + data.cardsterminal_id + '">' + data.type + '</td>' +
				'<td id="cardsAktiv_' + data.cardsterminal_id + '">' + (data.aktiv ? "Ja" : "Nein") + '</td>' +
				'<td id="cardsAktion_' + data.cardsterminal_id + '">' +
					'<i class="fa fa-edit editTerminal fa-2x" data-id="' + data.cardsterminal_id + '"></i>' +
					'&nbsp' +
					'<i class="fa fa-trash delTerminal fa-2x" data-id="' + data.cardsterminal_id + '"></i>' +
				'</td>' +
				'<td id="cardsSave_' + data.cardsterminal_id + '" class="hidden">' +
					'<i class="fa fa-save updateTerminal fa-2x" data-id="' + data.cardsterminal_id + '"></i>' +
				'</td>' +
			'</tr>'

		$("#terminalTable tbody").append(tr);
	},

	_addTableSorter: function()
	{
		Tablesort.addTablesorter("terminalTable", [[1, 0]], ["filter"], 2);
	},

	_setReadOnly: function(terminalID)
	{
		$('#cardsterminalRow_' + terminalID + ' td').each(function ()
		{
			var inputid = $(this).prop('id');

			if (inputid === 'cardsAktiv_' + terminalID)
			{
				var inputvalue = $('#input_' + inputid).prop('checked');
				var newinput = (inputvalue === true ? "Ja" : "Nein")
			}
			else if ((inputid !== 'cardsAktion_' + terminalID) && (inputid !== 'cardsSave_' + terminalID))
			{
				var newinput = $('#input_' + inputid).val();
			}
			else
			{
				$('#cardsAktion_' + terminalID).removeClass('hidden');
				$('#cardsSave_' + terminalID).addClass('hidden');
				return;
			}
			$(this).html(newinput);
		});
	},

	_formatDateToGerman: function(date)
	{
		if (date !== null)
			return date.substring(8, 10) + "." + date.substring(5, 7) + "." + date.substring(0, 4);
		else
			return '-';
	},

	_setUpdateEvents: function()
	{
		$('.editTerminal').click(function()
		{
			var terminalID = ($(this).data('id'));

			$('#cardsterminalRow_' + terminalID + ' td').each(function ()
			{
				var inputid = $(this).prop('id');
				var inputvalue = $(this).html();

				if (inputid === 'cardsType_' + terminalID)
				{
					var newInput =
						'<select id="input_' + inputid + '" class="form-control">' +
							'<option value="student"' + (inputvalue === "student" ? "selected" : "") + '>Student</option>' +
							'<option value="mitarbeiter"' + (inputvalue === "mitarbeiter" ? "selected" : "") + '>Mitarbeiter</option>' +
						'</select>';
				}
				else if (inputid === 'cardsAktiv_' + terminalID)
				{
					var newInput =
						'<input type="checkbox" class="checkbox" id="input_' + inputid + '" ' + (inputvalue === "Ja" ? "checked" : "") + '/>'
				}
				else if ((inputid !== 'cardsAktion_' + terminalID) && (inputid !== 'cardsSave_' + terminalID))
				{
					var newInput =
						'<input type="text" class="form-control" id="input_' + inputid +'" value="'+ inputvalue  +'"/>';
				}
				else
				{
					$('#cardsAktion_' + terminalID).addClass('hidden');
					$('#cardsSave_' + terminalID).removeClass('hidden');
					return;
				}

				$(this).html(newInput);
			});


		});

		$('.delTerminal').click(function()
		{
			var terminalID = ($(this).data('id'));

			TerminalOverview.delTerminal(terminalID);
		});

		$('.updateTerminal').click(function()
		{
			var terminalID = ($(this).data('id'));

			TerminalOverview.updateTerminal(terminalID);
			
		});
	}
}

