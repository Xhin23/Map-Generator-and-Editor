var _actionid = 0; 

var custom_tiles = { 
	fg: {}, 
	terrain: {} 
};

function hash(str) {
    var hash = 0;
    for (var i = 0; i < str.length; i++) 
    {
        hash = ((hash<<5)-hash)+str.charCodeAt(i);
        hash = hash & hash;
    }
    return hash;
}

function bind_ruletype() 
{
    $(".rules-ruletype").unbind().change(function() {
	    var val = $(this).val();
	    var el = $(this).closest('tr');
	    
	    if (val == 'trait')
	    {
	        el.find('.rules-min,.rules-max,.rules-message,.rules-entries-link').hide();
	    }
	    if (val == 'numeric'  || val == 'items')
	    {
	        el.find('.rules-min,.rules-max').show();
	    }
	    
	    if (val == 'numeric')
	    {
	        el.find('.rules-message,.rules-entries-link').hide();
	    }
	    else if (val == 'items')
	    {
	        if (el.find('.rules-entries-link').html().length)
	        {
	            el.find('.rules-message').hide();
	            el.find('.rules-entries-link').show();
	        }
	        else
	        {
	            el.find('.rules-message').show();
	            el.find('.rules-entries-link').hide();
	        }
	    }
	});
}
   
function bind_buttons() 
{
   $(".button").unbind().click(function() {
	   if ($(this).hasClass('button-el'))
	   {
	       buttons[$(this).attr('function')]($(this));
	   }
	   else
	   {
	       buttons[$(this).attr('function')]($(this).attr('var1'),$(this).attr('var2'));
	   }
   });
}

function mixed_clone(mixed) 
{        
    if (mixed instanceof Array)
    {
        var new_mixed = [];
        for (var a in mixed)
        {
            new_mixed.push(mixed[a]);
        }
    }
    else if (mixed instanceof Object)
    {
        var new_mixed = {};
        for (var a in mixed)
        {
            new_mixed[a] = mixed[a];
        }
    }
    else { var new_mixed = mixed; }
    
    return new_mixed;
}
    
// Handles all AJAX requests
var ajax = {
    d: {
    	'ajax': 1, 
    	'gameid': _gameid,
    	'sid': _sid                
	},
    push: function(func,data,success) {
        if (typeof success == 'undefined') 
        {
            success = function() {};
        }
        data = $.extend(
            { function: func, data: data },
            this.d,
        );
        $.ajax({
            type: "POST",
            url: "/map/ajax",
            data: data,
            success: success
        }); 
    },
    change: function(func,data,success) {
    	data['func'] = func;
        return this.push('changes',data,success);
    }
}

// Handles the function for getting new changes
var timer = {
    obj: {},
    get_new: function() {
    	timer.stop();
        ajax.push('get_new',{ shareid: _shareid },function(data) {
        	data = JSON.parse(data);
        	if (data)
        	{
        		for (var i in data)
        		{
        			client.render(data[i]);
        		}
        		_shareid = data[i].shareid;
        	}
            timer.start();
        });
    },
    stop: function() { 
        clearInterval(this.obj);
    },
    start: function() { 
        this.obj = setInterval(this.get_new, 1000);
    }
};

