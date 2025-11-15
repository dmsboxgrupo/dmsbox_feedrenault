class NotificationsPage extends Feed {

	constructor() {
		
		super();
		
		this.notifications = [];
		
		this.enabledSearch = false;
		
	}

	updateDom() {
		
		const noPostDom = $('#no-post');
		const postsDom = $('#posts');
		const readAll = $('#read_all');

		const createNotification = ( post ) => {
			
			let html =
			`<div ${post.header_div} class="artigo ${ !post.readed ? 'naolido' : '' }">
				<div class="artigo-imagem">`;
			
			if (post.content_type === 'image') {
				
				html += `<img src="${post.content}" class="center"/>`;
				
			} else if (post.content_type === 'video') {
				
				//html += `<video class="center" muted><source src="${post.content}" type="video/mp4"></video>`;
				html += `<img src="${post.thumbnail}" class="center"/>`;
				
			} else if (post.alt_content) {
				
				html += `<img src="${post.alt_content}" class="center"/>`;
				
			}
			
			html +=
			`</div>
			  <div class="artigo-texto">
				<b>${ post.origin }</b>
				${ post.share_message }
			  </div>
			</div>`;
			
			
			const dom = $(html);
			
			post.dom = dom;
			
			var mc = new Hammer(dom[0]);

			mc.on("swipeleft swiperight", (e) => {
				
				const width = $(document).width();
				const left = ( e.type == "swiperight" ? "+=" : "-=" ) + width;
				
				dom.css({position: 'relative'}).animate({left,opacity:0}, 300, function() { setTimeout( () => { $(this).remove() }, 300 ) });
				
				post.viewed = true;
				post.readed = true;
				
				signal.emit('viewed', { id: post.id });
				
				const length = this.length;
				
				if ( length == 0 ) {
					
					this.readAll();
					
				} else {
					
					this.setNotificationCount();
					
				}

			});

			dom.click(() => {
				
				post.readed = true;
				
				dom.removeClass('naolido');
				
				$("#visualizadas").show();
				
				dom.insertAfter("#visualizadas");
				
				signal.emit('viewed', { id: post.id, category: post.category_id });
				
				/*
				if( post.category_id == 7 ){
					
					location.hash = `#p=home&q=enquete:${post.id}&clean=true&backtopage=notificacoes&filter=enquete&version=1`;
					
				} else {
					
					location.hash = `#p=home&q=id:${post.id}&clean=true&backtopage=notificacoes`;
					
				}*/
				
				
				//ui.loadPage('comunicado', '', this.createHandoutModal( post, {backlink:''} ));
				
				/*
				if (post.type == 'comunicados') {
					
					ui.loadPage('comunicado', '', this.createHandoutModal( post, {backlink:''} ));
					
				} else if (post.type == 'campanha') {
					
					ui.loadPage('comunicado', '', this.createCampaignModal( post, { backlink: '' } ));
					
				} else if (post.app_link) {
					
					location.hash = post.app_link;
					
				} else {*/
					
					if( post.category_id == 7 ){

						location.hash = `#p=pesquisa&q=enquete:${post.id}&clean=true&backtopage=notificacoes&filter=enquete&version=1`;
						
					} else {
						
						location.hash = `#p=pesquisa&q=id:${post.id}&clean=true&backtopage=notificacoes`;
						
					}
					
				//}
				
				this.updateNotificationCount();
				
			});
			
			return dom;

		}
		
		if ( this.length > 0 )  {
			
			noPostDom.hide();
			postsDom.show();
			readAll.show();
			
			$("#visualizadas").hide();
			$("#naovisualizadas").hide();
			
			const feed = this.notifications.slice().reverse();;
			
			for(const post of feed) {

				if (!post.viewed && !postsDom.find( post.dom )[0]) {

					if( !post.readed ) {
						
						$("#naovisualizadas").show();
						
						postsDom.prepend( createNotification( post ) );
					
					} else{
						
						$("#visualizadas").show();

						$("#visualizadas").after( createNotification( post ) );
						
					}
					
				}
				
			}

		} else {
			
			noPostDom.show();
			postsDom.hide();
			readAll.hide();
			$("#destaques h4").remove();
		}
		
		 
		
	}
	
	get readedLength() {
		
		let count = 0;
		
		for(const post of this.notifications) {
			
			if (!post.readed) count++;
			
		}
		
		return count;
		
	}
	
	get length() {
		
		let count = 0;
		
		for(const post of this.notifications) {
			
			if (!post.viewed) count++;
			
		}
		
		return count;
		
	}
	
	readAll() {
		
		const noPostDom = $('#no-post');
		const postsDom = $('#posts');
		const readAll = $('#read_all');
		
		const ids = [];
		
		for(const post of this.notifications) {
			
			if (!post.readed) {
				
				ids.push( post.id );
				
			}
			
			post.viewed = true;
			post.readed = true;
			
		}
		
		signal.emit('viewed', { ids: ids });

		this.updateNotificationCount();
		
		postsDom.empty();
		
		noPostDom.show( 300 );
		postsDom.hide( 600 );
		readAll.hide( 300 );
		
	}

	requestNotifications() {
		
		const params = {q:'feed', 'notifications':true};

		app.request(params, ( res ) => {
			
			let requestDomUpdate = false;
			
			const reverseFeed = res.feed.slice().reverse();
			
			for(const post of reverseFeed) {
				
				const result = this.notifications.find( notifyPost => notifyPost.id === post.id );
				
				if (!result) {
				
					this.notifications.unshift( this.parsePostData( post ) );
					
					requestDomUpdate = true;
					
				}
				
			}

			this.updateNotificationCount();
			
			if (app.urlParams.p === 'notificacoes' && requestDomUpdate) {
				
				this.updateDom();
				
			}
			
		} );
		
	}

	updateNotificationCount() {
		
		this.setNotificationCount( this.readedLength );
		
	}

	setNotificationCount( value ) {
		
		const countDom = $('.notification-count');
		
		if (value > 0) {

			countDom.show( 300 );

			if ( countDom.text() != value ) {
			
				countDom.text( value );
			
				countDom.css({
					'animation-name': 'scale-easeOutElastic'
				});
				
				setInterval( () => { 

					countDom.css({
						'animation-name': ''
					});
				
				}, 1000);
				
			}
			
		} else {
			
			countDom.hide();
			
		}
		
	}

	reset() {
		
		this.notifications = [];
		
		this.updateNotifications();
		
	}

	updateNotifications() {
		
		setTimeout( () => {

			this.updateNotifications();
			
		}, 5 * 60 * 1000 );
		
		this.requestNotifications();
		
	}

	update() {

		super.update();
		
		this.setTags( [
			'facebook',
			'instagram',
			'youtube',
			'twitter',
			'campanhas',
			'noticias',
			'comunicados'
		] );
		
		this.updateHelpMessage('notifications');
		
		$('#read_all').click( () => {
			
			this.readAll();
			
			
		});
		
		this.updateDom();

	}

}

NotificationsPage.load = () => {
	
	if ( !app.pages.notifications ) {
		
		app.pages.notifications = new NotificationsPage();
		
	}
	
	app.pages.notifications.update();
	
}
