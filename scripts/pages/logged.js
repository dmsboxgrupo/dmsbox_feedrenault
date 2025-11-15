class LoggedPage {

	constructor() {
		
		$('#logout').click(() => {
			
			app.logout(() => {
			
				$('#page').attr('data-load', 'main.html').loadPage();
				
			});
			
		});
		
		$("body").on("click", "#footer-menu a,#menu-desktop a,#menu-subsecoes a", function() {
			
			$('#pesquisa-input,#pesquisa-desktop-input').val('');
			
		});
		
		/*
		$('.pesquisa-fecha').mousedown(function(e) {
			
			if (app.urlParams.backtopage) {
			
				e.preventDefault();
				e.stopImmediatePropagation();
				
				$(this).attr('href', `#p=${app.urlParams.backtopage}`);
				
				$('#pesquisa-input').val('');
				
			} 
			
		});
		*/
		
	}

	updateAppMessage() {

		const appMessage = $("#download-app-popup");
		
		let property = "read_popup_app";

		if ( navigator.userAgent.match(/iphone|ipod|ipad/i) ) {
			
			appMessage.find('play').hide();
			
			property += '_ios';
			
		} else {
			
			appMessage.find('apple').hide();
			
			property += '_android';
			
		}

		appMessage.find(".botao, .fechar").click(() => {

			// app.setMeta( property, true );
			
			app.global.hidePopupApp = true;

			appMessage.addClass("esconde");

		});
		
		if (!native.isWebView() && !app.user.metadata[property] && app.global.hidePopupApp !== true ) {

			appMessage.removeClass("esconde");

		}

	}

	update() {

		location.hash = '#p=home';
		
		NotificationsPage.load();
		
		app.pages.notifications.reset();
		
		this.updateAppMessage();

	}

}

LoggedPage.load = () => {
	
	if ( !app.pages.logged ) {
		
		app.pages.logged = new LoggedPage();
		
	}
	
	app.pages.logged.update();
	
}
