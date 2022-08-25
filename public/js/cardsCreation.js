$(document).ready(function() {

	$('#qrCreation').click(function() {
		CardsCreation.getQRCode();
	});
});

var CardsCreation = {

	getQRCode: function()
	{
		FHC_AjaxClient.ajaxCallPost(
			"extensions/FHC-Core-Cards/cis/Cards/getQRCode",
			{
				pincode : $('#pinCode').val()
			},
			{
				successCallback: function(response, textStatus, jqXHR) {
					if (FHC_AjaxClient.isError(response))
					{
						FHC_DialogLib.alertError(FHC_AjaxClient.getError(response));
					}
					else
					{
						var retval = FHC_AjaxClient.getData(response);
						$('.infos').removeClass('hidden');
						$('.infos div').empty();
						$('#qrCode').append(retval.svg);
						$('#pinCode').append('Ihr Pin zum Drucken der Karte: <b>' + retval.pin + '</b');
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