// Handles all button functions that do more specialized things than low-level objects
var buttons = {
    invert_selection: function() {
        $(".tile").toggleClass('darken');
    },
    clear_selection: function() {
        $(".darken").removeClass('darken');
    },            
    alt_region: function(mode,name,color,desc,func) {
        $("#alt-region").show();
        $("#alt-region-mode").html(mode);
        $("#alt-region-name").val(name);
        $("#alt-region-color").val(color);
        $("#alt-region-color").css('background-color',color);
        $("#alt-region-desc").val(desc);
        $("#alt-region-button").attr('function',func);
    
        $("#alt-region-delete").hide();
        $("#alt-region-delete").removeClass('sel');
        $("#alt-region-delete input").prop('checked',false);
    },
    add_region: function() {
        this.alt_region('Add','','00467D','','add_region_save');
    },
    edit_region: function(id) {
        $("#explore-region").hide();
        var d = regions.data[id];
        this.alt_region('Edit',d['name'],d['color'],d['desc'],'edit_region_save');
        $("#alt-region-button").attr('var1',id);
        $("#alt-region-delete").show();
    },
   
    add_img: function(type) {
        $("#add-img").show();
        var mode = 'Terrain';
        if (type == 'fg') { mode = 'Foreground'; }
        $("#add-img-mode").html(mode);
        
        $("#add-img-url").val('');
        $("#add-img-preview").html('');
        
        $("#save-add-img").attr('var1',type);
    },

    
    explore_edit: function(x,y) {
        explore.tile_editor(x,y);
    },

    reseed_rule: function(el) {
        var val = rand.range(1,_max_seed);
        el.parent().find('.rules-seed').val(val);
    },
      
    // -=-=-=-=-=-=-=-=-=-=-=-

    add_region_save: function() {
        var name = $("#alt-region-name").val();
        var desc = $("#alt-region-desc").val();
        var color = $("#alt-region-color").val();
        ajax.change('add_region',{ name: name , desc: desc, color: color }, function(e) {
        	e = JSON.parse(e);
        	client.publish('regions','add_region',e,name,desc,color);
	        $("#alt-region").hide();
    		regions.all_buttons("#region-buttons",'set_selected');
        });
    },                              
    edit_region_save: function(id) {
         $("#alt-region").hide();
    	if ($("#alt-region-delete input").prop('checked')) 
    	{
    		client.publish('regions','delete_region',id);
			ajax.change('delete_region',{ id: id });
			explore.display_main();
    		return;
    	}
    	var name = $("#alt-region-name").val();
    	var desc = $("#alt-region-desc").val();
    	var color = $("#alt-region-color").val(); 
    	
    	 client.publish('regions','edit_region',id,name,desc,color);
         ajax.change('edit_region',{
         	id: id,
         	name: name,
         	desc: desc,
         	color: color
         });
         regions.display(id);
    },
    save_add_img: function(type) {
    	var url = $("#add-img-url").val();
    	var name = 'url-'+hash(url);
    	client.run('add_image',type,name,url);
        ajax.change('add_image',{
        	type: type,
        	name: name,
        	url: url
    	});
        $("#add-img").hide();
    },                
    undo_selection: function(type) {
    	var coords = [];
        $(".darken").each(function() {
        	var x = $(this).attr('x');
        	var y = $(this).attr('y');
            coords.push([x,y]);
        });
        client.remap(coords,'map','undo_'+type);
        ajax.change('undo_tiles',{
        	type: type,
        	coords: JSON.stringify(coords)
        });
        this.clear_selection();
    },         
    revert: function() {      
    	var coords = [];
        $(".darken").each(function() {
        	var x = $(this).attr('x');
        	var y = $(this).attr('y');
            coords.push([x,y]);
        });
        client.remap(coords,'map','revert');
        ajax.change('revert',{ coords: JSON.stringify(coords) });
        this.clear_selection();
    },
    region: function(id,func) {
        regions[func](id);
    },           
    add_rules: function() {
        var rules = $("#add-rules").val();
        ajax.change('add_rules',{
        	entype: content.entype,
        	entid: content.entid,
        	rules: rules,
        	seed: _seed
        },function(e) {
        	var rules = JSON.parse(e);
        	client.batch('paired_list',rules,'content','add_rule', content.entype,content.entid,'$K','$V');
            content.message('Rules Added!');
    		content.refresh('rules');
        });
    },
    edit_rules: function() {
        var changes = [];
        var list = {};
        $("#rules-edit").find('tr').each(function() {
            var key = $(this).attr('key');
            if (!key) { return; }
            
            var data = {
            	name: $(this).find('.rules-name').val(),
                ruletype: $(this).find('.rules-ruletype').val(),
                chance: $(this).find('.rules-chance').val(),
                seed: $(this).find('.rules-seed').val(),
                min: $(this).find('.rules-min').val(),
                max: $(this).find('.rules-max').val()
           	};
            
            list[key] = data;
            
            data['ruleid'] = key;
            changes.push(data);
        });
        content.message('Saved!');
        client.batch('paired_list',list,'content','edit_rule', content.entype,content.entid,'$K','$V');
        ajax.change('update_rules',{ rules: changes });
        content.choose_entype(content.entype,content.entid);
    },
    delete_rule: function(ruleid) {
    	ajax.change('delete_rule',{ ruleid: ruleid });
    	
    	client.publish('content','delete_rule',content.entype,content.entid,ruleid);
        $("#rules-edit .key-"+ruleid).fadeOut(400,'swing',function() { $(this).remove(); }); 
        if ($.isEmptyObject(content.data[content.entype][content.entid]))
        {
            $("#rules-edit").parent().fadeOut();    
        }
    }, 
    add_entries: function() {
        var entries = $("#add-entries").val();
        ajax.change('add_entries',{
        	ruleid: content.ruleid,
        	entries: entries
        },function(e) {
        	var entries = JSON.parse(e);
        	client.batch('paired_list',entries,'content','add_entry', content.entype,content.entid,content.ruleid,'$K','$V');
            content.message('Entries Added!');
    		content.refresh('entries');
        });
    },
    edit_entries: function() {
    	var changes = [];
    	var list = {};
        $("#entries-edit").find('tr').each(function() {
            var key = $(this).attr('key');
            if (!key) { return; }
            	
            var data = { 
            	name: $(this).find('.entries-name').val(), 
            	weight: $(this).find('.entries-weight').val()
        	};
            
            list[key] = data;
        	changes.push({ entryid: key, data: data });
        });

        client.batch('paired_list',list,'content','edit_entry', content.entype,content.entid,content.ruleid,'$K','$V');
        content.message('Saved!');
        ajax.change('edit_entries',{ entries: changes });
        content.refresh('entries');
    },
    delete_entry: function(entryid) {
    	ajax.change('delete_entry',{ entryid: entryid });
    	client.publish('content','delete_entry',content.entype,content.entid,content.ruleid,entryid);
        $("#entries-edit .key-"+entryid).fadeOut(400,'swing',function() { $(this).remove(); });
        if ($.isEmptyObject(content.data[content.entype][content.entid][content.ruleid]['entries']))
        {
            $("#entries-edit").parent().fadeOut();
        }
    },                
    save_explore_edit: function(x,y) {
    	var name = $("#explore-edit-name").val();
    	var desc = $("#explore-edit-desc").val();
    	ajax.change('edit_tile_data',{
    		x: x,
    		y: y,
    		name: name,
    		desc: desc		
    	},function(e) {  });
        
        client.publish('explore','edit',x,y,name,desc);
        explore.reset();
        explore.display(x,y);
    },

};

