!function(t) {
	tuan800ued.addModule("shangjialist", {
		OnLoad: function() {
			t(".rightSJ li").each(function() {
				if (t.browser.msie && "6.0" == t.browser.version && !t.support.style) {
					var n = t(this).find(".sjimg img");
					n.width() / n.height() > 70 / 36 ? n.width(70) : n.height(36), t("#contentC li").each(function() {
						t(this).find("span").eq(0).width() > 215 && t(this).find("span").eq(0).width(215)
					})
				}
				t(this).hover(function() {
					t(this).addClass("on")
				}, function() {
					t(this).removeClass("on")
				})
			})
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("fuxuanlist", {
		OnLoad: function() {
			t("#contentD .l input").click(function() {
				window.location.href = t(this).attr("info")
			})
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("searchnull", {
		OnLoad: function() {
			t("#contentA .scnull h3 em").width() > 155 && t("#contentA .scnull h3 em").width(155).after("<em>...</em>"), t("#contentA .scnull form").submit(function() {
				return "换个检索词试一试么~" == t("#contentA .scnull .inp").val() || "" == t("#contentA .scnull .inp").val() ? (alert("请输入关键词"), !1) : void 0
			}), t("#contentD .r form").submit(function() {
				return "" == t("#contentD .r .inp").val() || "搜索最值商品" == t("#contentD .r .inp").val() ? (alert("请输入关键词"), !1) : void 0
			}), t(".rightSC form").submit(function() {
				return "" == t(".rightSC .inp").val() || "搜索最值商品" == t(".rightSC .inp").val() ? (alert("请输入关键词"), !1) : void 0
			})
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("ztdeallist", {
		OnLoad: function() {
			t(".deal img").lazyload(), t(".deal").each(function() {
				t(this).hover(function() {
					t(this).addClass("dealon")
				}, function() {
					t(this).removeClass("dealon")
				});
				var n = t(this).find("p").text(),
					e = "http://" + window.location.host +t(this).find("h4 a").attr("href"),
					i = t(this).find("img").attr("data-original");
				if ("/static/zhe800/images/load_deal.gif" == i && (i = ""), t(this).find("h6 em").length > 0 && t(this).find("h6 em").share({
					build: !0,
					title: encodeURI(n),
					url: encodeURI(e),
					pic: i,
					tencent: "qqwb"
				}), t(this).parents(".hengdeal").length) {
					var o = t(this).find("h5 span.fx"),
						a = t('<div class="dropdown">分享：</div>').appendTo(o);
					a.share({
						build: !0,
						title: encodeURI(n),
						url: encodeURI(e),
						pic: i,
						tencent: "qqwb"
					}), o.hover(function() {
						t(this).addClass("active")
					}, function() {
						t(this).removeClass("active")
					})
				} else t(this).find("h5 span.fx").length > 0 && t(this).find("h5 span.fx").share({
					build: !0,
					title: encodeURI(n),
					url: encodeURI(e),
					pic: i,
					tencent: "qqwb"
				})
			})
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("fenleigensui", {
		OnLoad: function() {
			if (t("#contentB .fblockA").length < 1) return !1;
			var n = t("#contentB .fblockA"),
				e = n.offset().top,
				i = t("#contentD .fblockB");
			t(window).scroll(function() {
				var t = 0 === document.body.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
				t > e ? n.hasClass("fblockA_f") || (n.addClass("fblockA_f"), i.addClass("fblockB_f")) : n.hasClass("fblockA_f") && (n.removeClass("fblockA_f"), i.removeClass("fblockB_f"))
			})
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("ztrightgud", {
		OnLoad: function() {
			if (t(".rightHD").length < 1) return !1;
			if (t("#contentA .left").height() < t("#contentA .right").height()) return !1;
			var n = t(".rightHD"),
				e = n.offset().top;
			t(window).scroll(function() {
				if (t("body .about").length > 0) var i = t("body .about").offset().top;
				var o = 0;
				t("#contentB .fblockA").length > 0 && (o = 50), t(".fblockB_f").length > 0 && (o = 42);
				var a = 0 === document.body.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
				a > e ? n.hasClass("zttb-sticky-tabbar") || (n.addClass("zttb-sticky-tabbar"), n.css("top", o), t.browser.msie && "6.0" == t.browser.version && !t.support.style && n.css("top", a + o)) : n.hasClass("zttb-sticky-tabbar") && n.removeClass("zttb-sticky-tabbar"), i && n.hasClass("zttb-sticky-tabbar") && i < a + n.height() + o ? (n.css("top", i - a - n.height() - 4), t.browser.msie && "6.0" == t.browser.version && !t.support.style && n.css("top", a + (i - a - n.height()))) : (n.css("top", o), t.browser.msie && "6.0" == t.browser.version && !t.support.style && n.css("top", a + o))
			})
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("ie6_maxWidth", {
		OnLoad: function() {
			var n = this;
			t.browser.msie && "6.0" == t.browser.version && !t.support.style && t(".deal img").each(function() {
				n.maxSize(t(this), "width", 200)
			})
		},
		maxSize: function(t, n, e) {
			t[n]() > e && t[n](e)
		}
	})
}(jQuery), function(t) {
	tuan800ued.addModule("yujiazai", {
		Init: function(n) {
			t.extend(n, {
				pageNum: 30,
				listlen: 0,
				ppTpl: function() {
					var n = new t.Buffers;
					return n.append('<div class="deal {{#ztdeal_oth}} dealyjz">'), n.append("{{#fjfinfo}}"), n.append('	   <a href="{{#jump}}" target="_blank"><img alt="{{#name}}" src="{{#image}}"></a>'), n.append('	   <h3><a target="_blank" href="{{#url}}">{{#title}}<em>{{#subtitle}}</em></a></h3>'), n.append("	   {{#ztinfo}}"), n.append("</div>"), n.toString()
				}(),
				ppTplt: function() {
					var n = new t.Buffers;
					return n.append('	<div class="info">{{#description}}</div>'), n.append('    <h4><a target="_blank" href="{{#jump}}"></a></h4>'), n.append('    <h5><span class="sj">{{#begin_time}}</span>{{#sitename}}{{#pinglun}}<span class="fx"><u>分享</u></span></h5>'), n.toString()
				}()
			})
		},
		OnLoad: function() {
			var n = window.jsonActivities || [];
			n.length > 0 ? t(window).bind("scroll", t.fn.listmore) : (t("#contentA .blockC").hide(), t("#contentA .page_div").show())
		}
	})
}(jQuery), function() {
	$.fn.listmore = function(t) {
		var n = ($(this), tuan800ued.opts),
			t = window.jsonActivities || [],
			e = t.length,
			i = $("#contentA .left").height();
		if ($(document).scrollTop() - i - 300 + $(window).height() > 0) {
			if ($(window).unbind("scroll", $.fn.listmore), n.totalnum = parseInt(e % n.pageNum) > 0 ? parseInt(e / n.pageNum) + 1 : parseInt(e / n.pageNum), n.listlen == n.totalnum) return $("#contentA .blockC").hide(), $("#contentA .page_div").show(), !1;
			n.listlen += 1;
			var o = new $.Buffers;
			return pt = (n.listlen - 1) * n.pageNum, pt = 0 > pt ? 0 : pt, $.each(t.slice(pt, n.listlen * n.pageNum), function(t, e) {
				if (e) {
					var i = $.ParseTpl(n.ppTpl, $.extend(!0, {}, e, {
						ztdeal_oth: function() {
							return e.expired || e.price_rise || e.sold_out ? "djn" : ""
						},
						ztinfo: function() {
							return e.expired ? '<span class="yjs"></span>' : e.price_rise ? '<span class="yzj"></span>' : e.sold_out ? '<span class="ysq"></span>' : $.ParseTpl(n.ppTplt, $.extend(!0, {}, e, {
								sitename: function() {
									return e.zhitao_site ? '<span class="ly">来源：<a href="' + e.zhitao_site.site_url + '" target="_blank">' + e.zhitao_site.site_name + "</a></span>" : ""
								},
								pinglun: function() {
									return e.pinglun && "等您评论" != e.pinglun.pinglun_name ? '<span class="pl"><a href="' + e.pinglun.pinglun_url + '" target="_blank">' + e.pinglun.pinglun_name + "</a></span>" : ""
								}
							}))
						}(),
						fjfinfo: function() {
							return e.fanjifen && 1 === e.fanjifen ? "<span class='fjfh'></span>" : ""
						}()
					}));
					o.append(i)
				}
			}), setTimeout(function() {
				$("#contentA .blockB").append(o.toString()), $(window).bind("scroll", $.fn.listmore), $.ztdeallistfun()
			}, 1e3), !1
		}
	}, $.ztdeallistfun = function() {
		$(".dealyjz").each(function() {
			function t(t, n, e) {
				t[n]() > e && t[n](e)
			}
			var n = $(this).find("h3").text(),
				e = "http://" + window.location.host + t(this).find("h4 a").attr("href"),
				i = $(this).find("img").attr("src"),
				o = $(this).find("h5 .fx").html("<u>分享</u>"),
				a = $('<div class="dropdown">分享：</div>').appendTo(o);
			a.share({
				build: !0,
				title: encodeURI(n),
				url: encodeURI(e),
				pic: i,
				tencent: "qqwb"
			}), o.hover(function() {
				$(this).addClass("active")
			}, function() {
				$(this).removeClass("active")
			}), $.browser.msie && "6.0" == $.browser.version && !$.support.style && $(this).find(".l img").each(function() {
				t($(this), "width", 200)
			}), $(this).removeClass("dealyjz")
		})
	}
}(jQuery), function(t) {
	tuan800ued.addModule("userGuid", {
		OnLoad: function() {
			if (!t.cookie("userGuid")) {
				var n = t(".fblockB");
				n.parent().css("position", "relative"), t(".fblockB span").tooltip({
					msg: " 不爱一成不变，喜欢变化多端？为您新增加了横竖版切换展示商品。",
					direction: !1
				})
			}
			t(".fblockB").delegate(".close", "click", function() {
				t.cookie("userGuid", "false", {
					path: "/",
					expires: 365,
					domain: "zhe800.com"
				})
			})
		}
	})
}(jQuery);