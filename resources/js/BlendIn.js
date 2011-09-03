var BlendIn = new Class({

	Implements: Options,

	element: null,

	options: {
	},

	initialize: function (options)
	{
		this.setOptions(options);
	},

	createBorder: function ()
	{
		var windowSize = document.body.getSize();

		return new Element('div.BBorder', {
			styles: {
				left: (windowSize.x / 2).round(),
				top: (windowSize.y / 2).round()
			}	
		}).inject(document.body);
	},
	
	show: function (element)
	{
		this.element = $(element);

		var left,
			size = this.element.getSize(),
			top,
			windowSize = document.body.getSize();

		left = ((windowSize.x / 2) - (size.x / 2)).round();
		top = ((windowSize.y / 2) - (size.y / 2)).round();

		this.element.setStyles({
			left: left,
			opacity: 0,
			top: top
		});

		var border = this.createBorder();
		
		new Fx.Morph(border, {
			duration: 500
		}).start({
			height: size.y,
			left: left,
			top: top,
			width: size.x
		}).chain(function () {
			new Fx.Tween(border, {
				duration: 200
			}).start('opacity', 0).chain(function () {
				border.dispose();
			});
			
			new Fx.Tween(this.element, {
				transition: 'elastic:in'
			}).start('opacity', 1);
		}.bind(this));
	}
	
});