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

});