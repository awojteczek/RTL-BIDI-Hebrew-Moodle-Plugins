//MooTools More, <http://mootools.net/more>. Copyright (c) 2006-2009 Aaron Newton <http://clientcide.com/>, Valerio Proietti <http://mad4milk.net> & the MooTools team <http://mootools.net/developers>, MIT Style License.

MooTools.More={'version':'1.2.4.4','build':'6f6057dc645fdb7547689183b2311063bd653ddf'};(function(){var wait={wait:function(duration){return this.chain(function(){this.callChain.delay($pick(duration,500),this);}.bind(this));}};Chain.implement(wait);if(window.Fx){Fx.implement(wait);['Css','Tween','Elements'].each(function(cls){if(Fx[cls])Fx[cls].implement(wait);});}
Element.implement({chains:function(effects){$splat($pick(effects,['tween','morph','reveal'])).each(function(effect){effect=this.get(effect);if(!effect)return;effect.setOptions({link:'chain'});},this);return this;},pauseFx:function(duration,effect){this.chains(effect).get($pick(effect,'tween')).wait(duration);return this;}});})();Fx.Scroll=new Class({Extends:Fx,options:{offset:{x:0,y:0},wheelStops:true},initialize:function(element,options){this.element=this.subject=document.id(element);this.parent(options);var cancel=this.cancel.bind(this,false);if($type(this.element)!='element')this.element=document.id(this.element.getDocument().body);var stopper=this.element;if(this.options.wheelStops){this.addEvent('start',function(){stopper.addEvent('mousewheel',cancel);},true);this.addEvent('complete',function(){stopper.removeEvent('mousewheel',cancel);},true);}},set:function(){var now=Array.flatten(arguments);if(Browser.Engine.gecko)now=[Math.round(now[0]),Math.round(now[1])];this.element.scrollTo(now[0],now[1]);},compute:function(from,to,delta){return[0,1].map(function(i){return Fx.compute(from[i],to[i],delta);});},start:function(x,y){if(!this.check(x,y))return this;var scrollSize=this.element.getScrollSize(),scroll=this.element.getScroll(),values={x:x,y:y};for(var z in values){var max=scrollSize[z];if($chk(values[z]))values[z]=($type(values[z])=='number')?values[z]:max;else values[z]=scroll[z];values[z]+=this.options.offset[z];}
return this.parent([scroll.x,scroll.y],[values.x,values.y]);},toTop:function(){return this.start(false,0);},toLeft:function(){return this.start(0,false);},toRight:function(){return this.start('right',false);},toBottom:function(){return this.start(false,'bottom');},toElement:function(el){var position=document.id(el).getPosition(this.element);return this.start(position.x,position.y);},scrollIntoView:function(el,axes,offset){axes=axes?$splat(axes):['x','y'];var to={};el=document.id(el);var pos=el.getPosition(this.element);var size=el.getSize();var scroll=this.element.getScroll();var containerSize=this.element.getSize();var edge={x:pos.x+size.x,y:pos.y+size.y};['x','y'].each(function(axis){if(axes.contains(axis)){if(edge[axis]>scroll[axis]+containerSize[axis])to[axis]=edge[axis]-containerSize[axis];if(pos[axis]<scroll[axis])to[axis]=pos[axis];}
if(to[axis]==null)to[axis]=scroll[axis];if(offset&&offset[axis])to[axis]=to[axis]+offset[axis];},this);if(to.x!=scroll.x||to.y!=scroll.y)this.start(to.x,to.y);return this;},scrollToCenter:function(el,axes,offset){axes=axes?$splat(axes):['x','y'];el=$(el);var to={},pos=el.getPosition(this.element),size=el.getSize(),scroll=this.element.getScroll(),containerSize=this.element.getSize(),edge={x:pos.x+size.x,y:pos.y+size.y};['x','y'].each(function(axis){if(axes.contains(axis)){to[axis]=pos[axis]-(containerSize[axis]-size[axis])/2;}
if(to[axis]==null)to[axis]=scroll[axis];if(offset&&offset[axis])to[axis]=to[axis]+offset[axis];},this);if(to.x!=scroll.x||to.y!=scroll.y)this.start(to.x,to.y);return this;}});(function(){var read=function(option,element){return(option)?($type(option)=='function'?option(element):element.get(option)):'';};this.Tips=new Class({Implements:[Events,Options],options:{onShow:function(){this.tip.setStyle('display','block');},onHide:function(){this.tip.setStyle('display','none');},title:'title',text:function(element){return element.get('rel')||element.get('href');},showDelay:100,hideDelay:100,className:'tip-wrap',offset:{x:16,y:16},windowPadding:{x:0,y:0},fixed:false},initialize:function(){var params=Array.link(arguments,{options:Object.type,elements:$defined});this.setOptions(params.options);if(params.elements)this.attach(params.elements);this.container=new Element('div',{'class':'tip'});},toElement:function(){if(this.tip)return this.tip;return this.tip=new Element('div',{'class':this.options.className,styles:{position:'absolute',top:0,left:0}}).adopt(new Element('div',{'class':'tip-top'}),this.container,new Element('div',{'class':'tip-bottom'})).inject(document.body);},attach:function(elements){$$(elements).each(function(element){var title=read(this.options.title,element),text=read(this.options.text,element);element.erase('title').store('tip:native',title).retrieve('tip:title',title);element.retrieve('tip:text',text);this.fireEvent('attach',[element]);var events=['enter','leave'];if(!this.options.fixed)events.push('move');events.each(function(value){var event=element.retrieve('tip:'+value);if(!event)event=this['element'+value.capitalize()].bindWithEvent(this,element);element.store('tip:'+value,event).addEvent('mouse'+value,event);},this);},this);return this;},detach:function(elements){$$(elements).each(function(element){['enter','leave','move'].each(function(value){element.removeEvent('mouse'+value,element.retrieve('tip:'+value)).eliminate('tip:'+value);});this.fireEvent('detach',[element]);if(this.options.title=='title'){var original=element.retrieve('tip:native');if(original)element.set('title',original);}},this);return this;},elementEnter:function(event,element){this.container.empty();['title','text'].each(function(value){var content=element.retrieve('tip:'+value);if(content)this.fill(new Element('div',{'class':'tip-'+value}).inject(this.container),content);},this);$clear(this.timer);this.timer=(function(){this.show(this,element);this.position((this.options.fixed)?{page:element.getPosition()}:event);}).delay(this.options.showDelay,this);},elementLeave:function(event,element){$clear(this.timer);this.timer=this.hide.delay(this.options.hideDelay,this,element);this.fireForParent(event,element);},fireForParent:function(event,element){element=element.getParent();if(!element||element==document.body)return;if(element.retrieve('tip:enter'))element.fireEvent('mouseenter',event);else this.fireForParent(event,element);},elementMove:function(event,element){this.position(event);},position:function(event){if(!this.tip)document.id(this);var size=window.getSize(),scroll=window.getScroll(),tip={x:this.tip.offsetWidth,y:this.tip.offsetHeight},props={x:'left',y:'top'},obj={};for(var z in props){obj[props[z]]=event.page[z]+this.options.offset[z];if((obj[props[z]]+tip[z]-scroll[z])>size[z]-this.options.windowPadding[z])obj[props[z]]=event.page[z]-this.options.offset[z]-tip[z];}
this.tip.setStyles(obj);},fill:function(element,contents){if(typeof contents=='string')element.set('html',contents);else element.adopt(contents);},show:function(element){if(!this.tip)document.id(this);this.fireEvent('show',[this.tip,element]);},hide:function(element){if(!this.tip)document.id(this);this.fireEvent('hide',[this.tip,element]);}});})();