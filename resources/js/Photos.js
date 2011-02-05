var AdoraGallery = new Class({

	Implements: Options,

	CurrentImage: null,
	Interval: false,

	options: {
		enableKeys: true
	},
	
	initialize: function(imageContainer, thumbnails, options)
	{
		this.imageContainer = $(imageContainer);
		this.thumbnails = $$(thumbnails);

		this.setOptions(options);

		this.attach();
		if(this.options.enableKeys)
		{
			this.attachKeyEvents();
		}
		
		this.show(0);
	},
	
	attach: function()
	{
		// Controls TopBar
		$$('.prev')[0].addEvent('click', function(e){
			e.stop();
			//this.prev();
		}.bind(this));

		$$('.next')[0].addEvent('click', function(e){
			e.stop();
			//this.next();
		}.bind(this));
		
		// Navigation		
		$('LinkInfo').addEvent('click', function(e){
			e.stop();
			this.toggleInfo();
		}.bind(this));

		// Toggle SlideShow
		$('Play').addEvent('click', function(e){
			e.stop();
			this.toggleSlideShow();
		}.bind(this));

		this.thumbnails.each(function(el, i){
			el.addEvents({
				'click': function(e){
					e.stop();
					this.show(i);
				}.bind(this)
			});
		}, this);
	},
	
	attachBoxFunctions: function(el)
	{
		el.getElement('a.close').addEvent('click', function(e){
			e.stop();
			this.closeBox(el);
		}.bind(this));
	},
	
	attachKeyEvents: function()
	{
		document.body.addEvent('keydown', function(e){
			if(e.key == 'up')
			{
				this.toggleInfo();
			}
			if(e.key == 'space')
			{
				this.toggleSlideShow();
			}
		}.bind(this));	
	},
	
	closeBox: function(el)
	{
		new Fx.Morph(el, {
			duration: 250	
		}).start({
			marginTop: 20,
			opacity: 0
		}).chain(function(){
			el.dispose();
		});
	},
	
	show: function(i)
	{
		this.imageContainer.empty();
		
		var thumbnail = this.thumbnails[i];
		
		var image = new Element('img', {
			src: thumbnail.get('href')	
		}).inject(this.imageContainer);
		
		// center image
		var availSize = this.imageContainer.getSize();
		
		var imageSize = image.getSize();
		console.log(availSize,imageSize);
	},
	
	toggleInfo: function()
	{
		if($('Info'))
		{
			this.closeBox($('Info'));
			return;
		}

		var Box = new Element('div#Info.Box').adopt(
			new Element('div')
		).inject(document.body);

		var src = $('Image').getElement('img').get('src').split('/').getLast();

		new Request.HTML({
			onSuccess: function(){
				this.attachBoxFunctions(Box);
			}.bind(this),
			update: $('Info').getElement('div'),
			url: AjaxURL+'getInfo'
		}).send('src='+src);
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