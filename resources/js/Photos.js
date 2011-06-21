var AdoraGallery = new Class({

	Implements: Options,

	autoPlay: false,
	busy: false,
	currentImage: null,
	infoWindowOpen: false,
	Interval: false,
	windowSize: null,

	options: {
		autoHideNavigation: true,
		autoHideTime: 5000,
		disableRightClick: true,
		enableKeys: true,
		fxDuration: 1000,
		fxTransition: 'sine:in',
		showBubble: false,
		slideShowInterval: 5000
	},
	
	initialize: function(imageContainer, thumbnails, options)
	{
		this.imageContainer = $(imageContainer);
		this.thumbnails = $$(thumbnails);
		
		this.windowSize = $(document.body).getCoordinates();

		this.setOptions(options);

		this.attach();
		this.createLoader();

		if(this.options.enableKeys)
		{
			this.attachKeyEvents();
		}

		this.initThumbnails();

		if (this.options.showBubble)
		{
			new infoBubble(this.thumbnails, {
				hideDelay: 1500,
				imageSource: 'rel',
				size: {
					height: 150,
					width: 200
				}
			});
		}

		this.show(0);
		
		if (this.options.autoHideNavigation)
		{
			/*
			var top_bar = $('TopBar');
			top_bar.addEvent('mouseleave', function (e) {
				this.hideElement(top_bar, ['top', top_bar.getHeight() * -1]);
			}.bind(this));
			*/
		}
	},
	
	attach: function()
	{
		// TODO add window Events resize to update windowSize / fire center image function
		
		// Controls TopBar
		$$('.prev')[0].addEvent('click', function(e){
			e.stop();
			this.prev();
		}.bind(this));

		$$('.next')[0].addEvent('click', function(e){
			e.stop();
			this.next();
		}.bind(this));

		// center image
		window.addEvent('resize', function () {
			this.windowSize = $(document.body).getCoordinates();

			this.centerElement(this.imageContainer.getElement('img'), this.imageContainer.getElement('div'));
		}.bind(this));
		
		// Navigation		
		$('LinkInfo').addEvent('click', function(e){
			e.stop();
			this.toggleInfo();
		}.bind(this));

		$('LinkHelp').addEvent('click', function(e){
			e.stop();
			//this.toggleHelp();
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
	},
	
	attachBoxFunctions: function(el)
	{
		el.getElement('a.close').addEvent('click', function(e) {
			e.stop();
			this.closeBox(el);
		}.bind(this));
	},
	
	attachKeyEvents: function()
	{
		$(document.body).addEvent('keydown', function(e) {
			if(e.key == 'left')
			{
				this.prev();
			}
			else if(e.key == 'right')
			{
				this.next();
			}
			else if(e.key == 'space')
			{
				this.toggleSlideShow();
			}
			else if(e.key == 'up')
			{
				this.toggleInfo();
			}
		}.bind(this));	
	},

	centerElement: function (el, target)
	{
		var size = el.getSize();

		(target !== undefined ? target : el).setStyles({
			left: ((this.windowSize.width/2) - (size.x/2)).round().limit(0, this.windowSize.width),
			top: ((this.windowSize.height/2) - (size.y/2)).round().limit(0, this.windowSize.height)
		});
	},
	
	closeBox: function(el)
	{
		new Fx.Morph(el, {
			duration: 500
		}).start({
			marginTop: 25,
			opacity: 0
		}).chain(function(){
			el.dispose();
		});
	},
	
	createLoader: function()
	{
		this.Loader = new Element('div#Loader').inject(document.body);

		this.centerElement(this.Loader);

		this.Loader.setStyle('opacity', 0).store('fxInstance', new Fx.Tween(this.Loader));
	},

	// TODO extend Element?
	getElementWidth: function(el)
	{
		return el.getWidth() + el.getStyle('margin-left').toInt() + el.getStyle('margin-right').toInt();		
	},
	
	hideElement: function (el, styles)
	{
		el.removeEvent('mouseleave', this.hideElement);
		el.tween(styles[0], styles[1]);
	},
	
	initThumbnails: function()
	{
		// TODO use script from cwpGallery
		var thumbnailHolder = $('Thumbnails');
		var thumbnailWrapper = thumbnailHolder.getElement('div');
		var ul = thumbnailHolder.getElement('ul');
		var thumbnails = ul.getElements('li');

		var availWidth = document.body.getWidth();
		var playButtonWidth = this.getElementWidth($('Play'));
		var paginationButtonWidth = this.getElementWidth($('Slide'));
		
		availWidth -= (playButtonWidth + paginationButtonWidth);
		availWidth -= (thumbnailWrapper.getStyle('margin-left').toInt() + thumbnailWrapper.getStyle('margin-right').toInt());
		
		thumbnailWrapper.setStyle('width', availWidth);
		
		return;
		
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
		if (this.busy === true)
		{
			return;
		}

		this.busy = true;

		var current,
			target,
			thumbnail;

		this.toggleLoader();

		this.currentImage = i;

		thumbnail = this.thumbnails[i];

		current = this.imageContainer.getElement('div');

		target = new Element('div', {
			styles: {
				opacity: 0,
				zIndex: 30	
			}	
		}).inject(this.imageContainer);

		var image = Asset.image(thumbnail.get('href'), {
			onLoad: function () {
				image.inject(target);
				
				if (this.options.disableRightClick)
				{
					image.addEvent('contextmenu', function (e) {
						e.stop();
					});
				}

				this.centerElement(image, target);

				this.toggleLoader();
				this.setCurrentThumbnail();

				if(current)
				{
					new Fx.Elements($$(current, target), {
						duration: this.options.fxDuration,
						onComplete: function(){
							current.dispose();
							this.busy = false;
							target.setStyle('z-index', 20);
							// update image details in the Info box
							this.updateInfo();
						}.bind(this),
						transition: this.options.fxTransition
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
						duration: this.options.fxDuration,
						transition: this.options.fxTransition
					}).start('opacity', 1).chain(function () {
						this.busy = false;
					}.bind(this));
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

		var Box = new Element('div#Info.Box', {
			styles: {
				marginTop: -25,
				opacity: 0
			}	
		}).adopt(
			new Element('div')
		).inject(document.body);

		this.updateInfo();
		
		new Fx.Morph(Box, {
			duration: 500
		}).start({
			marginTop: 0,
			opacity: 1
		});
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


// TODO move domready function to seperate file

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

			var password = MD5_hexhash(f.getElement('input[name=password]').get('value').trim());
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