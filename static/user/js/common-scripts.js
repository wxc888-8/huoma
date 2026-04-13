var Script = function () {

// 添加被动事件监听器支持
jQuery.event.special.touchstart = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchstart", handle, { passive: true });
    }
};
jQuery.event.special.touchmove = {
    setup: function( _, ns, handle ) {
        this.addEventListener("touchmove", handle, { passive: true });
    }
};
jQuery.event.special.wheel = {
    setup: function( _, ns, handle ) {
        this.addEventListener("wheel", handle, { passive: true });
    }
};
jQuery.event.special.mousewheel = {
    setup: function( _, ns, handle ) {
        this.addEventListener("mousewheel", handle, { passive: true });
    }
};
jQuery.event.special.scroll = {
    setup: function( _, ns, handle ) {
        this.addEventListener("scroll", handle, { passive: true });
    }
};
jQuery.event.special.DOMMouseScroll = {
    setup: function( _, ns, handle ) {
        this.addEventListener("DOMMouseScroll", handle, { passive: true });
    }
};

//    sidebar dropdown menu

    jQuery('#sidebar .sub-menu > a').click(function () {
        var last = jQuery('.sub-menu.open', $('#sidebar'));
        last.removeClass("open");
        jQuery('.arrow', last).removeClass("open");
        jQuery('.sub', last).slideUp(200);
        var sub = jQuery(this).next();
        if (sub.is(":visible")) {
            jQuery('.arrow', jQuery(this)).removeClass("open");
            jQuery(this).parent().removeClass("open");
            sub.slideUp(200);
        } else {
            jQuery('.arrow', jQuery(this)).addClass("open");
            jQuery(this).parent().addClass("open");
            sub.slideDown(200);
        }
        var o = ($(this).offset());
        diff = 200 - o.top;
        if(diff>0)
            $("#sidebar").scrollTo("-="+Math.abs(diff),500);
        else
            $("#sidebar").scrollTo("+="+Math.abs(diff),500);
    });

//    sidebar toggle


    $(function() {
        function responsiveView() {
            var wSize = $(window).width();
            if (wSize <= 768) {
                $('#container').addClass('sidebar-close');
                $('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                $('#container').removeClass('sidebar-close');
                $('#sidebar > ul').show();
            }
        }
        $(window).on('load', responsiveView);
        $(window).on('resize', responsiveView);
    });

    $('.icon-reorder').click(function () {
        if ($('#sidebar > ul').is(":visible") === true) {
            $('#main-content').css({
                'margin-left': '0px'
            });
            $('#sidebar').css({
                'margin-left': '-180px'
            });
            $('#sidebar > ul').hide();
            $("#container").addClass("sidebar-closed");
        } else {
            $('#main-content').css({
                'margin-left': '180px'
            });
            $('#sidebar > ul').show();
            $('#sidebar').css({
                'margin-left': '0'
            });
            $("#container").removeClass("sidebar-closed");
        }
    });

// custom scrollbar - 使用更安全的方式初始化NiceScroll
    try {
        // 为NiceScroll设置更多选项以支持被动事件
        var niceScrollOptions = {
            styler: "fb",
            cursorcolor: "#e8403f", 
            cursorwidth: '3', 
            cursorborderradius: '10px', 
            background: '#404040', 
            cursorborder: '',
            smoothscroll: true,
            preventmultitouchscrolling: false, // 允许多点触控
            touchbehavior: true,              // 开启触摸行为
            oneaxismousemode: "auto"          // 自动检测滚动轴
        };
        
        var htmlScrollOptions = {
            styler: "fb",
            cursorcolor: "#e8403f", 
            cursorwidth: '6', 
            cursorborderradius: '10px', 
            background: '#404040', 
            cursorborder: '', 
            zindex: '1000',
            smoothscroll: true,
            preventmultitouchscrolling: false,
            touchbehavior: true,
            oneaxismousemode: "auto"
        };
        
        if ($.fn.niceScroll) {
            $("#sidebar").niceScroll(niceScrollOptions);
            $("html").niceScroll(htmlScrollOptions);
        }
    } catch (e) {
        console.warn("NiceScroll初始化失败:", e);
    }

// widget tools

    jQuery('.widget .tools .icon-chevron-down').click(function () {
        var el = jQuery(this).parents(".widget").children(".widget-body");
        if (jQuery(this).hasClass("icon-chevron-down")) {
            jQuery(this).removeClass("icon-chevron-down").addClass("icon-chevron-up");
            el.slideUp(200);
        } else {
            jQuery(this).removeClass("icon-chevron-up").addClass("icon-chevron-down");
            el.slideDown(200);
        }
    });

    jQuery('.widget .tools .icon-remove').click(function () {
        jQuery(this).parents(".widget").parent().remove();
    });

//    tool tips

    $(function() {
        // 确保在文档准备好后再初始化tooltip
        setTimeout(function() {
            // 兼容Bootstrap 5的tooltip初始化
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('.tooltips, [data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });
            } else if (typeof $.fn.tooltip === 'function') {
                $('.tooltips, [data-toggle="tooltip"]').tooltip();
            }
        }, 500); // 延迟初始化以确保DOM和Bootstrap完全加载
    });

//    popovers

    $(function() {
        // 确保在文档准备好后再初始化popover
        setTimeout(function() {
            // 兼容Bootstrap 5的popover初始化
            if (typeof bootstrap !== 'undefined') {
                var popoverTriggerList = [].slice.call(document.querySelectorAll('.popovers, [data-bs-toggle="popover"]'));
                popoverTriggerList.forEach(function(popoverTriggerEl) {
                    new bootstrap.Popover(popoverTriggerEl);
                });
            } else if (typeof $.fn.popover === 'function') {
                $('.popovers, [data-toggle="popover"]').popover();
            }
        }, 500); // 延迟初始化以确保DOM和Bootstrap完全加载
    });

// custom bar chart

    if ($(".custom-bar-chart")) {
        $(".bar").each(function () {
            var i = $(this).find(".value").html();
            $(this).find(".value").html("");
            $(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }


//custom select box

//    $(function(){
//
//        $('select.styled').customSelect();
//
//    });



}();