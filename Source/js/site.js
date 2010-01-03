/**
 * @author Arian Stolwijk
 */

window.addEvent('domready',function(){
	
	(function(){
		// Load the components		
		new Request({
			url: 'scripts.json',
			onSuccess: function(data){
				var scripts = JSON.decode(data);
				var list = document.getElement('#list ul');
				
				// Build an ul list
				$each(scripts,function(section,index){
					var sectionLi = new Element('li',{
						text: index,
						'class': 'section'
					}).inject(list);
					
					$each(section,function(info,component){
						var li = new Element('li',{
							text: component,
							'class': 'component '+component.replace('.','_'),
							morhp: {
								duration: 1000
							},
							styles: {
								opacity: 0.6
							}
						}).inject(list);
						
						li.deselectComp = function(){
							this.set({
								styles: {
									'background-image': ''
								},
								events: {
									mouseover: function(){
										this.morph({
											'background-color': '#f5f5f5',
											opacity: 0.9
										});
									},
									mouseout: function(){
										this.morph({
											'background-color': '#FFFFFF',
											opacity: 0.6
										});
									}
								}
							}).morph({
								'background-color': '#FFF',
								'opacity': 0.6
							});	
							this.store('selectComp',false);
						};
						li.selectComp = function(){
							this.removeEvents('mouseout')
								.removeEvents('mouseover')
								.setStyle('background-image','url(images/apply.png)')
								.morph({
									'background-color': '#EFEFEF',
									'opacity': 1
								})
								.store('selectComp',true);						
						};
						li.deselectComp();
					});
				});
				
				var hash = location.hash.replace(/#/g,'');
				$each(hash.split(';'),function(comp){
					comp = list.getElement('.'+comp);
					if(comp && comp.selectComp)
						comp.selectComp();
				});
				
			}
		}).send();		
	})();
	
	
	var addInputField = function(){
		var inputField = new Element('p',{
			text: 'URL: '
		});
		new Element('input',{
			type: 'text',
			name: 'file[]',
			size: 50
		}).inject(inputField);
		new Element('span',{text: ' or: '}).inject(inputField);
		new Element('input',{
			type: 'file',
			name: 'file[]'
		}).inject(inputField);
		new Element('a',{
			href: '#',
			'events': {
				'click': function(){	
					var elmt = this.getParent('p');				
					var fx = elmt.get('tween',{property: 'opacity'});
					fx.start(0).chain(
						function(){elmt.dispose();}
					);
				}
			}
		}).inject(inputField).adopt(new Element('img',{
			src: 'images/remove.png'
		}));
		inputField.inject('inputFields').fade('hide').fade('in');
	}
	addInputField();
	
	document.id('addInputField').addEvent('click',function(e){
		e.stop();
		addInputField();
	});
	
	
	var loading = document.id('loading')
		.set('tween',{link: 'chain'})
		.fade('hide');
	

	document.getElement('form').iFrameFormRequest({
		onRequest: function(){
			loading.fade('in');
			
			document.getElements('#list li').each(function(dep){
				if(dep.deselectComp){
					dep.deselectComp();
				}
			});	
			genLinkText.setStyle('display','none');
		},
		onComplete: function(data){
			var deps = JSON.decode(data);

			deps.each(function(dep){
				document.getElement('.'+dep.replace('.','_')).selectComp();
			});
			loading.fade('out');
		}
	});
	
	var genLinkText = document.id('linkText').setStyle('display','none');
	var generateLink = function(){
		var components = new Array();
		document.getElements('#list li').each(function(dep){
			if(dep.retrieve('selectComp')){
				components.include(dep.get('text'));
			}
		});

		var link = location.href;
		var pos = link.indexOf('#');
		if (pos != -1) {
			link = (pos) ? link.substr(0, pos) : '';
		}
		var hash = components.join(';');
		link += hash ? '#'+hash : '';
		
		genLinkText.setStyle('display','inline').set('value',link);		
	}
	
	document.id('genLink').addEvent('click',function(e){
		e.stop();
		generateLink();
	});
	
});

