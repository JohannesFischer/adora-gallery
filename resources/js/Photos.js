var AdoraGallery = new Class({

	Implements: Options,

	options: {
		
	},
	
	initialize: function(imageContainer, thumbnails, options)
	{
		this.imageContainer = $(imageContainer);
		this.thumbnails = $$(thumbnails);

		this.setOptions(options);

		this.attach();
	},
	
	attach: function()
	{
		
		// Toggle SlideShow
		$('Play').addEvent('click', function(e){
			e.stop();
			this.toggleSlideShow();
		}.bind(this));
		
		this.thumbnails.each(function(el){
			el.addEvents({
				'click': function(e){
					e.stop();
					this.imageContainer.empty();
					new Element('img', {
						src: el.get('href')	
					}).inject(this.imageContainer);
				}.bind(this)
			});
		}, this);
	},
	
	toggleSlideShow: function()
	{
		console.log('slideshow');
	}
	
});


window.addEvent('domready', function(){
	
	if($('Login'))
	{
		var target = $('LoginForm');
		var f = $('Login');
	
		f.getElement('input').focus();
		
		f.addEvent('submit', function(e){
			e.stop();
			
			var code = f.getElement('input').get('value');
			
			new Request.JSON({
				onSuccess: function(json){
					new Fx.Tween(f, {
						
					}).start('opacity', 0).chain(function(){
						target.empty();
						new Element('span.user-icon',{
							styles: {
								'background-image': 'url('+json.icon+')'
							}	
						}).inject(target);
						new Element('strong.user-name',{
							html: json.username
						}).inject(target);
						(function(){
							window.location.reload();
						}).delay(2000);
					});
				},
				url: AjaxURL+'login/'
			}).send('usercode='+code);
		});
	}
	
	if($('AddImages'))
	{
		var images = $$('.new-image');

		images.each(function(el){
			var f = el.getElement('form');

			f.addEvent('submit', function(e){
				e.stop();
				var formData = {};

				f.getElements('input[type=text], input[type=hidden], textarea').each(function(el){
					formData[el.get('name')] = el.get('value').trim();
				});
				
				new Request.JSON({
					onSuccess: function(){
						//el.dispose();
						el.setStyle('display', 'none');
					},
					url: AjaxURL+'addPhoto'
				}).send('data='+JSON.encode(formData));
			});
		});
	}
	
	if($('Image'))
	{
		new AdoraGallery($('Image'), $$('#Thumbnails li a'));
	}
	
	new infoBubble('#Thumbnails li a', {
		imageSource: 'rel'	
	});
	
});