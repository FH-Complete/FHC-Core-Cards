$(document).ready(function() {

	$("#validation").click(function()
	{
		var cardIdentifier = $("#cardIdentifier").val();

		if (cardIdentifier === '')
			return FHC_DialogLib.alertWarning('Bitte alle Felder ausfüllen');

		Cards.validation(cardIdentifier);
	});
});

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

	_writeSuccess: function(text)
	{
		Cards._writeOutput(text, 'text-success');
	},

	_writeError: function(text)
	{
		Cards._writeOutput(text, 'text-danger');
	},

	_writeOutput: function(output, status)
	{
		$('#validationOutput p').remove();
		$("#validationOutput").append("<p class='" + status + "'>" + output + "</p>");
	}

}

