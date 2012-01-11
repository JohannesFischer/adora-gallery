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
	
	initialize: function (imageContainer, thumbnails, options)
	{
		this.imageContainer = $(imageContainer);
		this.thumbnails = $$(thumbnails);
		this.thumbnailHolder = $('Thumbnails');
		this.thumbnailWrapper = this.thumbnailHolder.getElement('div');
		
		this.windowSize = $(document.body).getCoordinates();

		this.setOptions(options);

		this.attach();
		this.createLoader();

		if (this.options.enableKeys)
		{
			this.attachKeyEvents();
		}

		if (this.options.showBubble)
		{
			new InfoBubble('#Thumbnails li a', {
				hideDelay: 1500,
				imageSource: 'rel',
				size: {
					height: 150,
					width: 200
				}
			});
		}

        if (this.thumbnails.length !== 0)
        {
            this.initThumbnails();
            this.show(0);
        }
		
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
	
	attach: function ()
	{
		// Controls TopBar
		$$('.prev')[0].addEvent('click', function (e){
			e.stop();
			this.prev();
		}.bind(this));

		$$('.next')[0].addEvent('click', function (e){
			e.stop();
			this.next();
		}.bind(this));

		// center image
		window.addEvent('resize', function () {

			// TODO update thumbnailholder-width
			this.windowSize = $(document.body).getCoordinates();

			this.centerElement(this.imageContainer.getElement('img'), this.imageContainer.getElement('div'));
		}.bind(this));
		
		// Navigation
		$('LinkAlbums').addEvent('click', function (e){
			e.stop();
			this.toggleBox($('LinkAlbums'));
		}.bind(this));

		$('LinkHelp').addEvent('click', function (e){
			e.stop();
			this.toggleBox($('LinkHelp'));
		}.bind(this));
		
		if ($('LinkInfo')) {
			$('LinkInfo').addEvent('click', function (e){
				e.stop();
				this.toggleBox($('LinkInfo'));
			}.bind(this));
		}

		// Toggle SlideShow
		$('Play').addEvent('click', function (e){
			e.stop();
			this.toggleSlideShow();
		}.bind(this));

		// Thumbnails
		/*
		myElement.addEvent('click:relay(a)', function(event, target){
			event.preventDefault();
			request.send({
				url: target.get('href')
			});
		});
		*/
		this.thumbnails.each(function (el, i){
			el.addEvents({
				'click': function (e){
					e.stop();
					this.show(i);
				}.bind(this)
			});
		}, this);
		
		// Scrolling
		if ($('SlideBack'))
		{
			$('SlideBack').addEvent('click', function (e) {
				e.stop();
				this.scrollBack();
			}.bind(this));
		}

		if ($('SlideForward'))
		{
			$('SlideForward').addEvent('click', function (e) {
				e.stop();
				this.scrollForward();
			}.bind(this));
		}
	},

	attachAlbumFunctions: function ()
	{
		$('Albums').getElements('a.album-link').each(function (el) {
			var id = el.get('id').split('_')[1];
			el.addEvent('click', function (e) {
				e.stop();
				
				new Request({
					onSuccess: function () {
						location.reload();	
					},
					url: AjaxURL + 'setAlbumID'
				}).send('id=' + id);
			});
		});
	},

	attachBoxFunctions: function (el)
	{
		if (!el.getElement('a.close'))
		{
			return;
		}

		el.getElement('a.close').addEvent('click', function (e) {
			e.stop();
			this.closeBox(el);
		}.bind(this));
	},
	
	attachKeyEvents: function ()
	{
		$(document.body).addEvent('keydown', function (e) {
			if (e.key === 'left')
			{
				this.prev();
			}
			else if (e.key === 'right')
			{
				this.next();
			}
			else if (e.key === 'space')
			{
				this.toggleSlideShow();
			}
			else if (e.key === 'up' && $('LinkInfo'))
			{
				this.toggleBox($('LinkInfo'));
			}
		}.bind(this));	
	},
	
	// TODO use Box_ prefix for "Box" function 
	Box_Create: function (id, fn)
	{
		var Box = new Element('div#' + id + '.Box', {
			styles: {
				marginTop: -25,
				opacity: 0
			}	
		}).adopt(
			new Element('a.close', {
				href: '#'
			}),
			new Element('div')
		).inject(document.body);

		new Request.HTML({
			onSuccess: function (){
				this.attachBoxFunctions($(id));
				if (fn !== false)
				{
					fn();
				}
				new Fx.Morph(Box, {
					duration: 500
				}).start({
					marginTop: 0,
					opacity: 1
				});
			}.bind(this),
			update: $(id).getElement('div'),
			url: AjaxURL + 'get' + id
		}).send();
	},

	centerElement: function (el, target)
	{
        if (!$(el))
        {
            return;
        }

		var size = el.getSize();

		(target !== undefined ? target : el).setStyles({
			left: ((this.windowSize.width / 2) - (size.x / 2)).round().limit(0, this.windowSize.width),
			top: ((this.windowSize.height / 2) - (size.y / 2)).round().limit(0, this.windowSize.height)
		});
	},
	
	closeBox: function (el)
	{
		new Fx.Morph(el, {
			duration: 500
		}).start({
			marginTop: 25,
			opacity: 0
		}).chain(function (){
			el.dispose();
		});
	},
	
	createLoader: function ()
	{
		this.Loader = new Element('div#Loader').inject(document.body);

		this.centerElement(this.Loader);

		this.Loader.setStyle('opacity', 0).store('fxInstance', new Fx.Tween(this.Loader));
	},

	// TODO extend Element?
	getElementWidth: function (el)
	{
		return el.getWidth() + el.getStyle('margin-left').toInt() + el.getStyle('margin-right').toInt();		
	},
	
	hideElement: function (el, styles)
	{
		el.removeEvent('mouseleave', this.hideElement);
		el.tween(styles[0], styles[1]);
	},
	
	initThumbnails: function ()
	{
		// TODO use script from cwpGallery
		var availableWidth,
            ul = this.thumbnailHolder.getElement('ul'),
            thumbnails = ul.getElements('li'),
			width;

		var availWidth = this.thumbnailHolder.getWidth();
		var playButtonWidth = this.getElementWidth($('Play'));
		var paginationButtonWidth = this.getElementWidth($('SlideBack'));
		paginationButtonWidth+= this.getElementWidth($('SlideForward'));

		availWidth -= (playButtonWidth + paginationButtonWidth);
		availWidth -= (this.thumbnailWrapper.getStyle('margin-left').toInt() + this.thumbnailWrapper.getStyle('margin-right').toInt());

		// TODO check for scrollBars

		this.thumbnailWrapper.setStyle('width', availWidth);
		
		width = 0;
		
		thumbnails.each(function (el){
			width+= this.getElementWidth(el);
		}.bind(this));

		if (width > availWidth)
		{
			ul.setStyles({
				position: 'absolute',
				width: width
			});
		}
	},
    
    next: function ()
    {
        var next = this.currentImage + 1 < this.thumbnails.length ? this.currentImage + 1 : 0;

        this.show(next);
    },
    
    prev: function ()
    {
        var prev = this.currentImage - 1 > -1 ? this.currentImage - 1 : this.thumbnails.length - 1;

        this.show(prev);
    },
	
	scroll: function (to)
	{
		var center,
			coordinates = {},
			left = to,
			limit,
			ul = this.thumbnailWrapper.getElement('ul');

		center = (this.thumbnailWrapper.getWidth() / 2).round();
		limit = (ul.getWidth() - (center * 2));

		if (to === undefined)
		{
			coordinates = ul.getElement('.current').getCoordinates(ul);
			left = (coordinates.left - center + (coordinates.width / 2).round());
		}

		if (coordinates.left < center || to < 0)
		{
			left = 0;
		}
		else if (left >= limit)
		{
			left = limit;
		}

		if (left !== ul.getStyle('left').toInt())
		{
			new Fx.Tween(ul, {
				duration: 500,
				transition: 'quart:out'
			}).start('left', left * -1);
		}
	},
	
	scrollBack: function ()
	{
		var coordinates,
			lastImage,
			limit,
			ul = this.thumbnailWrapper.getElement('ul');

		limit = ul.getStyle('left').toInt() * -1;

		for(var i = this.thumbnails.length-1; i >= 0; i--)
		{
			el = this.thumbnails[i];
			coordinates = el.getPosition(ul);
			elWidth = this.getElementWidth(el);

			if(coordinates.x <= limit || (coordinates.x + elWidth) <= limit)
			{
				this.scroll(coordinates.x + elWidth - this.thumbnailWrapper.getWidth());
				break;
			}
		}
	},
	
	scrollForward: function ()
	{
		var coordinates,
			el,
			lastImage,
			limit,
			ul = this.thumbnailWrapper.getElement('ul'),
			elWidth;

		limit = this.thumbnailWrapper.getWidth() + (ul.getStyle('left').toInt() * -1);

		for(var i = 0; i < this.thumbnails.length; i++)
		{
			el = this.thumbnails[i];
			coordinates = el.getPosition(ul);
			elWidth = this.getElementWidth(el);

			if(coordinates.x >= limit || (coordinates.x + elWidth) > limit)
			{
				this.scroll(coordinates.x);
				break;
			}
		}
	},
	
	setCurrentThumbnail: function ()
	{
		var current = $$('ul .current')[0];

		if (current)
		{
			current.removeClass('current');
		}

		this.thumbnails[this.currentImage].addClass('current');
	},
	
	show: function (i)
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

				if (current)
				{
					new Fx.Elements($$(current, target), {
						duration: this.options.fxDuration,
						onComplete: function (){
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

				this.scroll();
			}.bind(this)
		});
	},
	
	showBox: function (el)
	{
		var id = el.get('id');

		var fn = false;

		if (id === 'LinkInfo')
		{
			fn = this.updateInfo;
		}
		else if (id === 'LinkAlbums')
		{
			fn = this.attachAlbumFunctions;
		}

		this.Box_Create(el.get('id').substr(4), fn);
	},

	toggleBox: function (el)
	{
		var box = $$('.Box')[0];

		if (box && box.get('id') === el.get('id').substr(4))
		{
			this.closeBox(box);
			return;
		}

		if (box)
		{
			new Fx.Morph(box, {
				duration: 500
			}).start({
				marginTop: 25,
				opacity: 0
			}).chain(function (){
				box.dispose();
				this.showBox(el);
			}.bind(this));
		}
		else
		{
			this.showBox(el);
		}
	},
	
	toggleLoader: function ()
	{
		var fx = this.Loader.retrieve('fxInstance');

		fx.cancel().start('start', this.Loader.getStyle('opacity') == 0 ? 1 : 0);
	},
	
	toggleSlideShow: function ()
	{
		if (this.autoPlay)
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
	
	updateInfo: function ()
	{
		if (!$('Info'))
		{
			return;
		}

		var src = $('Image').getElement('img').get('src').split('/').getLast();

		new Request.HTML({
			update: $('Info').getElement('div'),
			url: AjaxURL+'getInfo'
		}).send('src=' + src);
	}
	
});