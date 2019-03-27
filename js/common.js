var rand = {
    rng: {},
    seed: function(seed) {
        if (typeof seed == 'undefined') { seed = _seed; }
    	seed = parseInt(seed);
    	this.rng = new MersenneTwister(seed);
	},
    range: function(min,max) {
        min = parseInt(min);
        max = parseInt(max);
        var num = Math.floor(this.rng.genrand_res53() * (max-min+1));
        num += min;
        return num;
    },
    chance: function(chance) {
        chance = parseInt(chance);
        var val = this.range(1,100);
        if (val <= chance)
        {
            return true;
        }
    },
    pick: function(mixed) {
    	if (mixed instanceof Array)
        {
        	var key = this.range(0,mixed.length-1);
        	return mixed[key];
        }
        else if (mixed instanceof Object)
        {
        	var keys = [];
        	for (var i in mixed)
        	{
        		keys.push(i);
        	}
        	var key = this.range(0,keys.length-1);
        	return mixed[keys[key]];
        }
    },
    weigh: function(obj) {
        var max = 0;
        
        if (obj.length == 0) { return; }
        
        for (var a in obj)
        {
            max += parseInt(obj[a]['weight']);
        }
        
        var rand = this.range(1,max);
        
        var index = 0;
        var entry;
        
        for (var b in obj)
        {
            index += parseInt(obj[b]['weight']);
            if (index >= rand)
            {
                entry = obj[b];
                obj.splice(b,1);
                return entry;
            }
        }
    }
};