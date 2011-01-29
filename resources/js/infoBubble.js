// requires MooTools More Fx.Elements && Assets

var infoBubble = new Class({

	Implements: Options,
	
	activeEl: null,
	bubble: null,
	delay: null,
	elements: null,
	linkType: null,
	tipHeight: 10,
	visible: false,

	options: {
		contentMargin: 10,
		fade: true,
		fxDuration: 250,
		hideDelay: 2500,
		imageSource: 'href',
		marginBottom: 10,
		marginTop: -20,
		size: {
			height: 200,
			width: 250
		},
		stopOnClick: true
	},
	
	initialize: function(selector, options)
	{
		this.setOptions(options);

		this.elements = $$(selector);

		if(this.elements.length > 0)
		{
			this.attach();
			this.createBubble();
		}
	},
	
	attach: function()
	{
		this.elements.each(function(el){
			el.addEvents({
				'click': function(e){
					if(this.options.stopOnClick)
					{
						e.stop();
					}
				}.bind(this),
				'mouseleave': function(){
					this.hideBubble();
				}.bind(this),
				'mouseover': function(){
					this.setLinkType(el);
					this.showBubble(el);
				}.bind(this)
			});
		}, this);
	},
	
	clearDelay: function()
	{
		window.clearInterval(this.delay);
	},
	
	createBubble: function()
	{
		this.bubbleContainer = new Element('div.infoBubble', {
			styles: {
				height: this.options.size.height + this.tipHeight + (this.options.contentMargin * 2),
				marginTop: this.options.marginTop,
				opacity: 0,
				top: -1000,
				width: this.options.size.width + (this.options.contentMargin * 2)
			}
		}).inject(document.body);;
		
		this.bubble = new Element('div', {
			'class': 'infoBubble-Bubble',
			events: {
				'mouseleave': function(){
					this.hideBubble();
				}.bind(this),
				'mouseover': function(){
					this.clearDelay();
				}.bind(this)
			},
			styles: {
				height: this.options.size.height + (this.options.contentMargin * 2)
			}
		}).inject(this.bubbleContainer);
		
		if(this.options.fade)
		{
			this.bubbleContainer.store('fxInstance', new Fx.Morph(this.bubbleContainer, {
				duration: this.options.fxDuration
			}));
		}
		
		this.bubbleContent = new Element('div', {
			'class': 'infoBubble-Content',
			styles: {
				height: this.options.size.height
			}
		}).inject(this.bubble);
	},
	
	getContent: function(el)
	{
		this.bubbleContent.addClass('loading');

		var href = el.get('href');

		if(this.linkType == 'inline')
		{
			this.resetBubble();

			var id = href.substr(1);

			if($(id))
			{
				this.setContent($(id).get('html'));
			}
		}
		else if(this.linkType == 'image')
		{
			var image = el.retrieve('image');

			if(!image)
			{
				image = new Asset.image(el.get(this.options.imageSource), {
					onload: function(){
						el.store('image', image);
						this.insertImage(el, image);
					}.bind(this)
				});
			}
			else
			{
				this.insertImage(el, image);
			}
		}
		else
		{
			this.resetBubble(true);

			if(el.retrieve('responseHTML'))
			{
				this.setContent(el.retrieve('responseHTML'));
			}
			else
			{
				new Request.HTML({
					onSuccess: function(responseTree, responseElements, responseHTML){
						el.store('responseHTML', responseHTML);
						this.bubbleContent.removeClass('loading')
					}.bind(this),
					update: this.bubbleContent,
					url: href	
				}).send();
			}
		}
	},
	
	setLinkType: function(el)
	{
		var href = el.get('href');

		if(href.substr(0, 1) == '#')
		{
			this.linkType = 'inline';
		}
		else if(href.test('.gif|.jpeg|.jpg|.png|.png'))
		{
			this.linkType = 'image';
		}
		else
		{
			this.linkType = 'ajax';
		}
	},
	
	hideBubble: function()
	{
		this.delay = (function(){
			var fx = this.bubbleContainer.retrieve('fxInstance');
			fx.start({
				marginTop: this.options.marginTop,
				opacity: 0
			}).chain(function(){
				this.activeEl = null;
				this.resetBubble();
				this.visible = false;
			}.bind(this));
		}.bind(this)).delay(this.options.hideDelay);
	},
	
	insertImage: function(el, image)
	{
		image.set('opacity', 0);
		this.bubbleContent.empty().adopt(image);
	
		var imageSize = image.getSize();

		var fn = function(){
			image.fade(0, 1);
		};

		this.resizeBubble(el, imageSize.y, imageSize.x, fn);	
	},
	
	setContent: function(html)
	{
		this.resetBubble();

		this.bubbleContent.removeClass('loading').set('html', html);
	},
	
	resetBubble: function()
	{
		this.bubbleContainer.setStyles({
			height: this.options.size.height + this.tipHeight + (this.options.contentMargin * 2),
			width: this.options.size.width + (this.options.contentMargin * 2)
		});

		this.bubble.setStyle('height', this.options.size.height + (this.options.contentMargin * 2));

		this.bubbleContent.setStyle('height', this.options.size.height).empty().addClass('loading');
	},
	
	resizeBubble: function(el, height, width, fn)
	{
		var bubbleSize = this.bubbleContent.getSize();

		if(height == bubbleSize.y && width == bubbleSize.x)
		{
			if(fn != undefined)
			{
				fn();
			}
			return;
		}
		var coordinates = el.getCoordinates();

		// TODO add method to calculate the position
		var left = (coordinates.left + (coordinates.width/2).round() - (width/2).round()) - this.options.contentMargin ;
		var top = (coordinates.top - height - this.tipHeight - this.options.marginBottom) - (this.options.contentMargin * 2);
		
		new Fx.Elements($$(this.bubbleContainer, this.bubble, this.bubbleContent), {
			onComplete: function(){
				this.bubble.setStyle('height', height + (this.options.contentMargin * 2));
				this.bubbleContent.setStyle('height', height).removeClass('loading');

				if(fn != undefined)
				{
					fn();
				}
			}.bind(this)
		}).start({
			'0': {
				height: height + this.tipHeight + (this.options.contentMargin * 2),
				left: left,
				top: top,
				width: width + (this.options.contentMargin * 2)
			},
			'1': {
				height: height + (this.options.contentMargin * 2)
			},
			'2': {
				height: height
			}
		});
	},
	
	setBubbleContent: function(content)
	{
		this.bubbleContent.removeClass('loading').adopt(content);	
	},
	
	showBubble: function(el)
	{
		this.clearDelay();
		
		if(this.activeEl == el)
		{
			return;
		}
		this.activeEl = el;

		var coordinates = el.getCoordinates();

		// TODO limit left 0, window.width

		var size = {
			y: this.options.size.height,
			x: this.options.size.width
		};
		
		if(this.visible && this.linkType == 'image')
		{
			size = this.bubbleContent.getSize();
		}
		
		var left = (coordinates.left + (coordinates.width/2).round() - (size.x/2).round()) - this.options.contentMargin;
		var top = (coordinates.top - size.y - this.tipHeight - this.options.marginBottom) - (this.options.contentMargin * 2);

		this.bubbleContainer.setStyles({
			left: left,
			top: top
		});

		this.getContent(el);

		if(this.visible)
		{
			return;
		}

		if(this.options.fade)
		{
			var fx = this.bubbleContainer.retrieve('fxInstance');
			fx.start({
				marginTop: [this.options.marginTop, 0],
				opacity: [0, 1]
			});
			this.visible = true;
		}
		else
		{
			this.bubbleContainer.fade('show');	
		}
	}
	
});