// Handles the "magic" selection function in the "Edit Map" menu.
var magic = {
    search: [],
    done: {},
    tile: '',
    fg: '',
    
    fix: function(x) {
       if (typeof this.done[x] == 'undefined')
       {
            this.done[x] = {};
       }
    },
    is_done: function(x,y) {
        this.fix(x);
        if (this.done[x][y]) { return true; }
    },
    set_done: function(x,y) {
        this.fix(x);
        this.done[x][y] = 1;
    },                
    check_tile: function(x,y)
    {
        var fail = false;
        if (x < 0 || y < 0) { fail = true; }
        if ($("#magic-terrain").hasClass('sel') && (map.get_tile(x,y) != this.tile)) { fail = true; }
        if ($("#magic-fg").hasClass('sel') && (map.get_fg(x,y) != this.fg)) { fail = true; }
        if ($("#magic-selbar").hasClass('sel') && map.el(x,y).hasClass('darken')) { fail = true; }
        if (!$("#magic-fg").hasClass('sel') && !$("#magic-terrain").hasClass('sel')) { fail = true; }
        
        if (fail) { return this.set_done(x,y); }
        if (this.is_done(x,y)) { return; }
        this.search.push([x,y]);
    },

    select: function(x,y) {
        
        this.tile = map.get_tile(x,y);
        this.fg = map.get_fg(x,y);
        this.search = [ [x,y] ];
        this.done = {};
        var mode = 'darken';
        if (map.el(x,y).hasClass('darken'))
        {
            mode = 'highlight';
        }
        
        var i = 0;
        while (this.search.length)
        {
            x = parseInt(this.search[0][0]);
            y = parseInt(this.search[0][1]);

            this.search.shift();
            if (this.is_done(x,y)) { continue; }                    

            map[mode](x,y);

            this.check_tile(x-1,y);
            this.check_tile(x+1,y);
            this.check_tile(x,y-1);
            this.check_tile(x,y+1);
            
            this.set_done(x,y);
        }
    }
};

// Handles various conditions that can be true or false and tie into other objects.
var conditions = {
    has_fg: function(x,y,fg)
    {
        if (map.get_fg(x,y) == fg)
        {
            return true;
        }
    },
    is_terrain: function(x,y,name) 
    {
        if (map.get_tile(x,y) == name)
        {
            return true;
        }
    }
};

// This object alters tiles, either individually or based on some tiles that have been selected through various means.
// Various methods from various objects can be applied whenever "apply_to_tile" or "apply_to_darkened" is called.
var render = {
    obj: '',
    func: '',
    var1: '',
    condition: '',
    func2: '',

    init: function(list) {
        this.obj = list[0];
        this.func = list[1];
        this.var1 = list[2];
        this.condition = list[3];
        this.func2 = list[4];
    },
    ajax: function(func,tiles) {
        ajax.change('edit_tiles',{
            subfunc: func,
            tiles: JSON.stringify(tiles),
            var1: render.var1
        },function(e) { });
    },
    
    apply_to_tile: function(x,y) {
        
        func = this.func;
        if (this.condition)
        {
            if (conditions[this.condition](x,y,this.var1))
            {
                func = this.func2;
            }
        }
        
        client.publish(this.obj,func,x,y,this.var1);
        this.ajax(func,[ [x,y] ]);
    },
    apply_to_darkened: function() {
        var coords = [];
        $(".darken").each(function() {
            var x = $(this).attr('x');
            var y = $(this).attr('y');
            coords.push([x,y]);
        });
                
        this.ajax(render.func,coords);
        client.batch('coords_custom',coords,this.obj,this.func,this.var1);
    }
};

// A low-level object that alters the actual HTML of the map and looks at information contained within.
var map = {
    el: function(x,y) {
        return $("#coord-"+x+'-'+y);
    },
    get_tile: function(x,y) {
        return this.el(x,y).attr('tile');
    },
    get_fg: function(x,y) {
        return this.el(x,y).find('.tile-ent').attr('fg');
    },
    // ----
    darken: function(x,y) {
        this.el(x,y).addClass('darken');
    },
    highlight: function(x,y) {
        this.el(x,y).removeClass('darken');
    },
    toggle: function(x,y) {
        this.el(x,y).toggleClass('darken');                    
    },
    
    darken_all: function() {
        $(".tile").addClass('darken');
    },
    highlight_all: function() {
        $(".tile").removeClass('darken');
    },
    
    hover: function(x,y,color) {
        this.el(x,y).hover(function() {
            $(this).addClass('hover');
            $(this).css('background-color','#'+color);
        }, function() {
            $(this).removeClass('hover');
            $(this).css('background-color','');
        });
    },
    edit_terrain: function (x,y,name) {
        var el = this.el(x,y).find('.tile-terrain');
        var url = '/map/__images/terrain/'+name+'.png';
        if (custom_tiles['terrain'][name]) { url = custom_tiles['terrain'][name]; }
        el.attr('src',url);
        el.parent().attr('tile',name);
    },
    undo_terrain: function(x,y) {
    	var tile = this.el(x,y).attr('original_tile');
        this.edit_terrain(x,y,tile);
    },
    undo_fg: function(x,y) {
    	var tile = this.el(x,y).attr('original_fg');
        this.edit_fg(x,y,tile);
    },
    revert: function(x,y) {
    	var tile = this.el(x,y).attr('gen');
    	this.edit_terrain(x,y,tile);
    	this.clear_fg(x,y);
    },
    edit_fg: function(x,y,name) {
        var s = _image_size-4;
        this.clear_fg(x,y);
        if (!name) { return; }
        var url = '/map/__images/ents/'+name+'.png';
        if (custom_tiles['fg'][name]) { url = custom_tiles['fg'][name]; }
        this.el(x,y).append('<img src="'+url+'" class="tile-ent" style="width: '+s+'px; height: '+s+'px;" fg="'+name+'" />');
    },
    clear_fg: function(x,y) {
        this.el(x,y).find('.tile-ent').remove();
    }
};

