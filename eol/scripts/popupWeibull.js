
function afficherWeibull(){
	//Faire apparaitre la pop-up et ajouter le bouton de fermeture
	$('#weibull').fadeIn().css({ 'width': 600, 'height':500}).prepend('<a href="#" class="close"><img src="close_pop.png" class="btn_close" title="Close Window" alt="Close" /></a>');

	//Récupération du margin, qui permettra de centrer la fenêtre - on ajuste de 80px en conformité avec le CSS
	var popMargTop = ($('#weibull').height() + 80) / 2;
	var popMargLeft = ($('#weibull').width() + 80) / 2;

	//Apply Margin to Popup
	$('#weibull').css({ 
	'margin-top' : -popMargTop,
	'margin-left' : -popMargLeft
	});

	//Apparition du fond - .css({'filter' : 'alpha(opacity=80)'}) pour corriger les bogues d'anciennes versions de IE
	$('body').append('<div id="fade"></div>');
	$('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

	//Close Popups and Fade Layer
	$('body').on('click', 'a.close, #fade', function() { //Au clic sur le body...
	$('#fade , .popup_block').fadeOut(function() {
	$('#fade, a.close').remove();  
	}); //...ils disparaissent ensemble

	return false;
	});	
}

