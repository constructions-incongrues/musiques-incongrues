(function(a){a.yellowFade={fadeTo:"#ff9",duration:2800,repeat:1,selector:"a",init:function(){var b=a.yellowFade;a(window).load(b.onWindowLoad);a(b.selector).click(b.onClick)},onWindowLoad:function(){a.yellowFade.highlight(unescape(window.location))},onClick:function(){var b=a.yellowFade;a(window).unbind("load",b.onWindowLoad);b.highlight(a(this).attr("href"))},highlight:function(d){var b=d.indexOf("#"),c=a.yellowFade,f=c.duration/c.repeat,g;if(b>-1){g=d.substring(b);for(var e=0;e<c.repeat;e++){a(g).effect("highlight",{color:c.fadeTo},f)}}}};a(a.yellowFade.init)})(jQuery.noConflict());