// This object handles the various "Tools" within the "Edit Map" menu. The "Magic" tool is also handled in its own object.
var select = {
    selecting: 0,
    x: 0,
    y: 0,
    mode: 'darken',
	shape: 'box',
	trigger: function(x,y) {
	    this.x = x;
	    this.y = y;
	    this.mode = 'highlight';
	    if ($("#select-mode").hasClass('sel'))
	    {
	        this.mode = 'darken';
	    }
	    
	    if (this.selecting == 0)
	    {
	        map[this.mode](x,y);
	    }
	    this.selecting = 1-this.selecting;
	},
	flip_by_type: function(x,y,type)
	{
	    var arr = [x,y];
	    if (type == 'y')
	    {
	        arr = [y,x];
	    }
	    return arr;
	},
	stroke: function(x,y,type)
	{
	   var c = this.flip_by_type(x,y,type);
	    
	   min = (c[0] < this[type]) ? c[0] : this[type];
	   max = (c[0] < this[type]) ? this[type] : c[0];
	   
	   var u;
	   
	   for (var i = min; i <= max; i++)
	   {
	       u = this.flip_by_type(i,c[1],type);
	       
	       map[this.mode](u[0],u[1]);
	   } 
	},
	draw: function(x,y) {
	   if (!this.selecting) { return; }
	   
	   this[this.shape](x,y);
	},
	box: function(x,y) {
	   this.line(x,y);
	   this.stroke(x,y,'x');      
	   this.stroke(x,y,'y');
    },
    line: function(x,y) {
       map[this.mode](x,y);
    }
};
	   
var tools = {
    obj: '',
	func: '',
	mode: '',
	init: function(mode) {
	    this.mode = mode;
	    $("#edit-mode").hide();
	    $("#select-tools").hide();
	    $("#magic-settings").hide();
	    $(".img-edit").addClass('nosel');
	    $("#region-tools").show();
	    this[mode]();
	},
	point: function() {
	    map.highlight_all();
	    this.obj = 'render';
	    this.func = 'apply_to_tile';   
	    $(".img-edit").removeClass('nosel');
	    $("#region-tools").hide();
	},
	line: function() {
	    this.obj = 'select';
	    this.func = 'trigger';
	    select.shape = 'line';    
	    $("#edit-mode").show();
	    $("#select-tools").show();
	},
	box: function() {
	    this.line();
	    select.shape = 'box';
	},
	magic: function() {
	    this.obj = 'magic';
	    this.func = 'select';
	    $("#select-tools").show();
	    $("#magic-settings").show();
    }
};
   
// A low-level object that handles anything having to do with regions, including updating them, viewing their information, or loading
// region-specific menus.
var regions = {
    data: {
    },
        
    add_region: function(id,name,desc,color) { 
        this.data[id] = { name: name, desc: desc, color: color, coords: [] };
    },
    edit_region: function(id,name,desc,color) {
	    this.data[id]['name'] = name;
		this.data[id]['desc'] = desc;
		this.data[id]['color'] = color;
	},
	delete_region: function(id) {
		this.reset_points(id);
		delete this.data[id];
	},
	reset_points: function(id) {
	    var coords = this.data[id]['coords'];
	    var x,y;
	    for (var i in coords)
	    {
	        explore.remove_region(coords[i][0],coords[i][1],id);    
	    }
	    this.data[id]['coords'] = []; 
	},
	add_point: function(id,x,y) {
	    this.data[id]['coords'].push([x,y]);
	},
	remove_point: function(id,x,y) {
	    var coords = this.data[id]['coords'];
	    for (var i in coords)
	    {
	        if (coords[i][0] == x && coords[i][1] == y)
	        {
	            this.data[id]['coords'].splice(i,1);
	            break;
	        }
	    }
	},
	
	add_selected: function(id) {
		var coords = [];
	    $(".darken").each(function() {
	        var x = $(this).attr('x');
	        var y = $(this).attr('y');
	        coords.push([x,y]);
	    });
	    
	    client.batch('coords_custom',coords,'client','do_add_to_region',id);
	    
	    map.highlight_all();
		return coords;
	},
	set_selected: function(id) {
	    if (!$("#edit-region-mode").hasClass('sel'))
	    {
	        return this.select_region(id,'darken');
	    }
	    this.reset_points(id);
	    
	    var coords = this.add_selected(id);
	    
	    ajax.change('set_region',{
	    	id: id,
	    	coords: JSON.stringify(coords)
	    });
	},
	select_region: function(id,func) {
	    
	    if (func == 'highlight')
	    {
	        map.darken_all();
	    }
	    else
	    {
	        map.highlight_all();
	    }
	    
	    var coords = this.data[id]['coords'];
	    for (var i in coords)
	    {
	        map[func](coords[i][0],coords[i][1]);
	    }
	},
	content_choose_entype: function(id) {
	    content.choose_entype('region',id);
	},
	display: function(id) {
	    explore.reset();
	    var d = this.data[id];
	    this.select_region(id,'highlight');
	    
	    explore.show('region');
	    $("#explore-region-name").html(d['name']);
	    $("#explore-region-desc").html(d['desc']);
	    $("#explore-region-edit").attr('var1',id);
	},
	delete_tile_region: function(id) {
	    var x = explore.ux;
	    var y = explore.uy;
	    
	    ajax.change('delete_tile_region',{ x: x, y: y, regionid: id });
	    
		client.run('delete_tile_region',id,x,y);
	    
	    explore.tile_editor(x,y);
	},
	
	buttons: '',
	all_buttons: function(el,region_function,no_add) {
	    this.buttons = '';
	    
	    if (typeof no_add == 'undefined')
	    {
	        no_add = false;
	    }
	    
	    for (var i in this.data)
	    {
	        this.button(i,this.data[i],region_function);
	    }
	    this.render_buttons(el,no_add);
	},
	buttons_by_keys: function(el,keys,region_function) {
	    this.buttons = '';
	    for (var i in keys) 
	    {
	        this.button(keys[i],this.data[keys[i]],region_function);
	    }
	    this.render_buttons(el,true);
	},
	button: function(key,data,region_function) {
	    var style = '';
	    if (data['color'])
	    { 
	        style += 'background-color: #'+data['color']+';';
	    }
	    this.buttons += '<input type="button" class="button" function="region" var1="'+key+'" var2="'+region_function+'" style="'+style+'" value="'+data['name']+'" />';
	},
	render_buttons: function(el,no_add) 
	{       
	    if (!no_add)
	    {
	        this.buttons += '<input type="button" class="button" function="add_region" value=" + " />';
	    }
	    $(el).html(this.buttons);
	    bind_buttons();
	    this.buttons = '';
    }
};

