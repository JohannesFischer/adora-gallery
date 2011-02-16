var AdoraGallery = new Class({

	Implements: Options,

	autoPlay: false,
	currentImage: null,
	Interval: false,
	windowSize: null,

	options: {
		enableKeys: true,
		slideShowInterval: 5000
	},
	
	initialize: function(imageContainer, thumbnails, options)
	{
		this.imageContainer = $(imageContainer);
		this.thumbnails = $$(thumbnails);
		
		this.windowSize = document.body.getCoordinates();

		this.setOptions(options);

		this.attach();
		this.createLoader();

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
			this.prev();
		}.bind(this));

		$$('.next')[0].addEvent('click', function(e){
			e.stop();
			this.next();
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

		// Thumbnails
		this.thumbnails.each(function(el, i){
			el.addEvents({
				'click': function(e){
					e.stop();
					this.show(i);
				}.bind(this)
			});
		}, this);
		
		// Navigation & Thumbnail
		
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
			if(e.key == 'left')
			{
				this.prev();
			}
			if(e.key == 'right')
			{
				this.next();
			}
			if(e.key == 'space')
			{
				this.toggleSlideShow();
			}
			if(e.key == 'up')
			{
				this.toggleInfo();
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
	
	createLoader: function()
	{
		this.Loader = new Element('div#Loader').inject(document.body);

		var size = this.Loader.getSize();

		this.Loader.setStyles({
			left: ((this.windowSize.width/2) - (size.x/2)).round(),
			opacity: 0,
			top: ((this.windowSize.height/2) - (size.y/2)).round()
		}).store('fxInstance', new Fx.Tween(this.Loader));
	},
    
    next: function()
    {
        var next = this.currentImage + 1 < this.thumbnails.length ? this.currentImage + 1 : 0;

        this.show(next);
    },
    
    prev: function()
    {
        var prev = this.currentImage - 1 > -1 ? this.currentImage - 1 : this.thumbnails.length - 1;

        this.show(prev);
    },
	
	setCurrentThumbnail: function()
	{
		var current = $$('ul .current')[0];

		if(current)
		{
			current.removeClass('current');
		}

		this.thumbnails[this.currentImage].addClass('current');
	},
	
	show: function(i)
	{
		this.toggleLoader();

		this.currentImage = i;

		var thumbnail = this.thumbnails[i];

		var current = this.imageContainer.getElement('div');

		var target = new Element('div', {
			styles: {
				opacity: 0,
				zIndex: 30	
			}	
		}).inject(this.imageContainer);

		var image = Asset.image(thumbnail.get('href'), {
			onLoad: function(){
				image.inject(target);

				var imageSize = image.getSize();

				target.setStyles({
					left: ((this.windowSize.width/2) - (imageSize.x/2)).round().limit(0, this.windowSize.width),
					top: ((this.windowSize.height/2) - (imageSize.y/2)).round().limit(0, this.windowSize.height)
				});

				this.toggleLoader();
				this.setCurrentThumbnail();

				if(current)
				{
					new Fx.Elements($$(current, target), {
						onComplete: function(){
							current.dispose();
							target.setStyle('z-index', 20);
						}
					}).start({
						'0': {
							opacity: 0
						},
						'1': {
							opacity: 1
						}
					});
				}
				else
				{
					new Fx.Tween(target, {
						
					}).start('opacity', 1);
				}
			}.bind(this)
		});
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
	
	toggleLoader: function()
	{
		var fx = this.Loader.retrieve('fxInstance');

		fx.cancel().start('start', this.Loader.getStyle('opacity') == 0 ? 1 : 0);
	},
	
	toggleSlideShow: function()
	{
		if(this.autoPlay)
		{
			$('Play').removeClass('pause');
			window.clearInterval(this.interval);
			this.autoPlay = false;
		}
		else
		{
			$('Play').addClass('pause');
			this.interval = this.next.periodical(this.options.slideShowInterval, this);
			this.autoPlay = true;
		}
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
			
			var password = f.getElement('input[name=password]').get('value');
			var username = f.getElement('input[name=username]').get('value');
			
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
			}).send('username='+username+'&password='+password);
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
	
	if($('LinkLogout'))
	{
		$('LinkLogout').addEvent('click', function(e){
			e.stop();
			new Request({
				onSuccess: function(){
					location.reload();	
				},
				url: AjaxURL+'logout'	
			}).send();
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