$.fn.extend({
    tab: function(t) {
        function e(t) {
            s.eq(t).addClass(n.cls).siblings().removeClass(n.cls),
            o.eq(t).addClass(n.cls).siblings().removeClass(n.cls),
            "function" === $.type(n.callback) && n.callback.call(i, t)
        }
        var n = $.extend({
            nav: ".tab-nav",
            pane: ".tab-content .tab-pane",
            delegater: "a",
            cls: "active",
            evt: "mouseenter",
            callback: null
        },
        t),
        i = this,
        a = i.find(n.nav),
        s = a.find(n.delegater),
        o = i.find(n.pane),
        r = a.find("." + n.cls),
        l = 0;
        return this.length ? (r.length && (l = r.index()), e(l), a.delegate(n.delegater, n.evt,
        function(t) {
            t.preventDefault();
            var n = $(this);
            l = n.index(),
            e(l)
        }), "click" == n.evt || a.delegate(n.delegater, "click",
        function(t) {
            t.preventDefault()
        }), this) : this
    },
    dropdown: function(t) {
        var e = $.extend({
            evt: "click"
        },
        t),
        n = this;
        return "hover" == e.evt ? (n.bind("mouseenter",
        function() {
            n.addClass("open")
        }), n.bind("mouseleave",
        function() {
            n.removeClass("open")
        })) : n.find(".trigger").bind(e.evt,
        function(t) {
            t.preventDefault(),
            n.toggleClass("open")
        }),
        "click" == e.evt && ($global = $(document), $global.data("dropBinded") || ($global.bind("click",
        function(t) {
            var e = $(t.target);
            e.closest(".dropdown").length || $(".dropdown.open").removeClass("open")
        }), $global.data("dropBinded", "true"))),
        this
    },
    affix: function(t) {
        if (!this.length) return this;
        var e = $.extend({
            top: 0,
            bottom: 0,
            scrollTop: null,
            maxTop: null,
            oHeight: null
        },
        t),
        n = this,
        i = n.get(0).offsetParent || document.body,
        i = "html" == i.tagName.toLowerCase() ? document.body: i,
        a = $(i),
        s = a.length ? a.height() : $("body").height(),
        o = e.oHeight || n.height(),
        r = e.maxTop ? e.maxTop - o: 0,
        l = document.body.scrollTop || document.documentElement.scrollTop,
        u = n.offset().top;
        if ((e.scrollTop || l >= u - e.top - e.bottom) && (e.maxTop && l >= r - e.bottom ? r > u && n.addClass("affix-bottom").css({
            top: r
        }) : e.scrollTop ? l >= e.scrollTop && n.addClass("affix") : n.addClass("affix")), "undefined" == typeof document.body.style.maxHeight) {
            var c = tuan800ued.getModule("ie6Fixed"),
            d = $(window).height();
            c && c.fixed(n, (d - o) / 2)
        } else $(window).bind("scroll",
        function() {
            var t = document.body.scrollTop || document.documentElement.scrollTop;
            if (a.height() != s) {
                var i = a.height() - s;
                s = a.height(),
                r += i
            }
            e.scrollTop || t >= u - e.top ? e.maxTop && t >= r - e.bottom ? r > u && (n.hasClass("affix-bottom") ? 1 * n.css("top").match(/\d+/) != r && n.css("top", r) : n.removeClass("affix").addClass("affix-bottom").css({
                top: r
            })) : e.scrollTop ? t >= e.scrollTop ? n.hasClass("affix") || n.addClass("affix").removeClass("affix-bottom").css({
                top: ""
            }) : n.hasClass("affix") && n.removeClass("affix") : n.hasClass("affix") || n.addClass("affix").removeClass("affix-bottom").css({
                top: ""
            }) : n.removeClass("affix").removeClass("affix-bottom").css("top", "")
        });
        return this
    },
    slide: function(t) {
        function e() {
            d <= a.num ? (l.hide(), u.hide()) : r.clone().appendTo(o)
        }
        function n(t, e) {
            f.n = void 0 !== t ? t: f.n,
            f.n > p && (f.n = 1, o.css("margin-left", 0)),
            f.n < 0 && (f.n = p - 1, o.css("margin-left", -p * c)),
            "instance" === e ? (o.css({
                marginLeft: -f.n * c * a.num
            }), f.n == p && o.css("margin-left", 0)) : o.stop().animate({
                marginLeft: -f.n * c * a.num
            },
            a.speed,
            function() {
                f.n >= p && o.css("margin-left", 0)
            })
        }
        var i, a = $.extend({
            container: "ul",
            item: "li",
            prev: ".prev",
            next: ".next",
            num: 1,
            speed: 500,
            auto: !0,
            time: 3e3
        },
        t),
        s = this,
        o = s.find(a.container),
        r = o.find(a.item),
        l = s.find(a.prev),
        u = s.find(a.next),
        c = r.outerWidth(!0),
        d = r.length,
        p = Math.ceil(d / a.num),
        h = 0,
        f = {
            n: 0,
            go: n
        };
        return e(),
        l.bind("click",
        function(t) {
            t.preventDefault(),
            f.n--,
            n()
        }),
        u.bind("click",
        function(t) {
            t.preventDefault(),
            f.n++,
            n()
        }),
        a.auto && (i = setInterval(function() {
            n(h++)
        },
        a.time), $.each([l, u],
        function() {
            $(this).bind("click",
            function() {
                clearInterval(i),
                i = setInterval(function() {
                    n(f.n++)
                },
                a.time)
            })
        })),
        this.exports = f,
        this
    },
    tooltip: function(t) {
        var e = $.extend({
            direction: "top",
            evt: "default",
            close: !0,
            offset: null,
            callback: null,
            msg: "",
            clz: ""
        },
        t),
        n = this,
        i = $('<div class="tooltip ' + e.clz + '"><div class="tooltip-content">' + e.msg + (e.close ? '<i class="close"></i>': "") + '</div><div class="tooltip-arrow"></div></div>').insertAfter(n),
        a = n.position(),
        s = i.find(".tooltip-arrow"),
        o = n.outerWidth(),
        r = n.outerHeight(),
        l = i.width(),
        u = i.height();
        if (n.length) {
            switch (e.direction) {
            case "top":
                i.css({
                    top:
                    a.top - u - s.height(),
                    left: a.left - (l - o) / 2
                });
                break;
            case "left":
                i.css({
                    left:
                    a.left - l - s.width(),
                    top: a.top + (r - u) / 2
                }),
                s.addClass("right");
                break;
            case "right":
                i.css({
                    left:
                    a.left + o + s.width(),
                    top: a.top + (r - u) / 2
                }),
                s.addClass("left");
                break;
            case "bottom":
                i.css({
                    top:
                    a.top + r + s.height(),
                    left: a.left - (l - o) / 2
                }),
                s.addClass("up")
            }
            switch (e.offset && i.css(e.offset), e.evt) {
            case "hover":
                i.hide(),
                n.unbind("mouseenter.tooltip").unbind("mouseleave.tooltip").bind("mouseenter.tooltip",
                function() {
                    i.show()
                }).bind("mouseleave.tooltip",
                function(t) {
                    var n = t.pageX,
                    a = t.pageY,
                    o = i.offset();
                    switch (e.direction) {
                    case "left":
                        n > o.left + l + s.width() && i.hide();
                        break;
                    case "top":
                        a > o.top + u + s.height() && i.hide();
                        break;
                    case "right":
                        n < o.left - s.width() && i.hide();
                        break;
                    case "bottom":
                        a < o.top - s.height() && i.hide()
                    }
                }),
                i.unbind("mouseleave.tooltip").bind("mouseleave.tooltip",
                function(t) {
                    var n = t.pageX,
                    a = t.pageY,
                    s = i.offset();
                    switch (e.direction) {
                    case "left":
                        n < s.left + l && i.hide();
                        break;
                    case "top":
                        a < s.top + u && i.hide();
                        break;
                    case "right":
                        n > s.left && i.hide();
                        break;
                    case "bottom":
                        a > s.top && i.hide()
                    }
                });
                break;
            case "click":
                n.unbind("click.tooltip").bind("click.tooltip",
                function() {
                    i.toggle()
                })
            }
            if (i.find(".close").bind("click",
            function() {
                i.remove()
            }), "function" === $.type(e.callback)) try {
                e.callback.call(i, null)
            } catch(c) {
                console && console.log(c)
            }
            return this
        }
    },
    marquee: function(t) {
        return this.each(function() {
            function e() {
                u.append(u.html())
            }
            function n(t, e, n) {
                return setInterval(function() {
                    var n = +/\-?\d+/.exec(t.css(e))[0];
                    return - s >= n ? (t.css(e, 0), void 0) : (t.css(e, n - 1), void 0)
                },
                n)
            }
            var i, a, s, o, r = $.extend({
                container: "ul",
                item: "li",
                type: " default",
                pause: !0,
                fps: 18
            },
            t),
            l = $(this),
            u = l.find(r.container),
            c = l.find(r.item),
            d = c.length,
            p = c.outerWidth(!0),
            h = c.outerHeight(!0),
            f = 1e3 / r.fps;
            "vertical" === r.type ? (i = "height", a = "marginTop", s = h * d) : (i = "width", a = "marginLeft", s = p * d),
            e(),
            o = n(u, a, f),
            r.pause && u.bind("mouseenter.marquee",
            function() {
                clearInterval(o)
            }).bind("mouseleave",
            function() {
                o = n(u, a, f)
            })
        }),
        this
    },
    
    placeholder: function(t) {
        var e = {
            pColor: "#999",
            pFont: "12px",
            posL: 0,
            zIndex: "99"
        },
        n = $.extend(e, t);
        return this.each(function() {
            if (! ("placeholder" in document.createElement("input"))) {
                $(this).parent().css("position", "relative");
                var t = $.browser.msie,
                e = $.browser.version,
                i = $(this),
                a = i.attr("placeholder"),
                s = i.outerHeight(),
                o = i.outerWidth(),
                r = i.position().left,
                l = i.position().top,
                u = $("<label>", {
                    "class": "test",
                    text: a,
                    css: {
                        position: "absolute",
                        left: r + "px",
                        top: l + "px",
                        width: o - n.posL + "px",
                        "padding-left": n.posL + "px",
                        height: s + "px",
                        "line-height": s + "px",
                        color: n.pColor,
                        "font-size": n.pFont,
                        "z-index": n.zIndex,
                        cursor: "text"
                    }
                }).insertBefore(i),
                c = i.val();
                if (c.length > 0 && u.hide(), t && 9 > e) var d = "propertychange";
                else var d = "input";
                i.on("focus",
                function() {
                    var t = $(this).val();
                    t.length > 0 && u.hide()
                }).on("blur",
                function() {
                    var t = $(this).val();
                    0 === t.length && u.show()
                }).on(d,
                function() {
                    var t = $(this).val();
                    0 === t.length ? u.show() : u.hide()
                }),
                u.on("click",
                function() {
                    i.trigger("focus")
                }),
                i.filter(":focus").trigger("focus")
            }
        })
    }
}),
function() {} (jQuery),
!function(e) {
    tuanpub.addModule("dealbinds", {
    })
} (jQuery),
function(e) {
    tuanpub.addModule("pinpaiVerA", {
        init: function() {
            e(".brands_logo").slide({
                auto: !1
            }),
            e(".brandbox .bd").each(function() {
                var n = e(this),
                t = n.find(".bar");
                0 == t.length && n.css("border-bottom", "none")
            })
        },
        template: function() {
            return tuanpub.getModule("dealbinds").dealtpl
        } (),
        parseDeal: function(n) {
            return tuanpub.getModule("dealbinds").parseDealData(n),
            e.ParseTpl(this.template, n)
        },
        connect: function(n) {
            for (var t = n.length,
            a = new e.Buffers,
            r = 0; t > r; r++) a.push(this.parseDeal(n[r]));
            return a.toString()
        }
    })
} (jQuery);