// Handles the icon-based links in the menu that move it around, change its opacity, etc.
var menu_alt = { 
    el: $("#controls"),
    
    css: function(field,a,b) {
        if (this.el.css(field) == a) 
        {
			a = b;
        }
        this.el.css(field,a);
    },
    swap: function(a,b,val) {
        if (this.el.css(a) == val)
        {
            this.el.css(b,val);
            this.el.css(a,'');
        }
        else
        {
            this.el.css(a,val);
            this.el.css(b,'');
        }
    },
    
    horizontal: function(el) {
        this.swap('left','right','0px');
    },
    vertical: function(el) {
        this.swap('top','bottom','0px');
        this.el.toggleClass('pos-top');
    },
    opacity: function(el) {
        el.toggleClass('sel');
        this.el.toggleClass('transparent');
    },
    fixed: function(el) {
    	this.el.removeClass('transparent');
    	$("#opacity").removeClass('sel');
        $(".alt").toggle();
        this.el.toggleClass('unfixed');
        el.toggleClass('sel');
        this.css('position','fixed','relative');
    }
};

// Handles the transition between "Explore", "Edit Map" and "Content Rules" menus. A lot of other objects are initialized or otherwise
// referenced here.
var panes = {
    explore: function() {
        tools.obj = 'explore';
        tools.func = 'display';
        
        explore.display_main();
    },
    edit: function() {
        tools.init('point');
        map.highlight_all();
        $("#tools-point").trigger('click');
        $("#tile-field").trigger('click');
        $("#fg-edit .sel").removeClass('sel');
        regions.all_buttons("#region-buttons",'set_selected');
    },
    rules: function() {
    	$("#choose-region").show();
    	if ($.isEmptyObject(regions.data)) { $("#choose-region").hide(); }
        map.highlight_all();
        var func = 'content_choose_entype';
        tiles.render("#rules-terrain",'terrain',func);
        tiles.render("#rules-fg",'fg',func);
        regions.all_buttons("#rules-regions",func,true);
        content.menu('main');
        content.entype = '';
        content.entid = '';
        content.ruleid = '';
        content.panel = '';
        
        $("#rules-back").hide();
    }
};

