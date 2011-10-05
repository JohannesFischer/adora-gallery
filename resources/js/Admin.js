var addImage = function(els)
{
	els.each(function(el){
		el.addEvent('click', function(e){

			var deleteLink,
				fileName = el.get('href'),
				li,
				parent = el.getParent(),
				target;
			
			e.stop();

			if (parent.getElement('div'))
			{
				return;
			}

			el.removeClass('image').addClass('loading');

			target = new Element('div.new-image').inject(el, 'after');
			
			target.adopt(new Element('p', {
				text: 'generating thumbnail'	
			}));
			
            li = target.getParent('li');

			new Request.HTML({
				onSuccess: function(){
					el.removeClass('loading').addClass('image');

					var f = target.getElement('form');

					f.addEvent('submit', function(e){
						e.stop();
						
						loadingOverlay.create(f);
						
						var formData = {};
		
						f.getElements('input[type=text], input[type=hidden], select, textarea').each(function(el){
							formData[el.get('name')] = el.get('value').trim();
						});
						
						if (formData.Album == 0)
						{
							loadingOverlay.dispose(f);
							return;
						}

						new Request({
							onSuccess: function(){
								loadingOverlay.dispose(f);
								new Fx.Tween(li).start('height', 0).chain(function () {
									li.dispose();
								});
							},
							url: AjaxURL+'addPhoto'
						}).send('data='+JSON.encode(formData));
					});
					
					// attach delete
					
					deleteLink = target.getElement('a.delete');
					
					deleteLink.addEvent('click', function (e) {
						e.stop();
						if (confirm('do you really want to delete the file ' + fileName + '?'))
						{
							new Request({
								onSuccess: function () {
									new Fx.Tween(li).start('height', 0).chain(function () {
										li.dispose();
									});
								},
								url: AjaxURL + 'deleteImage'
							}).send('filename=' + fileName);
						}
					});
				},
				update: target,
				url: AjaxURL+'getImageForm'	
			}).send('file='+fileName);
		});
	});	
};

var editUser = function(el)
{
	var li = el.getParent('li');
	
	var div = li.getElement('div');
	
	var id = el.getParent().getElement('span.ID').get('text').toInt();
	
	if(id > 0)
	{	
		new Request.HTML({
			onSuccess:function(responseTree) {
				div.setStyle('display', 'none');

				new Element('div').adopt(responseTree).inject(li);
				
				var ul = li.getElement('ul.user-icons');
		
				ul.addEvent('click', function(){
					ul.setStyle('overflow', 'visible');	
				});
				
				var form = li.getElement('form');
				form.addEvent('submit', function(e){
					e.stop();

					var data = {};

					data.ID = id;

					form.getElements('input[type=text]').each(function(el){
						data[el.get('name')] =  el.get('value');
					});

					new Request({
						onSuccess:function() {
							Object.each(data, function(el){
								li.getElement('.'+Object.keyOf(data, el)).set('text', el);
							});
							var divs = li.getElements('div');
							divs.setStyle('display', 'block');
							divs.getLast().dispose();
						},
						url: AjaxURL+'updateUser'
					}).send('data='+JSON.encode(data));
				});
			},
			url: AjaxURL+'editUser'
		}).send('id='+id);
	}
};

var getGalleries = function ()
{
	new Request.HTML({
		update: $('Galleries'),
		url: AjaxURL + 'getGalleries'	
	}).send();
}

var loadingOverlay = {
	
	create: function (el)
	{
		var dimensions = el.getSize();
		
		el.setStyle('position', 'relative');
		
		new Element('span.loading-overlay', {
			styles: {
				height: dimensions.y,
				width: dimensions.x
			}
		}).inject(el);
	},
	dispose: function (el)
	{
		el.getElement('.loading-overlay').dispose();
	}
	
};

var newAlbum = function ()
{
	var layer = new Element('div.layer').inject(document.body);
	
	new Request.HTML({
		onSuccess: function () {
			
		},
		update: layer,
		url: AjaxURL+'getView/admin_new_album'	
	}).send();
};

window.addEvent('domready', function(){

	if($('AddImages'))
	{
		addImage($$('#AddImages li a'));
	}
	
	if ($('Galleries'))
	{
		getGalleries();

		var link = $('Content').getElement('a');

		link.addEvent('click', function (e) {
			e.stop();
			
			new Element('div#FormTarget').inject(link, 'after');

			new Request.HTML({
				onSuccess: function (response) {
					$('CreateGallery').addEvent('submit', function (e) {
						e.stop();
						var title = $('CreateGallery').getElement('input').get('value').trim();
						
						new Request({
							onSuccess: function () {
								$('FormTarget').dispose();
								getGalleries();
							},
							url: AjaxURL + 'addGallery'	
						}).send('title=' + title)
					});
				},
				update: $('FormTarget'),
				url: AjaxURL + 'getAjaxView'
			}).send('view=admin_new_album');
		});
	}

	if($('User'))
	{
		$$('#User li a').addEvent('click', function(e){
			e.stop();
			editUser(this);
		});
	}
	
});