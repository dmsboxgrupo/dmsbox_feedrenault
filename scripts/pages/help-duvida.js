class HelpDuvidaPage extends Feed {

	constructor() {
		super();
	}

	update() {
		super.update();
	}

}

HelpDuvidaPage.load = () => {
	
	if ( !app.pages.helpduvida ) {
		app.pages.helpduvida = new HelpDuvidaPage();
	}
	
	app.pages.helpduvida.update();
	
}