// A low-level object that handles the information of individual tiles.
// This object also handles menus associated with viewing/editing individual tiles and is a starting place for the explore menu in general.
var explore = {
    data: {
    },
    
    get: function(x,y) 
    {
        var key = x+'-'+y;
        if (typeof this.data[key] == 'undefined')
        {
            this.data[key] = {name: '', desc: '', regions: []};
        }
        return this.data[key];
    },
    edit: function(x,y,name,desc)
    {    
        this.get(x,y)['name'] = name;
        this.get(x,y)['desc'] = desc;
    },
    add_region: function(x,y,id) 
    {
        this.get(x,y)['regions'].push(id);
    },
    remove_region: function(x,y,id)
    {
        var regions = this.get(x,y)['regions'];
        for (var i in regions)
        {
            if (regions[i] == id)    
            {
                this.get(x,y)['regions'].splice(i,1);
                break;
            }
        }
    },
    
    show: function(name) {
        $("#explore > div").hide();
        $("#explore-"+name).show();
    },
    tile_show: function(name) {
        $("#explore-tile-panes > div").hide();
        $("#explore-tile-"+name).show();  
    },
    tile_editor: function(x,y) {
    	$("#explore-regions").hide();
    	
        this.tile_show('edit');
        $("#save-explore-edit").attr('var1',x);
        $("#save-explore-edit").attr('var2',y);
        var data = this.get(x,y);
        $("#explore-edit-name").val(data['name']);
        $("#explore-edit-desc").val(data['desc']);
    },
    
    ux: -1,
    uy: -1,
    reset: function()
    {
        this.ux = -1;
        this.uy = -1;
    },
    display_main: function() {
        this.reset();
        this.show('main');
        map.highlight_all();
        regions.all_buttons("#explore-region-buttons",'display',true);
        $("#regions-h3").show();
        if ($.isEmptyObject(regions.data)) { $("#regions-h3").hide(); }
    },
    delete_region_buttons: function(x,y) {
        var r = this.get(x,y)['regions'];
        
        $("#delete-regions-wrapper").show();
        regions.buttons_by_keys("#delete-regions",r,'delete_tile_region');
        if (r.length == 0)
        {
            $("#delete-regions-wrapper").hide();
        }
    },
    display: function(x,y)
    {
        if (this.ux == x && this.uy == y)
        {
            this.display_main(); 
            return;
        }
        this.ux = x;
        this.uy = y;
        
        map.darken_all();
        map.highlight(x,y);
        this.show('tile');
        
        var d = this.get(x,y);
        $("#explore-name,#explore-desc,#explore-regions").hide();
        
        this.tile_show('display');
        
        if (d['name'])
        {
            $("#explore-name").html(d['name']).show();   
        }
        
        if (d['desc'])
        {
            $("#explore-desc").html(d['desc']).show();
        }
        
        $("#explore-edit").attr('var1',x);
        $("#explore-edit").attr('var2',y);    
        
        $("#delete-regions-wrapper").hide();
        if (d['regions'].length)
        {
            $("#explore-regions").show();
            regions.buttons_by_keys("#explore-regions > span",d['regions'],'display');
            this.delete_region_buttons(x,y);
            $("#delete-regions-wrapper").show();
        }
        
        content.render(x,y);  
    }
};

// A low-level object that handles content rules and their associated menus.
var content = {
    data: {
        terrain: { },
        fg: { },
        region: { }
    },
    
    add_rule: function(entype,entid,ruleid,data) {
        if (typeof this.data[entype][entid] == 'undefined')
        {
            this.data[entype][entid] = {};
        }
        this.data[entype][entid][ruleid] = data;
    },
    edit_rule: function(entype,entid,ruleid, data) {
        var ruletype = data['ruletype'];
        if (ruletype != 'numeric' && ruletype != 'items')
        {
            delete data['min'];
            delete data['max'];    
        }
        
        entries = this.data[entype][entid][ruleid]['entries'];
		
		if (ruletype == 'items')
        {
        	if (typeof entries == 'undefined') 
        	{
        		entries = [];
        	}
            data['entries'] = entries;
        }
        else if (typeof entries != 'undefined')
        {
        	data['entries'] = entries;
        }
        
        this.data[entype][entid][ruleid] = data;
    },
    delete_rule: function(entype,entid,ruleid) {
        delete this.data[entype][entid][ruleid];
    },
    
    add_entry: function(entype,entid,ruleid,entryid,data) {
    	if (typeof this.data[entype][entid][ruleid]['entries'] == 'undefined')
    	{
    		this.data[entype][entid][ruleid]['entries'] = {};
    	}
        this.data[entype][entid][ruleid]['entries'][entryid] = data;
    },
    edit_entry: function(entype,entid,ruleid,entryid,data) {
        this.data[entype][entid][ruleid]['entries'][entryid] = data;
    },
    delete_entry: function(entype,entid,ruleid,entryid) {
        delete this.data[entype][entid][ruleid]['entries'][entryid];
    },
    
    traits: [],
    do_trait: function(rule) {
        this.traits.push(rule['name']);
    },
    do_numeric: function(rule) {
        var val = rand.range(rule['min'],rule['max']);
        this.output(rule['name'],val);
    },
    do_items: function(rule) {
        var amt = rand.range(rule['min'],rule['max']);
        var entries = mixed_clone(rule['entries']);
        var list = [];
        var entry = {};
        for (var i = 0; i < amt; i++)
        {
            entry = rand.weigh(entries);
            if (entry) 
            {
                list.push(entry['name']);
            }     
        }
        this.output(rule['name'],list);
    },
    
    output: function(cat,values) {
        if (typeof values == 'object')
        {
            values = values.join(', ');
        }
        if (values.length == 0)
        {
            values = 'N/A';
        }
        
        $("#explore-rules").append('<tr><td>'+cat+'</td><td>'+values+'</td></tr>');                    
    },
    
    run: function(ruletype,id) {
        var rules = this.data[ruletype][id];
        if (typeof rules == 'undefined') { return; }
        
        var rule, ruleseed;
        
        for (var i in rules)
        {
            rule = rules[i];
            
            if (!rand.chance(rule['chance'])) { continue; }
            ruleseed = this.xyseed+rule['seed'];
            rand.seed(ruleseed);
            this['do_'+rule['ruletype']](rule);
        }
    },
    
    xyseed: 0,
    
    render: function(x,y) {
        $("#explore-rules").html('');
        this.traits = [];
        this.xyseed = (x*y)+x+y;
        
        var fg = map.get_fg(x,y);
        var terrain = map.get_tile(x,y);
        var regions = explore.get(x,y)['regions'];
        
        this.run('fg',fg);
        this.run('terrain',terrain);
        for (var i in regions)
        {
            this.run('region',regions[i]);
        }
        
        if (this.traits.length)                    
        {
            this.output('Traits',this.traits);
        }
    },
    
    menu: function(name) {
        $("#rules > div").hide();
        $("#rules-"+name).show();
    },
    
    entype: '',
    entid: '',
    ruleid: '',
    panel: '',
    choose_entype: function(entype,entid) {
        this.panel = 'rules'; 
        $("#rules-back").show();
        
        this.entype = entype;
        this.entid = entid;
        $("#add-rules").val('');
        this.menu('rules');
        
        var data = this.data[entype][entid];
        
        // fix trait min/max
        for (var i in data)
        {
        	if (data[i]['ruletype'] == 'trait')
        	{
        		delete data[i]['min'];
        		delete data[i]['max'];
        	}
        }
        
        table.build('#rules-edit',data);
        
        bind_ruletype();
        bind_buttons();
    },
    edit_entries: function(ruleid) {
        this.panel = 'entries';
        $("#rules-back").show();
        
        this.ruleid = ruleid;
        $("#add-entries").val('');
        this.menu('entries');
        
        var data = this.data[this.entype][this.entid][ruleid]['entries'];
        table.build('#entries-edit',data);
        
        bind_buttons();
    },
    refresh: function(type) {
        if (type == 'rules')
        {
            this.choose_entype(this.entype,this.entid);
        }
        else if (type == 'entries')
        {
            this.edit_entries(this.ruleid);
        }
    },
    message: function(message) {
        var el = $('#top-message');
        if ($("#controls").hasClass('pos-top') && !$("#controls").hasClass('unfixed')) 
        {
            el = $('#bottom-message');
        }
        el.show();
        el.html(message);
        el.fadeOut(2500);
    },
    back: function() {
        var panel = this.panel;
        if (panel == 'rules')
        {
            panes.rules();
        }
        else if (panel == 'entries')
        {
            this.refresh('rules');
        }
        
    }
};

