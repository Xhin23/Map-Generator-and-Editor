_shift = 0;

function add_to_ratio(ratio,amt)
{
    ratio = ratio.split('/');
    var min = parseInt(ratio[0]);
    var max = parseInt(ratio[1]);
    
    if (!_shift)
    {
        min += amt;
        if (min < 0) { min = 0; }
    }
    else
    {
        max += amt;
        if (max < 1) { max = 1; }
    }
    
    if (min > max) { min = max; }
    
    ratio = min+'/'+max;
    return ratio;
}


function map_rescale(size) {
    $(".map img").css('height',size).css('width',size);
}

function rand_ratio(el)
{
    var max = 1 + Math.floor(Math.random() * 20);
    var min = 1 + Math.floor(Math.random() * max);

    el.val(min+'/'+max);
}
    
$("#random-seed").click(function() {
    $("#map-form").submit(function() {
        $(this).find('input[name=seed]').val('');
    });
    
    $("#map-form").submit();
});

$("#random-ratios").click(function() {
    $("#map-form").submit(function(e) {
        var hold = [];
        $(this).find('input').each(function() {
            if ($(this).attr('ratio') == 1) { 
                if (!$(this).closest('tr').find('.switch-button').hasClass('sel'))
                {
                    rand_ratio($(this));          
                }
                else
                {
                    hold.push($(this).attr('name'));
                }
            }
        });
        $("#hold-ratios").val(hold.join(','));
    });
    
    $("#map-form").submit();
});

$("#reset").click(function() {
    document.location.href = '/map'; 
});

$("#reset-dims").click(function() {
    $("input[name=height],input[name=width],input[name=image_size]").each(function() {
        $(this).val($(this).attr('init'));
    });
    $("#map-form").submit();
});

$(document).on('keyup keydown', function(e) {
    if (e.which == 16)
    {
        _shift = (e.type == 'keydown') ? 1 : 0;
    }
});

$("#map-form input").keyup(function(e) {
    if (e.which != 38 && e.which != 40) { return; }

    var value = $(this).val();
    
    var amt = (e.which == 38) ? 1 : -1;
    
    if (value.indexOf('/') != -1)
    {
        value = add_to_ratio(value,amt);
    }
    else
    {
        value = parseInt(value);
        value += amt;
    }
    
    $(this).val(value);
    if ($(this).attr('name') == 'image_size')
    {
        map_rescale($(this).val());
    }
});

$(".rand-field").click(function() {
    var el = $(this).parent().parent().find('.input');
    rand_ratio(el);
});

// ---

$("#save").click(function() {
    $("#interface > div").hide();
    $("#save-form").fadeIn();
});

$("#name").keyup(function() {
    var val = $(this).val();
    val = val.toLowerCase();
    val = val.replace(/[^A-Za-z0-9 _]/g,'');
    val = val.replace(/ /g,'_');
    val = val.replace(/__/g,'_');
    $("#slug").val(val);
});

$("#save-form form").submit(function() {
    var arr = [];
    
    $("#map-form .input").each(function() {
        arr['settings['+$(this).attr('name')+']'] = $(this).attr('value');
    });
    
    var disable = [];
    $("#advanced-settings input").each(function() {
        if ($(this).prop('checked'))
        {
            disable.push($(this).val());
        }
    });
    if (disable.length)
    {
        arr['disable'] = disable.join(',');
    }
    
    for (x in arr)
    {
        $(this).prepend('<input type="hidden" name="'+x+'" value="'+arr[x]+'" />');
    }
});

$("#advanced").click(function(e) { 
    e.preventDefault();
    $("#basic-settings").hide();
    $("#advanced-settings").show();
});

$("#basic").click(function(e) { 
    e.preventDefault();
    $("#advanced-settings").hide();
    $("#basic-settings").show();
});

$("input[type=checkbox]").change(function() {
    $(this).parent().toggleClass('sel');
});


$("input[name=image_size]").change(function() {
    map_rescale($(this).val());
});

$(".toggle-down").click(function() {
    var unselect = $(this).closest('tr').find('label').hasClass('sel');
    var row = parseInt($(this).attr('row'));
    $("#advanced-settings label").each(function() {
        if (parseInt($(this).attr('row')) >= row)
        {
            if (unselect)
            {
                $(this).removeClass('sel');
                $(this).find('input').prop('checked',false);
            }
            else
            {
                
                $(this).addClass('sel');
                $(this).find('input').prop('checked',true);
            }
        }
    });
});

$(".switch-button").click(function() {
    $(this).toggleClass('sel');
});

$("#toggle-sculpt").click(function(e) {
    e.preventDefault();
    var val = parseInt($("#show-sculpt").val());
    $("#show-sculpt").val(1-val);
    $(".sculpt").toggle();
});

$("#sculpt-dist-button").click(function() {
    var dist = parseInt($("#sculpt-dist").val());
    var len = $(".sculpt-input input").length;
    $(".sculpt-input input").val(0);
    for (var i = 0; i < dist; i++)
    {
        var el = $($(".sculpt-input input")[rand.range(0,len-1)]);
        var val = parseInt(el.val());
        val++;
        el.val(val);
    }
    $("#map-form").submit();
});

rand.seed(_seed);