var AdoraGallery = new Class({

	Implements: Options,

	autoPlay: false,
	currentImage: null,
	infoWindowOpen: false,
	Interval: false,
	windowSize: null,

	options: {
		autoHideNavigation: true,
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

		this.initThumbnails();

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

	// TODO extend Element
	getElementWidth: function(el)
	{
		return el.getWidth() + el.getStyle('margin-left').toInt() + el.getStyle('margin-right').toInt();		
	},
	
	initThumbnails: function()
	{
		var thumbnailHolder = $('Thumbnails');
		var thumbnailWrapper = thumbnailHolder.getElement('div');
		var ul = thumbnailHolder.getElement('ul');
		var thumbnails = ul.getElements('li');
		
		console.log(thumbnailWrapper.getWidth(), ul.getWidth());

		var availWidth = document.body.getWidth();
		var playButtonWidth = this.getElementWidth($('Play'));
		var paginationButtonWidth = this.getElementWidth($('Slide'));
		console.log(paginationButtonWidth);
		availWidth-= (playButtonWidth + paginationButtonWidth) + (thumbnailWrapper.getStyle('margin-left').toInt() + thumbnailWrapper.getStyle('margin-right').toInt());
		console.log(availWidth);
		
		thumbnailWrapper.setStyle('width', availWidth);
		
		return;
		
		var ulMargin = ul.getStyle('margin-left').toInt() + ul.getStyle('margin-right').toInt();
		
		var width = ulMargin;
		
		thumbnails.each(function(el){
			width+= el.getWidth() + el.getStyle('margin-left').toInt() + el.getStyle('margin-right').toInt();
		});
		
		if(width > thumbnailHolder.getWidth())
		{
			var buttonSize = $('Play').getWidth() + $('Play').getStyle('margin-left').toInt() + $('Play').getStyle('margin-right').toInt();
			
			var availableWidth = thumbnailHolder.getWidth() - ulMargin - (buttonSize * 2);

			//new Element('div').setStyle('width', availableWidth).wraps(ul);

			new Element('a.control.slide', {
				href: '#'
			}).inject(thumbnailHolder);
		}
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
							// update image details in the Info box
							this.updateInfo();
						}.bind(this)
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

		this.updateInfo();
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
	},
	
	updateInfo: function()
	{
		if(!$('Info'))
		{
			return;
		}

		var src = $('Image').getElement('img').get('src').split('/').getLast();

		new Request.HTML({
			onSuccess: function(){
				this.attachBoxFunctions($('Info'));
			}.bind(this),
			update: $('Info').getElement('div'),
			url: AjaxURL+'getInfo'
		}).send('src='+src);
	}
	
});


window.addEvent('domready', function(){
	
	if($('Login'))
	{
		var target = $('LoginForm');
		var f = $('Login');

		var size = target.getSize();
		var windowSize = document.body.getSize();

		target.setStyles({
			left: ((windowSize.x/2)-(size.x/2)).round(),
			opacity: 0,
			top: size.y*-1
		});

		new Fx.Morph(target,{
			duration: 750
		}).start({
			opacity: 1,
			top: ((windowSize.y/2)-(size.y/2)).round()
		}).chain(function(){
			f.getElement('input[type=text]').focus();
		});
		
		f.addEvent('submit', function(e){
			e.stop();

			var password = f.getElement('input[name=password]').get('value').trim();
			var username = f.getElement('input[name=username]').get('value').trim();

			if(username == '' || password == '')
			{
				return;
			}

			new Request.JSON({
				onSuccess: function(json){
					if(json.error)
					{
						f.getElement('input[name=password]').set('text', '');

						new Fx.Tween(target, {
							duration: 500,
							transition: 'elastic:out'
						}).start('marginLeft', [-30, 0]);
					}
					else
					{
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
					}
				},
				url: AjaxURL+'login/'
			}).send('username='+username+'&password='+password);
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
	
	if($('Thumbnails'))
	{
		new infoBubble('#Thumbnails li a', {
			hideDelay: 1500,
			imageSource: 'rel',
			size: {
				height: 150,
				width: 200
			}
		});
		/*
		var el = $('Thumbnails');

		var height = el.getHeight();

		var holder = new Element('div.holder', {
			styles: {
				bottom: 0,
				height: height
			}
		}).inject(el, 'after').grab(el).store('fx', new Fx.Tween(el));

		holder.addEvents({
			'mouseenter': function(){
				console.log('enter');
				var fx = holder.retrieve('fx');
				fx.cancel().start('bottom', 0);
			},
			'mouseleave': function(){
				(function(){
					var fx = holder.retrieve('fx');
					console.log('fx', height*-1);
					fx.cancel().start('bottom', height*-1);
				}).delay(3000);
			}
		});
		*/
	}

});