// A low-level object that builds tables for various menus.
var table = {
    reset: function(el) {
        $(el).find('.generated').remove();
    },
    bind: function() {
        $(".rules-entries-link").click(function(e) {
            e.preventDefault();
            var key = $(this).closest('tr').attr('key');
            content.edit_entries(key);
        });
    },
    row: function(el,key,data) {
        var row = $('<tr class="generated key-'+key+'" key="'+key+'">'+$(el).find('.template').html()+'</tr>'); 
        $(el).append(row);
        
        row.find('td').each(function() {
            var fname = $(this).attr('fname');
            if (!fname) { return; }
            
            var ftype = $(this).attr('ftype');
            var f = $(this).find('.f');
            
            if (typeof data[fname] != 'undefined') 
            {
                table['do_'+ftype](f,data[fname],$(this).parent().attr('key'));
            }
            else
            {
                f.hide();
            }
        });
    },
    build: function(el,data) {
        this.reset(el);
        if (typeof data == 'undefined' || $.isEmptyObject(data))
        {
            $(el).parent().hide();
            return;
        }
        
        $(el).parent().show();
        
        for (var i in data)
        {	
            this.row(el,i,data[i]); 
        }
        this.bind();
    },
    
    do_input: function(el,val) {
        el.val(val);
    },
    do_chance: function(el,val) {
        if (!val) { val = 100; }
        this.do_input(el,val);
    },
    do_numeric: function(el,val) {
        if (!val) { val = 0; }
        this.do_input(el,val);
    },
    do_delete: function(el,val,key) {
        el.val('Delete '+val);
        el.attr('var1',key);
    },
    
    do_entries_link: function(el,val) {
        el.html('Edit Entries');
    },
};

// This object manages what happens every time you click on a specific terrain or foreground inside a menu. It also generates these tile
// links and binds them appropriately. Different things happen depending on the "Edit Map" mode or if you're picking a content rule.
var tiles = {
    data: {
        terrain: [],
        fg: []
        
    },
    dirs: {
        terrain: 'terrain',
        fg: 'ents'
    },
    bind: function(el,func) {
        $(el+" > img").unbind().click(function() { tiles['do_'+func]($(this)); });
    },
    img: function(el,src,tile) {
        $(el).append('<img src="'+src+'" tile="'+tile+'" id="tile-'+tile+'" />');
    },
    render: function(el,type,func) {
        $(el).html('');
        var data = this.data[type];
        var dir = this.dirs[type];
        for (var i in data)
        {
            this.img(el,'/map/__images/'+dir+'/'+data[i]+'.png',data[i]);
        }
        
        for (var i in custom_tiles[type])
        {
            this.img(el,custom_tiles[type][i],i);
        }
        
        this.bind(el,func);  
    },
    
    do_editor: function(el) {
        var p = el.parent();
        render.init(['map',p.attr('function'),el.attr('tile'),p.attr('condition'),p.attr('function2')]);
        if (tools.mode == 'point')
        {
            $(".fg-sel .sel,.terrain-sel .sel").removeClass('sel');
            el.addClass('sel');
            return;
        }
        render.apply_to_darkened();
        map.highlight_all();
    },
    
    do_content_choose_entype: function(el) {
        var type = el.parent().attr('type');
        content.choose_entype(type,el.attr('tile'));
    }
};

// This object initializes data to various low-level objects when the program first loads.
var init = {
    render: function(data) {
        var subdata;
        for (var i in data)
        {
        	subdata = data[i]['data'];
        	if (subdata.length == 0) { continue; }
        	
        	this[data[i]['init']](i,subdata);
        }
    },
    data: function(obj,data)
    {
    	window[obj].data = data;
    },
    obj: function(name,data)    
    {
    	window[name] = data;
    }
};

// This object is used for changes that happen on the user's end. Any change a user makes will be pushed through this object, which 
// will make changes locally and then push the same set of changes to everyone online.
var client = {
	ajax: function(data) { 
		ajax.push('publish',data,function(e) { });
	},
	publish: function(obj,func,var1,var2,var3,var4) {
		window[obj][func](var1,var2,var3,var4);
		var data = {
			obj: obj,
			func: func,
			var1: var1,
			var2: var2,
			var3: var3,
			var4: var4
		};
		this.ajax(data);
	},
	run: function(func,var1,var2,var3,var4)
	{
		return this.publish('client','do_'+func,var1,var2,var3,var4);
	},
	
	batch: function(batch_func,batch_data,obj,func,var1,var2,var3,var4,var5) {
		
		batch[batch_func](batch_data,obj,func,var1,var2,var3,var4,var5);
		
		var data = { 
			obj: obj,
			func: func,
			batch_func: batch_func,
			batch_data: JSON.stringify(batch_data),
			var1: var1,
			var2: var2,
			var3: var3,
			var4: var4,
			var5: var5
		};
		this.ajax(data);
	},
	render: function(data) {
		if (data['batch_func'])
		{
			batch[data['batch_func']](JSON.parse(data['batch_data']), data['obj'],data['func'], data['var1'],data['var2'],data['var3'],data['var4'],data['var5']);
		}
		else
		{
			window[data['obj']][data['func']](data['var1'],data['var2'],data['var3'],data['var4'],data['var5']);
		}
	},
	
	remap: function(coords,obj,func) {
		return this.batch('coords',coords,obj,func);
	},
	
	// ---
	do_add_image(type,name,url) {
        $("#edit-"+type+"-imgs").append('<img style="width: 20px; height: 20px;" src="'+url+'" tile="'+name+'" />');
        tiles.bind("#edit-"+type+"-imgs",'editor');
        custom_tiles[type][name] = url;
	},
	do_add_to_region(x,y,id) {
        regions.add_point(id,x,y);
        explore.add_region(x,y,id);
	},
	do_delete_tile_region(id,x,y) {
	    regions.remove_point(id,x,y);
	    explore.remove_region(x,y,id);
	    explore.delete_region_buttons(x,y);    
	}
};

// This object handles local changes (from the client object above) that are the same function applied to a large amount of 
// data (usually coordinates).
var batch = {
	coords: function(coords,obj,func) {
		return this.coords_custom(coords,obj,func);
	},
	coords_custom: function(coords,obj,func,var1,var2,var3,var4,var5) { 
		var x,y;
		for (var i in coords)
		{
			x = coords[i][0];
			y = coords[i][1];
			window[obj][func](x,y,var1,var2,var3,var4,var5);
		}
	},
	parse_vars: function(key,val,vars) {
		for (var i in vars)
		{
			if (vars[i] == '$K') 
			{
				vars[i] = key;
			}
			else if (vars[i] == '$V') 
			{
				vars[i] = val;
			}
		}
		return vars;
	},
	paired_list: function(list,obj,func,var1,var2,var3,var4,var5) {
		var vars;
		for (var i in list)		
		{
			vars = this.parse_vars(i,list[i],[var1,var2,var3,var4,var5]);
			window[obj][func](vars[0],vars[1],vars[2],vars[3],vars[4]);
		}
	}
};
   
$("#add-img-url").keyup(function() {
    var val = $(this).val();
    if (val)
    {
        $("#add-img-preview").html('<img src="'+val+'" />');
    }
});

$(".tile").click(function() {
    x = parseInt($(this).attr('x'));
    y = parseInt($(this).attr('y'));
    window[tools.obj][tools.func](x,y);
});
   
$(".tile").mouseover(function() {
    var x = parseInt($(this).attr('x'));
    var y = parseInt($(this).attr('y'));
    select.draw(x,y);
});

$("#tools a").click(function(e) {
	$("#add-img").hide();
	$("#alt-region").hide();
    e.preventDefault();
    $("#tools a.sel").removeClass('sel');
    $(this).addClass('sel');
    tools.init($(this).attr('tool'));
});

$(".switch-button").click(function() {
    var switchto = $(this).attr('switchto');
    var val = $(this).val();
    $(this).attr('switchto',val);
    $(this).attr('value',switchto);
    $(this).toggleClass('sel');    
});

$("#menu a").click(function(e) {
    e.preventDefault();

    var pane = $(this).attr('pane');
    var alt_function = $(this).attr('alt_function');
    if (pane)
    {
    	$("#alt-region").hide();
        $("#menu a.menusel").removeClass('sel');
        $(this).addClass('sel');
        $("#panes .pane").hide();
        $("#"+pane).show();
        panes[pane]();
    }
    else
    {
        menu_alt[alt_function]($(this));
    }
});

$("#rules-back").click(function(e) {
    e.preventDefault();
    content.back();
});

$("#alt-region-delete input").change(function() {
	$(this).parent().toggleClass('sel');
});

rand.seed(_seed);
init.render(JSON.parse(_init_data));

tiles.render("#edit-terrain-imgs",'terrain','editor');

tiles.render("#edit-fg-imgs",'fg','editor');
$("#edit-fg-imgs").prepend($('<img src="/map/__images/terrain/_blank0.png" tile="" class="round" />'));

tiles.bind('#edit-fg-imgs','editor');

timer.start();