function js_share_copy(t) {
	function e(t) {
		return "undefined" != typeof window.clipboardData ? (window.clipboardData.setData("Text", t), !0) : !1
	}
	e(t) ? alert("复制网址成功,请粘贴到你的QQ/MSN上推荐给你的好友！") : alert("目前只支持IE，请复制地址栏URL,推荐给你的QQ/MSN好友！")
}
$.fn.extend({
	tab: function(t) {
		function e(t) {
			s.eq(t).addClass(n.cls).siblings().removeClass(n.cls), o.eq(t).addClass(n.cls).siblings().removeClass(n.cls), "function" === $.type(n.callback) && n.callback.call(i, t)
		}
		var n = $.extend({
			nav: ".tab-nav",
			pane: ".tab-content .tab-pane",
			delegater: "a",
			cls: "active",
			evt: "mouseenter",
			callback: null
		}, t),
			i = this,
			a = i.find(n.nav),
			s = a.find(n.delegater),
			o = i.find(n.pane),
			r = a.find("." + n.cls),
			l = 0;
		return this.length ? (r.length && (l = r.index()), e(l), a.delegate(n.delegater, n.evt, function(t) {
			t.preventDefault();
			var n = $(this);
			l = n.index(), e(l)
		}), "click" == n.evt || a.delegate(n.delegater, "click", function(t) {
			t.preventDefault()
		}), this) : this
	},
	dropdown: function(t) {
		var e = $.extend({
			evt: "click"
		}, t),
			n = this;
		return "hover" == e.evt ? (n.bind("mouseenter", function() {
			n.addClass("open")
		}), n.bind("mouseleave", function() {
			n.removeClass("open")
		})) : n.find(".trigger").bind(e.evt, function(t) {
			t.preventDefault(), n.toggleClass("open")
		}), "click" == e.evt && ($global = $(document), $global.data("dropBinded") || ($global.bind("click", function(t) {
			var e = $(t.target);
			e.closest(".dropdown").length || $(".dropdown.open").removeClass("open")
		}), $global.data("dropBinded", "true"))), this
	},
	affix: function(t) {
		if (!this.length) return this;
		var e = $.extend({
			top: 0,
			bottom: 0,
			scrollTop: null,
			maxTop: null,
			oHeight: null
		}, t),
			n = this,
			i = n.get(0).offsetParent || document.body,
			i = "html" == i.tagName.toLowerCase() ? document.body : i,
			a = $(i),
			s = a.length ? a.height() : $("body").height(),
			o = e.oHeight || n.height(),
			r = e.maxTop ? e.maxTop - o : 0,
			l = document.body.scrollTop || document.documentElement.scrollTop,
			u = n.offset().top;
		if ((e.scrollTop || l >= u - e.top - e.bottom) && (e.maxTop && l >= r - e.bottom ? r > u && n.addClass("affix-bottom").css({
			top: r
		}) : e.scrollTop ? l >= e.scrollTop && n.addClass("affix") : n.addClass("affix")), "undefined" == typeof document.body.style.maxHeight) {
			var c = tuan800ued.getModule("ie6Fixed"),
				d = $(window).height();
			c && c.fixed(n, (d - o) / 2)
		} else $(window).bind("scroll", function() {
			var t = document.body.scrollTop || document.documentElement.scrollTop;
			if (a.height() != s) {
				var i = a.height() - s;
				s = a.height(), r += i
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
			f.n = void 0 !== t ? t : f.n, f.n > p && (f.n = 1, o.css("margin-left", 0)), f.n < 0 && (f.n = p - 1, o.css("margin-left", -p * c)), "instance" === e ? (o.css({
				marginLeft: -f.n * c * a.num
			}), f.n == p && o.css("margin-left", 0)) : o.stop().animate({
				marginLeft: -f.n * c * a.num
			}, a.speed, function() {
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
		}, t),
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
		return e(), l.bind("click", function(t) {
			t.preventDefault(), f.n--, n()
		}), u.bind("click", function(t) {
			t.preventDefault(), f.n++, n()
		}), a.auto && (i = setInterval(function() {
			n(h++)
		}, a.time), $.each([l, u], function() {
			$(this).bind("click", function() {
				clearInterval(i), i = setInterval(function() {
					n(f.n++)
				}, a.time)
			})
		})), this.exports = f, this
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
		}, t),
			n = this,
			i = $('<div class="tooltip ' + e.clz + '"><div class="tooltip-content">' + e.msg + (e.close ? '<i class="close"></i>' : "") + '</div><div class="tooltip-arrow"></div></div>').insertAfter(n),
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
					top: a.top - u - s.height(),
					left: a.left - (l - o) / 2
				});
				break;
			case "left":
				i.css({
					left: a.left - l - s.width(),
					top: a.top + (r - u) / 2
				}), s.addClass("right");
				break;
			case "right":
				i.css({
					left: a.left + o + s.width(),
					top: a.top + (r - u) / 2
				}), s.addClass("left");
				break;
			case "bottom":
				i.css({
					top: a.top + r + s.height(),
					left: a.left - (l - o) / 2
				}), s.addClass("up")
			}
			switch (e.offset && i.css(e.offset), e.evt) {
			case "hover":
				i.hide(), n.unbind("mouseenter.tooltip").unbind("mouseleave.tooltip").bind("mouseenter.tooltip", function() {
					i.show()
				}).bind("mouseleave.tooltip", function(t) {
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
				}), i.unbind("mouseleave.tooltip").bind("mouseleave.tooltip", function(t) {
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
				n.unbind("click.tooltip").bind("click.tooltip", function() {
					i.toggle()
				})
			}
			if (i.find(".close").bind("click", function() {
				i.remove()
			}), "function" === $.type(e.callback)) try {
				e.callback.call(i, null)
			} catch (c) {
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
					return -s >= n ? (t.css(e, 0), void 0) : (t.css(e, n - 1), void 0)
				}, n)
			}
			var i, a, s, o, r = $.extend({
				container: "ul",
				item: "li",
				type: " default",
				pause: !0,
				fps: 18
			}, t),
				l = $(this),
				u = l.find(r.container),
				c = l.find(r.item),
				d = c.length,
				p = c.outerWidth(!0),
				h = c.outerHeight(!0),
				f = 1e3 / r.fps;
			"vertical" === r.type ? (i = "height", a = "marginTop", s = h * d) : (i = "width", a = "marginLeft", s = p * d), e(), o = n(u, a, f), r.pause && u.bind("mouseenter.marquee", function() {
				clearInterval(o)
			}).bind("mouseleave", function() {
				o = n(u, a, f)
			})
		}), this
	},
	share: function(t) {
		var e = $.extend({
			build: !1,
			title: encodeURI(document.title),
			url: encodeURI(location.href),
			pic: "",
			sites: null,
			text: ""
		}, t || {}),
			n = this,
			i = {
				sina: "http://v.t.sina.com.cn/share/share.php?url={{#url}}&title={{#title}}&pic={{#pic}}",
				qqwb: "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={{#url}}&title={{#title}}&pics={{#pic}}",
				renren: "http://share.renren.com/share/buttonshare.do?link={{#url}}",
				kaixin: "http://www.kaixin001.com/rest/records.php?url={{#url}}&style=11&content={{#title}}&pic={{#pic}}",
				douban: "http://www.douban.com/recommend/?url={{#url}}&title={{#title}}&image={{#pic}}",
				qzong: "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={{#url}}&title={{#title}}&pics={{#pic}}"
			};
		return e.build && (e.sites ? $.each(e.sites, function(t, i) {
			n.append('<a href="#" target="_blank" class="' + i + '">' + (e.text[i] || "") + "</a>")
		}) : $.each(i, function(t) {
			n.append('<a class="' + t + '" href="#" target="_blank"></a>')
		})), $.browser.msie && "6.0" === $.browser.ver && (e = $.extend(e, {
			title: decodeURI(e.title)
		})), $.each(i, function(t, i) {
			i && n.find("." + t).attr("href", $.ParseTpl(i, e))
		}), this
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
			if (!("placeholder" in document.createElement("input"))) {
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
				i.on("focus", function() {
					var t = $(this).val();
					t.length > 0 && u.hide()
				}).on("blur", function() {
					var t = $(this).val();
					0 === t.length && u.show()
				}).on(d, function() {
					var t = $(this).val();
					0 === t.length ? u.show() : u.hide()
				}), u.on("click", function() {
					i.trigger("focus")
				}), i.filter(":focus").trigger("focus")
			}
		})
	}
}), function(t) {
	t.addfavorite = function(e) {
		t.fn.addfavorite(e)
	}, t.fn.addfavorite = function(e) {
		var n = t(this),
			i = e || {},
			a = i.url ? i.url : "http://www.zhe800.com/",
			s = i.title ? i.title : "折800，每天9块9！",
			o = [{
				string: navigator.platform,
				subString: "Win",
				identity: "Windows"
			}, {
				string: navigator.platform,
				subString: "Mac",
				identity: "Mac"
			}, {
				string: navigator.userAgent,
				subString: "iPhone",
				identity: "iPhone/iPod"
			}, {
				string: navigator.platform,
				subString: "Linux",
				identity: "Linux"
			}],
			r = "按 <strong>{{#sys}}+D</strong>，把折800放入收藏夹，每天9块9！",
			l = tuanpub,
			u = "Ctrl",
			c = function(t) {
				for (var e = 0; e < t.length; e++) {
					var n = t[e].string;
					if (n && -1 != n.indexOf(t[e].subString)) return t[e].identity
				}
			};
		if (("chrome" == t.browser.name || "safari" == t.browser.name || "opera" == t.browser.name || "firefox" == t.browser.name && parseInt(t.browser.ver) >= 23) && (i = r), l.OS = c(o) || "an unknown OS", "Mac" == l.OS && (u = "Command"), t.browser.msie) try {
			window.external.addFavorite(a, s)
		} catch (d) {
			try {
				window.external.addToFavoritesBar(a, s)
			} catch (p) {
				(0 == n.size() ? t : n).Dialogs({
					auto: !1,
					msg: "加入收藏失败，请您手工加入。"
				})
			}
		} else window.external && t.browser.mozilla ? 11 != t.browser.ver && window.sidebar && window.sidebar.addPanel ? window.sidebar.addPanel(s, a, "") : t.Dialogs({
			msg: "按Ctrl+D，把折800加入收藏夹。"
		}) : (0 == n.size() ? t : n).Dialogs({
			auto: !1,
			msg: t.ParseTpl(r, {
				sys: u
			})
		})
	}
}(jQuery), function(t) {
	tuanpub.addModule("toolbar_pp", {
		Init: function() {
			tuanpub.headQueue = new t.Buffers
		},
		init: function() {
			var e = this,
				n = 0 == t("#prologin").length ? "tblogin" : "prologin";
			PassportSC.drawPassportNew(n, this.toolbarTpl, {
				appid: 3001,
				_this: e
			}), tuanpub.headQueue.push(function() {
				if ("yes" == tuanpub.opts.user.login_stauts.login) {
					var e = t("#toolbar .right .user"),
						n = tuanpub.opts.user.login_stauts,
						i = parseInt(n.msg_num);
					if (e.length) {
						if (!e.find(".icon-level").length) {
							var a = new t.Buffers,
								s = "",
								o = "",
								r = "";
							a.push('<i class="icon-arrow arrow-down"></i>'), a.push('<a class="icon-level level' + (n.grade - 1) + '" href="http://www.zhe800.com/jifen/profile/trade" target="_blank"></a>'), r = i > 0 ? '<i class="msg-num">' + i + "</i>" : "", s = '|&nbsp;<a href="http://www.zhe800.com/profile/message_box" class="msg" target="_blank">我的消息' + r + "</a>&nbsp;|", n.pop_msg && (o = '<div class="msg-tips"><span>' + n.pop_msg + '</span><i class="up-arr"></i><i class="close-tips"></i></div>'), e.after(a.toString()), t("#mytrade").after(s + o);
							var l = e.parent(".dropdown"),
								u = t(".msg-tips");
							u.find(".close-tips"), l.dropdown({
								evt: "hover"
							}), u.click(function(e) {
								var n = t(this),
									i = e.target;
								"close-tips" === i.className ? n.hide() : "SPAN" === i.nodeName && window.open("http://www.zhe800.com/profile/message_box"), t.get("/cn/msg/set_pull_time?pop_read=1")
							})
						}
						if (e.width() > 180 && e.addClass("maxuser"), tuanpub.opts.user.muying) {
							var c = [];
							for (var d in tuanpub.opts.user.muying) tuanpub.opts.user.muying.hasOwnProperty(d) && c.push('"' + d + '":"' + tuanpub.opts.user.muying[d] + '"');
							t.cookie("maternal", "{" + c.join(",") + "}", {
								path: "/",
								expires: 365,
								domain: ".zhe800.com"
							})
						}
					}
				}
			})
		},
		toolbarTpl: function(e, n) {
			var i = PassportCardList[e],
				a = -1 == document.location.href.indexOf("login") ? "?return_to=" + encodeURIComponent(document.location.href) : "?return_to=" + encodeURIComponent("http://www.zhe800.com");
			i.rootElement = function() {
				this.cElement = n, this.sElement = n.after('<div class="hidden"></div>').next()
			}, i.drawLoginForm = function() {
				var e = new t.Buffers;
				e.push('<a target="_blank" class="sina" href="http://passport.tuan800.com/sso/partner_login/weibo' + a + '">微博登录</a> '), e.push('<a target="_blank" class="qq" href="http://passport.tuan800.com/sso/partner_login/qq_connect' + a + '">QQ登录</a> | '), e.push('<a href="http://www.zhe800.com/login' + a + '">登录</a> <a href="http://www.zhe800.com/signup' + a + '">免费注册</a> <span></span>'), this.cElement.html(e.toString())
			}, i.drawPassportWait = function() {
				this.cElement.html("登录中，请稍候...")
			}, i._drawPassportCard = function() {
				var i = this.cookie,
					s = ["未知", "邮箱", "手机", "新浪微博", "QQ", "淘宝", "人人网"],
					o = '<a href="http://www.zhe800.com/profile/recheck" class="user" target="_blank">' + i.userid + "</a>&nbsp;";
				0 == i.nkd && (o = "<span>" + s[i.regw] + "</span>" + '用户，请<a href="http://passport.tuan800.com/account/setting' + a + '" target="_blank" class="user">设置昵称</a>&nbsp;');
				var r = new t.Buffers;
				r.push('<span>您好，</span><div class="dropdown myzhe">' + o + '</a><ul class="dropdown-menu"><li><a href="http://passport.tuan800.com/account/safe" target="_blank" class="trigger">账号信息</a></li><li><a href="http://www.zhe800.com/profile/my_invitation/mode" target="_blank" class="trigger">邀请好友</a></li><li><a href="http://www.zhe800.com/profile/my_favorites/all" target="_blank" class="trigger">我的收藏</a></li><li><a href="http://www.zhe800.com/jifen/profile/score_histories/all" target="_blank" class="trigger">我的积分</a></li><li><a class="exit" href="javascript:PassportCardList[' + e + '].doLogout();">退出</a></li></ul></div>'), r.push('|&nbsp;<a href="http://shop.zhe800.com/my/orders" id="mytrade" target="_blank">我的订单</a>&nbsp;'), this.cElement.html(r.toString()), 0 == i.nkd && n.find(".user").attr("href", "http://passport.tuan800.com/account/setting" + a)
			}, "prologin" == n.attr("id") && (i.logoutRedirectUrl = "http://www.zhe800.com/"), i.loginApp = function() {
				tuanpub.queue()
			}, i.logoutApp = function() {
				i.loginApp()
			}, i.drawPassport(n)
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("jupublic", {
		auto: !0,
		headLogin: function(e) {
			var n = tuanpub.opts,
				e = e || new Function,
				i = "/cn/user_status?",
				a = [];
			0 !== t("#signinid").length && a.push("c=1&d=" + tuan800ued.getModule("signin").calendar("days")), window.ju_fav && a.push("f=1"), t.getJSON(i + a.join("&"), function(e) {
				var i = e || [];
				n.user ? n.user = i : t.extend(n, {
					user: i
				}), tuanpub.queue(tuanpub.headQueue.toArray())
			})
		},
		headloginQueue: function() {},
		OnLoad: function() {
			var t = this;
			tuanpub.login() && t.headLogin()
		},
		Queue: function() {
			tuanpub.getModule("jupublic").headLogin()
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("dialogPP", {
		ppTpl: function(e, n, i) {
			var a = i.t || this,
				s = PassportCardList[e],
				o = i.ttl || "登录折800，享受会员特权";
			s.rootElement = function() {
				this.cElement = n, this.sElement = n.after('<div class="hidden"></div>').next()
			}, s._drawLoginForm = function() {
				var n = -1 == document.location.href.indexOf("login") ? "?return_to=" + encodeURIComponent(document.location.href) : "?return_to=" + encodeURIComponent("http://www.zhe800.com"),
					i = new t.Buffers;
				i.push('<form method="post" onsubmit="return PassportCardList[' + e + '].doLogin();" name="loginform">'), i.push(' <h3 class="hwid2">' + o + "</h3>"), i.push(" <ul>"), i.push('     <div class="error" id="pperrmsg"></div>'), i.push("     <li>"), i.push('         <label>用户名：</label><input value="邮箱/手机号/用户名" title="邮箱/手机号/用户名" onfocus="this.value==this.title?this.value=\'\':null" onblur="this.value==\'\'?this.value=this.title:null" id="ddusername" name="username" type="text" autocomplete="off" disableautocomplete=""" />'), i.push("     </li>"), i.push("     <li>"), i.push('         <label>密　码：</label><input style="display:none" value="" name="password" id="ddpw_1" type="password" autocomplete="off" disableautocomplete="" /><input value="请输入密码" id="ddpw_1_text" type="text" autocomplete="off" disableautocomplete="" />'), i.push("     </li>");
				var a = " hidden",
					s = "";
				window.login_need_captcha && (a = "", s = ' src="' + window.login_error_result.captcha_url + '"'), i.push('     <li class="captcha_box token' + a + '">'), i.push('         <label>验证码：</label><input value="" name="captcha" type="text" autocomplete="off" disableautocomplete=""/><img class="captchaImg"' + s + " />"), i.push("     </li>"), i.push('     <li class="btn">'), i.push('         <input type="submit" class="sign" value=" " onfocus="this.blur()" alt="登 录" title="登 录"/>'), i.push('         <a target="_blank" href="http://passport.tuan800.com/users/password/new">忘记密码？</a>'), i.push('         <a target="_blank" href="http://passport.tuan800.com/users/signup">立即注册</a>'), i.push("     </li>"), i.push(" </ul>"), i.push("</form>"), i.push('<div class="sf clear">'), i.push('<p><a class="qq" href="http://passport.tuan800.com/sso/partner_login/qq_connect?return_to=' + n + '"></a>'), i.push('<a class="morela"><span>更多</span><span class="moreicona"></span></a></p>'), i.push('<p class="more_logina hidden">'), i.push('<a class="sina" href="http://passport.tuan800.com/sso/partner_login/weibo?return_to=' + n + '"></a>'), i.push('<a class="taobao" href="http://passport.tuan800.com/sso/partner_login/taobao?return_to=' + n + '"></a>'), i.push('<a class="renren" href="http://passport.tuan800.com/sso/partner_login/renren?return_to=' + n + '"></a>'), i.push("</p>"), i.push("</div>"), this.cElement.html(i.toString()), t(".morela").click(function() {
					var e = t(".moreicona");
					e.hasClass("iconup") ? (e.removeClass("iconup"), t(".more_logina").slideUp()) : (e.addClass("iconup"), t(".more_logina").slideDown())
				}), t("#ddpw_1_text").focus(function() {
					t("#ddpw_1_text").hide(), t("#ddpw_1").show().focus()
				}), t("#ddpw_1").blur(function() {
					"" == t(this).val() && (t("#ddpw_1_text").show(), t("#ddpw_1").hide())
				})
			}, s.drawPassportWait = function() {}, s._drawPassportCard = function() {
				a.dback()
			}, s.drawPassportInfo = function() {}, s.loginApp = function() {
				tuanpub.queue()
			}, s.logoutApp = function() {
				tuanpub.queue()
			}, s.drawPassport(n)
		},
		callbackQueue: [],
		init: function() {
			var e = this,
				n = new t.Buffers;
			return n.push('<div class="qd_login">'), n.push(' <div class="ht">'), n.push('     <a class="logo" target="_blank" href="http://www.zhe800.com"></a>'), n.push(" </div>"), n.push('<div class="hc clear" id="ppLogin_qd"></div>'), n.push("</div>"), t.Dialogs({
				id: "dialog_log_qiandao",
				overlay: !0,
				auto: !1,
				openfun: function() {
					PassportSC.drawPassportNew("ppLogin_qd", e.ppTpl, {
						appid: 3002,
						t: e
					}), t.each(e.callbackQueue, function(e, n) {
						if ("function" == t.type(n)) try {
							n.apply(this, [])
						} catch (i) {
							window.console && console.info(i.message)
						}
					})
				},
				msg: n.toString()
			}), !1
		},
		dback: function() {
			t("#dialog_log_qiandao .close").trigger("click")
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("wztaoinnav", {
		OnLoad: function() {
			t("#wztaoid").hover(function() {
				t(this).addClass("topfon")
			}, function() {
				t(this).removeClass("topfon")
			})
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("signin", {
		init: function() {
			var e = t("#signinid").data("qq-g-num") || "",
				n = t("#signinid").data("qq-g-label") || "",
				i = function() {
					var t = this;
					t.hasClass("sign_panel") ? t.find(".gotuan").before('<p class="addQQ">' + n + "<span>" + e + "</span></p>") : t.hasClass("sign_board") && t.find(".ft .article").append("<p>" + n + "<em>" + e + "</em></p>"), t.data("qqgroup", !0)
				},
				a = {
					addtion: t("#signinid").data("qqgroup") ? !1 : i,
					callback: function() {
						t("#signinid a.signin,.side_panel .signin").unbind("click").bind("click", function(t) {
							return t.preventDefault(), tuanpub.getModule("dialogPP").init(), !1
						})
					}
				};
			this.createPanel(a)
		},
		calendar: function(e) {
			var n = new Date,
				i = n.getFullYear(),
				a = n.getMonth(),
				s = new Date(i, a, 1),
				o = s.getDay(),
				r = new Date(new Date(i, a + 1, 1).getTime() - 864e5).getDate(),
				l = new Date(s.getTime() - 1e3 * 3600 * 24 * o),
				u = o + r,
				c = [];
			if ("days" === e) return n.getDate() + o;
			"array" === t.type(e) && (c = e), code = new t.Buffers, code.push('<div class="tit">' + i + "年" + (a + 1) + '月</div><table class="calendar"><tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr>'), code.push("<tr>");
			for (var d = 0; 42 > d; d++) code.push("<td" + ("1" == c[d] ? ' class="on"' : o > d || d >= u ? " class='disable' " : "") + ">" + l.getDate() + "</td>"), l = new Date(l.getTime() + 864e5), 0 === (d + 1) % 7 && code.push("</tr><tr>");
			return code.push("</tr>"), code.push("</table>"), code.toString()
		},
		signQueue: new t.Buffers("queue"),
		sideAd: function(e) {
			var n = '<a href="{{#src}}" title="{{#src_desc}}" target="{{#target}}"><img src="{{#image_src}}" alt="{{#image_desc}}" /></a>';
			return e ? (e.src || (n = '<img src="{{#image_src}}" alt="{{#image_desc}}" />'), t.ParseTpl(n, e)) : '<div class="tit">小贴士</div><div class="con"><h3>折800商品的更新时间：</h3><p>折800每天更新两次。<br />上午9点，更新近千款；<br />晚上20点，更新百款左右；</p><i class="icon_ft"></i></div>'
		},
		createPanel: function(e) {
			function n(t) {
				return t ? "<p>" + t.name + "<em><b>+" + t.integral + "</b></em>分，" + '<a target="_blank" href="' + t.url + '">马上领取</a>' + "</p>" : ""
			}
			var i = t.extend({
				type: !1,
				container: t("#signinid"),
				return_to: t("#signinid").attr("return_to") || "http://www.zhe800.com/login",
				addtion: null,
				callback: null,
				data: null
			}, e),
				a = i.container instanceof jQuery ? i.container : jQuery(i.container),
				s = new t.Buffers,
				o = this,
				r = tuan800ued.getModule("sidePanel");
			if (i.type === !1) a.hasClass("sign_board") && a.removeClass("sign_board").addClass("sign_panel"), a.hasClass("signin_on") && a.removeClass("signin_on"), s.push('<a class="signin" href="' + i.return_to + '"></a>'), s.push('<div class="dropdown-menu">'), s.push('   <dl class="">'), s.push("       <dt>每天最多可赚：<b>" + t("#signinid").data("holiday") + '</b>&nbsp;积分<br><a class="cnlogin" href="' + i.return_to + '">登录</a>&nbsp;后才能签到<br></dt>'), s.push("       <dd>"), s.push("           <p>"), s.push('              <a target="_blank" href="http://www.zhe800.com/jifen/profile/score_histories/all">我的积分</a>'), s.push("              &nbsp;|&nbsp;"), s.push('              <a target="_blank" href="http://www.zhe800.com/faq/integrall">积分攻略</a>'), s.push("          </p>"), s.push('          <a target="_blank" href="http://www.zhe800.com/hd/firsttask">折800新手任务，轻松起步</a>'), s.push('          <p class="gotuan"><span>团800签到获双倍积分，</span><a target="_blank" href="http://www.tuan800.com/">去签到</a></p>'), s.push("      </dd>"), s.push("  </dl>"), s.push("</div>"), a.empty().html(s.toString());
			else {
				var l, u;
				tuanpub.opts.user.login_stauts && 0 !== tuanpub.opts.user.login_stauts.tasks.length && (l = tuanpub.opts.user.login_stauts.tasks[0], u = tuanpub.opts.user.login_stauts.tasks[1]);
				var c = 1 == tuanpub.opts.user.login_stauts.follow.qq ? '<a target="_blank" href="http://www.zhe800.com/jifen/profile/score_histories/all">已领取</a>' : '<a target="_blank" href="http://www.zhe800.com/hd/firsttask/zone/">马上领取</a>',
					d = 1 == tuanpub.opts.user.login_stauts.follow.wx ? '<a target="_blank" href="http://www.zhe800.com/jifen/profile/score_histories/all">已领取</a>' : '<a target="_blank" href="http://www.zhe800.com/hd/firsttask/wechat/">马上领取</a>',
					p = i.data || tuanpub.opts.user.checkin_status;
				switch (i.type) {
				case "1":
					s.push('<a href="javascript:void(0)" class="signin"></a>'), s.push('<div class="dropdown-menu">'), s.push('<div class="hd"><p>连续签到 <b>' + p.day + "</b>  天，<span><b>+" + p.current_score + "</b>分</span>明天签到可得<span><b>" + p.tomorrow_score + "</b>分</span>我的积分：<span><b>" + p.score + "</b></span></p>"), s.push('<p><a href="http://www.zhe800.com/jifen/raffle" target="_blank">积分抽奖(1分起)>></a> <a href="http://www.zhe800.com/jifen/welfare" target="_blank">积分兑换>></a> <a href="http://www.zhe800.com/jifen/auction" target="_blank">积分竞拍>></a> <a href="http://www.zhe800.com/jifen/play_activity/jfdzp" target="_blank">3积分中500分>></a></p></div>'), s.push('<div class="bd clear"><div class="side">' + o.sideAd(p.ad) + "</div>"), s.push('<div class="article">' + o.calendar(p.sign_info || tuanpub.opts.user.checkin_status.sign_info) + "</div></div>"), s.push('<div class="ft"><h3>积分任务：</h3><div class="side">' + n(l) + '<p>购物返积分  <a href="http://www.zhe800.com" target="_blank">去购物>></a></p></div><div class="article">' + n(u) + '<p>团800签到获双倍积分，<a target="_blank" href="http://www.tuan800.com/">去签到</a></p><p>下载手机折800，签到更方便，<a href="http://www.tuan800.com/m/zhe800" target="_blank">去下载&gt;&gt;</a></p></div></div>'), s.push("</div>"), a.hasClass("sign_panel") && a.removeClass("sign_panel").addClass("sign_board"), a.hasClass("signin_on") || a.addClass("signin_on"), a.empty().html(s.toString()), a.find(".ft a").each(function() {
						var e = t(this);
						/\>\>$/.test(e.text()) || e.text(e.text() + ">>")
					});
					break;
				case "0":
					s.push('<a href="javascript:void(0)" class="signin"></a>'), s.push('<div class="dropdown-menu">'), s.push('    <dl class="">'), s.push("        <dt>连续签到：" + p.day + "天<br />签到即可获得<b> " + p.current_score + " </b>分</dt>"), s.push("        <dd>"), s.push("            <p>"), s.push("                <span>我的积分：</span>"), s.push('                <a target="_blank" href="http://www.zhe800.com/jifen/profile/score_histories/all"><b>' + p.score + '</b></a> | <a target="_blank" href="http://www.zhe800.com/jifen/play_activity/jfdzp">去抽奖</a>'), s.push("            </p>"), s.push("            <p><span>关注空间+10分，</span>" + c + "</p>"), s.push("            <p><span>关注微信+10分，</span>" + d + "</p>"), s.push('            <p class="gotuan"><span>团800签到获双倍积分，</span><a target="_blank" href="http://www.tuan800.com/">去签到</a></p>'), s.push("        </dd>"), s.push("    </dl>"), s.push("</div>"), a.hasClass("signin_on") && a.removeClass("signin_on"), a.empty().html(s.toString())
				}
			}
			if (i.container.data("status", i.type), i.callback) try {
				i.callback.call(i.container, null)
			} catch (h) {
				console && console.log(h)
			}
			i.addtion && o.signQueue.push(i.addtion), t.each(o.signQueue.toArray(), function(e, n) {
				"function" === t.type(n) && n.call(i.container, null)
			}), t(".side_panel").length || r.init(), r.createSignPanel(i.type)
		},
		cookfunc: function(e) {
			if (!tuanpub.login()) return !1;
			t("#signinid"), t("#signinid").data("qq-g-num") || "", t("#signinid").data("qq-g-label") || "";
			var n = t.cookie("qd");
			qd_data = n.split("-");
			var i = qd_data[0],
				a = (qd_data[2], qd_data[1] || 0),
				s = t("#signinid").length > 0 ? t("#signinid").attr("info").split("-") : "1-2-3".split("-"),
				o = (s[2], s[0]),
				r = s[0],
				l = s[0];
			a > 0 && 3 > a && (o = s[a], r = s[a - 1], l = s[a]), a >= 3 && (o = s[2], r = s[2], l = s[2]);
			var u = t("#signinid");
			switch (e.current_score && e.tomorrow_score && (o = r = e.current_score, l = e.tomorrow_score, u.attr("qdscore") || u.attr("qdscore", e.current_score + "-" + e.tomorrow_score)), i) {
			case "0":
				this.createPanel({
					type: i,
					data: e,
					callback: function() {
						var n = tuanpub.login(),
							i = n.idss.id2;
						0 == i && tuanpub.opts.user.login_stauts.ck_times <= 3 ? t("#signinid .signin,.side_panel .signin").unbind("click").click(function() {
							tuanpub.getModule("bindPhoneMod").init()
						}) : t("#signinid .signin,.side_panel .signin").unbind("click").click(function() {
							t.qdpost("/cn/checkin?checkin=1", e, t(this)), t(".hdts").remove()
						})
					}
				});
				break;
			case "1":
				this.createPanel({
					type: i,
					data: e,
					callback: function() {
						t("#signinid .signin,.side_panel .signin").unbind("click")
					}
				})
			}
		},
		action: function() {
			var e = new t.Buffers,
				n = t.cookie("outoverlay");
			t("#head_nav .tooltip .close").trigger("click");
			var i = function() {
					e.push('<b>今日是签到双倍积分日</b><br/>主站和手机客户端可分别签到领积分<br/><a target="_blank" href="http://www.tuan800.com/m/zhe800">去下载手机客户端</a>'), e = e.toString(), t("#signinid").tooltip({
						msg: e,
						offset: {
							top: -60
						}
					}), t.cookie("outoverlay", !0, {
						path: "/",
						expires: 1,
						domain: "zhe800.com"
					})
				},
				a = tuanpub.opts.user.checkin_status || {};
			if (tuanpub.getModule("signin").cookfunc(a), 1 != a.channels || n || 2 != a.status) if (2 == a.status || n || 0 != a.channels.web || 0 != a.channels.mobile) {
				if (0 == a.channels.web && 1 == a.channels.mobile && 2 != a.status && !t.cookie("signed")) {
					var s = t.cookie("qd");
					qd_data = s.split("-");
					var o = qd_data[1] || 0,
						r = t("#signinid").length > 0 ? t("#signinid").attr("info").split("-") : "1-2-3".split("-"),
						l = r[0],
						u = r[0],
						c = r[0];
					o > 0 && 3 > o && (l = r[o], u = r[o - 1], c = r[o]), o >= 3 && (l = r[2], u = r[2], c = r[2]);
					var d = new t.Buffers;
					d.push('<div class="blockB"><span>今日是签到双倍积分日！</span><p>您已在客户端签到，今日积分<em>+' + u + '</em>，点击以下按钮再次领积分</p><a class="btn1 close" id="signNow" href="javascript:void(0);"><em>签到领双倍积分</em></a></div>'), d = d.toString(), t.Dialogs({
						id: "dialog_tishi_qiandao",
						msg: d,
						auto: !1,
						closebtn: "a.close, span.close",
						overlay: !0,
						openfun: function() {
							t("#signNow").click(function() {
								var e = tuanpub.login(),
									n = e.idss.id2;
								0 == n && tuanpub.opts.user.login_stauts.ck_times <= 3 ? tuanpub.getModule("bindPhoneMod").init() : t.qdpost("/cn/checkin?checkin=1")
							})
						}
					})
				}
			} else i();
			else i()
		},
		Queue: function() {
			tuanpub.login() || (t.cookie("qd", "", {
				path: "/",
				expires: -336,
				domain: "zhe800.com"
			}), tuanpub.getModule("signin").init())
		},
		Init: function() {
			tuanpub.headQueue.push(this.action)
		}
	}), t.qdpost = function(e, n, i) {
		var a = tuanpub.getModule("signin"),
			s = tuanpub.getModule("bindEmailPop");
		t.post(e, function(o) {
			var r = o.status,
				l = o.score,
				u = o.day,
				c = t("#signinid").length > 0 ? t("#signinid").attr("info").split("-") : "1-2-3".split("-"),
				d = (c[2], c[0]),
				p = c[0],
				h = c[0];
			if (t("#signinid").data("qq-g-num") || "", t("#signinid").data("qq-g-label") || "", 5 == r) {
				var f = t.cookie("qd").split("-");
				u = f[1], l = f[2]
			}
			u > 0 && 3 > u && (d = c[u], p = c[u - 1], h = c[u]), u >= 3 && (d = c[2], p = c[2], h = c[2]);
			var g = t("#signinid");
			if (n) n.current_score && n.tomorrow_score && (d = p = n.current_score, h = n.tomorrow_score);
			else if (g.attr("qdscore")) {
				var m = g.attr("qdscore").split("-");
				d = p = m[0], h = m[1]
			}
			var b = "签到失败，请重新签到",
				v = '请<a class="font3399cc"target="_blank" href="#">激活</a>您的账户后再来签到！',
				_ = "为避免刷小号现象，即日起<br/>每台电脑仅允许一个账号进行签到！";
			switch (r) {
			case -1:
				a.createPanel({
					type: "0"
				}), -1 != e.indexOf("?checkin=1") && (tuanpub.opts, t.Dialog({
					id: "dialog_empty_deal",
					cls: "diglog-wrapper-ad",
					offset: {
						top: t("#signinid").offset().top + 45,
						left: t("#signinid").offset().left - 175
					},
					msg: v,
					overlay: !0,
					auto: !1
				}), t("#dialog_empty_deal .diginfo a").attr("href", o.url), t("#dialog_empty_deal .diginfo a").click(function() {
					t("#dialog_empty_deal .close").trigger("click")
				}));
				break;
			case 0:
				a.createPanel(), t("a.signin_a,a.cnlogin").click(function() {
					return self.callfun(), !1
				});
				break;
			case 1:
				window._gaq && (i && i.parents(".side_panel").length ? _gaq.push(["_trackPageview", "/PageAction/daohang/bottom/Signin"]) : _gaq.push(["_trackPageview", "/PageAction/daohang/top/Signin"])), t("#signinid").find(".signin_a").remove().end().prepend('<span class="signin_a"></span>');
				var w = parseInt(t("#signinid dl dt strong").html());
				if (t("#signinid dl dt span").html(u), l > w) {
					var k = function() {
							l > w ? (w++, t("#signinid dl dt strong").html(w)) : window.clearInterval(t.getTime.countTimer.qdjfadd)
						};
					t.getTime.countTimer.qdjfadd = window.setInterval(k, 100)
				}
				if (tuanpub.opts.user && tuanpub.opts.user.checkin_status && tuanpub.opts.user.checkin_status.sign_info) {
					var m = tuanpub.opts.user.checkin_status.sign_info;
					m[m.length - 1] = "1"
				}
				if (a.createPanel({
					type: "1",
					data: o
				}), o.email_tip && s.create(), 0 == o.channels.mobile) {
					var x = new t.Buffers;
					x.push('<div class="blockA clear">'), x.push("    <span>今日是签到双倍积分日！</span>"), x.push("    <p>您还可以到折800手机客户端再次签到获取双倍积分！</p>"), x.push('    <div class="l"><em></em>扫一扫 立即下载</div>'), x.push('    <div class="r">'), x.push("        <b>也可以发送到手机下载：</b>"), x.push("        <ul>"), x.push('            <li><label>手机号码：</label><input id="userPhoneNum" type="text" /></li>'), x.push('            <li><label>验证码：</label><input class="yzm" type="text" /><img id="vCode" url="/cn/captcha" src=""></li>'), x.push('            <li><label>&nbsp;</label><a class="btn1 submitPhone" href="javascript:void(0);"><em>免费发送到手机</em></a></li>'), x.push("        </ul>"), x.push("     </div>"), x.push("</div>"), x = x.toString(), t.Dialogs({
						id: "dialog_tishi_qiandao",
						cls: "diglog-wrapper",
						msg: x,
						auto: !1,
						closebtn: "a.close, span.close",
						overlay: !0,
						openfun: function() {
							t.get("/cn/get_phone_number", function(e) {
								"" != e.phone && t("#userPhoneNum").val(e.phone), t.post("/cn/captcha", function(e) {
									t("#vCode").attr("src", e.url)
								})
							}), t(".submitPhone").click(function() {
								if (t(this).hasClass("blockSubmit")) return !1;
								var e = t(this).children("em").html(),
									n = t(this),
									i = t("#userPhoneNum").val(),
									a = t("#vCode").siblings("input").val();
								return "" == i ? (alert("请输入手机号码!"), !1) : 11 != i.length ? (alert("请输入正确的手机号!"), !1) : "" == a ? (alert("请输入短信验证码!"), !1) : (t(this).children("em").html("发送中..."), t(this).addClass("blockSubmit"), t.post("/cn/send_wap_download_message", {
									phone_number: i,
									captcha: a
								}, function(i) {
									n.children("em").html(e), n.removeClass("blockSubmit"), 1 == i.status ? t("#dialog_tishi_qiandao .close").trigger("click") : 0 == i.status && (alert("您输入的验证码错误!"), t("#vCode").siblings("input").val(""), t.post("/cn/captcha", function(e) {
										t("#vCode").attr("src", e.url)
									}))
								}), void 0)
							}), t("#vCode").click(function() {
								t.post("/cn/captcha", function(e) {
									t("#vCode").attr("src", e.url)
								})
							})
						}
					})
				} else 1 == o.channels.mobile && 1 == o.channels.web && (t.cookie("signed", !0, {
					path: "/",
					expires: 1,
					domain: "zhe800.com"
				}), t.Dialogs({
					id: "dialog_empty_deal",
					cls: "dialog-wrapper-ad",
					offset: {
						top: t("#signinid").offset().top + 45,
						left: t("#signinid").offset().left - 135
					},
					msg: "获取双倍分成功！",
					auto: 5
				}));
				break;
			case 2:
				a.createPanel({
					type: "0"
				}), tuanpub.opts, t.Dialogs({
					id: "dialog_empty_deal",
					cls: "dialog-wrapper-ad",
					offset: {
						top: t("#signinid").offset().top + 45,
						left: t("#signinid").offset().left - 135
					},
					msg: b,
					auto: !1
				});
				break;
			case 3:
				a.createPanel({
					type: "1"
				});
				break;
			case 4:
				a.createPanel({
					type: "0"
				});
				break;
			case 5:
				a.createPanel({
					type: "0"
				}), tuanpub.opts, t.Dialogs({
					id: "dialog_empty_deal",
					cls: "dialog-wrapper-ad",
					msg: _,
					auto: !1
				});
				break;
			default:
				return !1
			}
			0 != r && -1 != r && (1 == r || 3 == r) && t("#signinid").addClass("signin_on"), (-1 == r || 2 == r || 4 == r || 5 == r) && t("#signinid .signin,.side_panel .signin").unbind("click").click(function() {
				t.qdpost("/cn/checkin?checkin=1", n, t(this))
			})
		})
	}
}(jQuery), function(t) {
	tuan800ued.addModule("sidePanel", {
		init: function() {
			var e = new t.Buffers,
				n = this,
				i = "",
				a = "",
				s = null;
			if (t("body").data("userversion")) {
				var o = t.cookie("user_version") ? t.cookie("user_version") : "def",
					r = {
						def: "默认",
						man: "男",
						woman: "女"
					};
				i = '<a class="trigger" id="userversion" href="javascript:void(0)"><p>人群(' + (r[o] || "默认") + ')</p><span><i class="icon icon-' + o + '"></i></span></a>'
			}
			t("body").data("feedback") && (a = '<a class="trigger" href="http://www.zhe800.com/feedback" target="_blank"><p>信息反馈</p><span><i class="icon icon-feedback"></i></span></a>'), e.push('<div class="side_panel"><div class="content">' + a + i + '<a class="trigger protection" href="http://www.zhe800.com/help/cs_support" target="_blank"><p>消费保障</p><span><i class="icon icon-umbrella"></i></span></a><a class="trigger signin" href="javascript:void(0)"><p>点击签到</p><span><i class="icon icon-pen"></i></span></a><a class="trigger" id="goTop" href="javascript:void(0)"><p>返回顶部</p><span><i class="icon icon-up"></i></span></a></div> </div>'), s = t(e.toString()), s.insertAfter("#toolbar"), s.find("#goTop").bind("click", function() {
				var e = document.body.scrollTop ? document.body : document.documentElement;
				t(e).animate({
					scrollTop: 0
				}, 300), window._gaq && _gaq.push(["_trackPageview", "/PageAction/left_menu/Top"])
			}), this.createSignPanel(!1), t.each(n.selfQueue.toArray(), function(e, n) {
				"function" === t.type(n) && n.call(s, null)
			})
		},
		OnLoad: function() {
			var e = t(".side_panel");
			e.affix({
				scrollTop: 300,
				maxTop: t(".about").length ? t(".about").offset().top : null,
				bottom: t(window).height() - e.height() > 0 ? t(window).height() - e.height() : 0
			}), t("body").data("scroll") && setTimeout(function() {
				var e = t(".dealbox .deal").first().offset(),
					n = 500;
				e && (n = e.top), t(window).scrollTop(n)
			}, 1e3)
		},
		selfQueue: new t.Buffers,
		signQueue: new t.Buffers,
		createSignPanel: function(e) {
			var n, i = this,
				a = t(".side_panel"),
				s = a.find(".signin"),
				o = t("#signinid"),
				r = a.find(".signin .icon"),
				l = t("#userversion"),
				u = 0;
			if (l.length) {
				u = 60;
				var c = t.cookie("user_version"),
					d = ["", "", ""],
					p = {
						def: 2,
						man: 1,
						woman: 0
					},
					h = "http://www.zhe800.com?userversion=";
				c = c ? c : "def", d[p[c]] = "on";
				var f = {
					direction: "left",
					evt: "hover",
					msg: '<div class="select"><a title="选择后，将优先展示偏女性商品" data-version="woman" href="' + h + 'woman" class="' + d[0] + '" target="_self">女</a><a title="选择后，将优先展示偏男性商品" data-version="man" href="' + h + 'man" class="' + d[1] + '" target="_self">男</a><a class="noborder ' + d[2] + '" data-version="def" href="' + h + 'def" target="_self">默认</a></div>',
					clz: "versiontip",
					close: !1
				};
				0 === l.next(".tooltip").length && l.tooltip(f), t(".select a").click(function() {
					var e = t(this).data("version");
					t.cookie("user_version", e, {
						expires: 365,
						domain: ".zhe800.com",
						path: "/"
					})
				})
			}
			if (s.length) {
				var f = {
					direction: "left",
					evt: "hover",
					msg: "outerHTML" in document.body ? o.find(".dropdown-menu").get(0).outerHTML : o.clone().find(".signin").remove().end().html(),
					close: !1,
					clz: "signintip",
					offset: {
						top: "auto",
						bottom: 30
					}
				};
				if ("object" === t.type(e)) t.extend(f, e);
				else {
					switch (e) {
					case !1:
						r.attr("class", "icon icon-pen"), a.removeClass("sign_board").addClass("sign_panel"), a.addClass("signin_on"), s.unbind("click").bind("click", function(t) {
							return t.preventDefault(), tuanpub.getModule("dialogPP").init(), !1
						}).find("p").html("点击签到").end().find("span em").html("签到");
						break;
					case "0":
						a.addClass("signin_on"), a.removeClass("sign_board").addClass("sign_panel"), s.find("p").html("点击签到");
						break;
					case "1":
						a.removeClass("signin_on"), a.addClass("sign_board").removeClass("sign_panel"), s.unbind("click").find("em").text("已签"), r.attr("class", "icon icon-smile"), s.find("p").html("明日再签")
					}
					s.data("status", e)
				}
				n = s.next(".tooltip").length ? s.next(".tooltip").remove().end().tooltip(f).next(".tooltip") : s.tooltip(f).next(".tooltip"), t.each(i.signQueue.toArray(), function(e, n) {
					"function" === t.type(n) && n.call(s, null)
				})
			}
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("changeSignPanel", {
		Init: function() {
			tuanpub.getModule("sidePanel").signQueue.push(this.replace), tuanpub.getModule("signin").signQueue.push(this.replace)
		},
		replace: function() {
			var e = this,
				n = t("#signinid"),
				i = e.next(".tooltip").find(".dropdown-menu"),
				a = e.data("status"),
				s = (new t.Buffers, {
					text: n.data("side-text"),
					link: n.data("side-link")
				});
			switch ("signinid" === this.attr("id") && (i = this.find(".dropdown-menu"), a = this.data("status")), a) {
			case !1:
				var o = '<div class="login_board"> <div class="con"> <p>每天最多可赚：<b>3</b> 积分</p> <p><a target="_blank" href="http://www.zhe800.com/login?return_to=' + location.href + '">登录</a>后才能签到</p>  </div> <div class="bm"><div class="intro"> <p>每天签到等同赚：<b>5</b> 集分宝</p> <p>' + (s.link ? '<a href="{{#link}}" target="_blank">{{#text}}</a>' : s.text ? "{{#text}}" : "") + "</p> </div> <p>加群享特权：<b>116147054</b></p> </div> </div>";
				i.html(t.ParseTpl(o, s));
				break;
			case "0":
				var o = '<div class="login_board"> <div class="con"><p>连续签到：{{#day}} 天</p><p>今天签到可赚：<b>{{#current_score}}</b> 积分</p><p>连续签到每天可赚：<b>' + t("#signinid").data("holiday") + '</b> 积分</p> </div> <div class="bm"><div class="intro"> <p>每天签到等同赚：<b>5</b> 集分宝</p> <p>' + (s.link ? '<a href="{{#link}}" target="_blank">{{#text}}</a>' : s.text ? "{{#text}}" : "") + "</p> </div> <p>加群享特权：<b>116147054</b></p> </div> </div>",
					r = t.extend({}, s, tuanpub.opts.user.checkin_status);
				i.html(t.ParseTpl(o, r))
			}
		}
	})
}(jQuery), function() {}(jQuery), function(t) {
	tuanpub.addModule("bindPhoneMod", {
		bindPhoneTpl: function() {
			var e = new t.Buffers,
				n = tuanpub.opts.user.login_stauts.ck_times;
			return e.append('<div class="bangdtit">'), e.append("<h3>为了保障您的账户和积分安全，请绑定您的手机！</h3>"), 0 === n ? e.append("<p>您已完成签到<i> 13 </i>次，您需要绑定手机后才能继续签到！</p>") : e.append("<p>未绑定手机前您还可以签到<i> " + n + " </i>次，绑定手机后就可以放心签到啦！</p>"), e.append("</div>"), e.append('<div class="reg_box clear">'), e.append('<div class="item">'), e.append('<label>手机号：</label><input type="text" class="itext1" id="mobil">'), e.append("</div>"), e.append('<div class="item i_code">'), e.append("<label>短信验证码：</label>"), e.append('<input type="text" class="itext2" id="validCodeP">'), e.append('<span class="i_codeP"><a class="send-idf" href="javascript:void(0);">获取验证码</a></span>'), e.append("</div>"), e.append('<div class="item i_txt">'), e.append('<input type="button" class="tbtn btn" id="reg_submit_i" value="">'), 0 !== n && e.append('<a class="nextbind" href="javascript:void(0)">下次绑定</a>'), e.append("</div>"), e.append("</div>"), e.toString()
		},
		bindPhoneSugTpl: function() {
			var e = new t.Buffers;
			return e.append('<div class="bangdok">'), e.append("<h3>恭喜，手机号绑定成功。</h3>"), e.append(" </div>"), e.toString()
		},
		init: function() {
			var e = (tuanpub.opts, this);
			setTimeout(function() {
				t.Dialogs({
					id: "dialog_bdsj_bangding",
					overlay: !0,
					msg: e.bindPhoneTpl(),
					auto: !1
				});
				var n = t("#reg_submit_i");
				e.inputEvent(), tuanpub.getMobileCode(t(".send-idf"), t("#mobil")), n.live("click", function(t) {
					t.preventDefault(), e.Checkout()
				}), 0 !== t("a.nextbind").length && t("a.nextbind").click(function() {
					t("#dialog_bdsj_bangding .close").trigger("click"), t.qdpost("/cn/checkin?checkin=1")
				})
			}, 200)
		},
		inputEvent: function() {
			var e = [t("#mobil"), t("#validCodeP")],
				n = [{
					focus: function() {
						t(this).inputWarn("为了您的安全，请确保绑定手机号是您的真实手机", 1), t("#reg_submit_i").inputNotip()
					},
					blur: function() {
						t(this).quizMobile()
					}
				}, {
					focus: function() {
						t(this).inputNotip(), t("#reg_submit_i").inputNotip()
					},
					blur: function() {
						t(this).quizVeriCode()
					}
				}];
			t.each(e, function(t, e) {
				e.live(n[t])
			})
		},
		Checkout: function() {
			var e = [t("#mobil"), t("#validCodeP")];
			t.each(e, function(t, e) {
				e.trigger("blur")
			}), t(".reg_box").find(".msg_zt3")[0] || this.submitForm()
		},
		submitForm: function() {
			var e = this;
			t.getScript("http://passport.tuan800.com/api/bind_mobiles?phone_number=" + t("#mobil").val() + "&phone_confirmation=" + t("#validCodeP").val(), {
				callback: function() {
					var n = window.result1 || {};
					switch (n.status) {
					case 1:
						"yes" == tuanpub.opts.user.login_stauts.login && (t("#reg_submit_i").inputNotip(), t("#dialog_bdsj_bangding .close").trigger("click"), t.Dialogs({
							overlay: !0,
							id: "dialog_bdsj_bangding",
							msg: e.bindPhoneSugTpl,
							auto: !0,
							times: 3,
							closefun: function() {
								t.qdpost("/cn/checkin?checkin=1")
							}
						}));
						break;
					case 2:
						return t("#validCodeP").inputError(n.message);
					case 3:
						return t("#reg_submit_i").inputError(n.message);
					case 10:
						return t("#reg_submit_i").inputError(n.message);
					case 11:
						return t("#mobil").inputError(n.message);
					default:
						t("#reg_submit_i").inputNotip()
					}
				},
				cache: "false"
			})
		}
	})
}(jQuery), function(t) {
	var e = {
		tip: function(t, e, n) {
			var i = this.parents(".item").removeClass("msg_zt1 msg_zt2 msg_zt3");
			this.siblings(".msg_box").remove(), t !== !0 && i.addClass("msg_zt" + t).append('<div class="msg_box row' + n + '"><em></em><span class="msg">' + e + "</span></div>")
		},
		isMobile: function(t) {
			return t ? /^0?1[3458]\d{9}$/.test(t) ? void 0 : 2 : 1
		},
		isPcode: function(t) {
			return t ? void 0 : 1
		}
	},
		n = {
			quizVeriCode: function() {
				var t = this;
				t.val() ? t.inputNotip() : t.inputError("请输入短信验证码")
			},
			quizMobile: function() {
				var t = this;
				switch (e.isMobile(t.val())) {
				case 1:
					return t.inputError("请输入手机号");
				case 2:
					return t.inputError("手机号码格式错误");
				default:
					return t.inputCorrect()
				}
			}
		};
	t.extend(t.fn, {
		inputWarn: function(t, n) {
			e.tip.call(this, 1, t, n || 1)
		},
		inputError: function(t, n) {
			e.tip.call(this, 3, t, n || 1)
		},
		inputCorrect: function() {
			e.tip.call(this, 2, "", 1)
		},
		inputNotip: function() {
			e.tip.call(this, !0)
		}
	}, n, {
		sendMobileCode: function(e, n) {
			var n = (t(this), n || new Function);
			t.getScript("http://passport.tuan800.com/api/bind_mobiles/phone_confirmations?phone_number=" + t("#mobil").val() + "&id=" + (new Date).valueOf(), {
				callback: function() {
					var e = window.result || {};
					switch (e.status) {
					case 1:
						break;
					case 2:
						return t("#mobil").inputError(e.message), n(e);
					case 3:
						return t("#mobil").inputError(e.message), n(e);
					case 4:
						return t("#mobil").inputError(e.message), n(e);
					case 5:
						return t("#mobil").inputError(e.message), n(e);
					case 10:
						return t("#mobil").inputError(e.message), n(e);
					case 11:
						return t("#mobil").inputError(e.message), n(e)
					}
				},
				cache: "false"
			})
		}
	}), t.extend(tuanpub, {
		addTimeout: function(t, e, n, i) {
			var a = new Function,
				s = null;
			return i = i || a, n = n || a, window.setInterval(function() {
				return 0 === t-- ? (i.call(window, t), window.clearInterval(s)) : (n.call(window, t), void 0)
			}, e)
		},
		getMobileCode: function(n, i) {
			var a = (new Function, !0),
				s = null,
				o = 120,
				r = 1e3;
			t(".send-idf").unbind().bind("click", function() {
				if (a) switch (i = t("#mobil"), e.isMobile(i.val())) {
				case 1:
					return i.inputError("请输入手机号");
				case 2:
					return i.inputError("手机号码格式错误");
				default:
					s = tuanpub.addTimeout(o, r, function(e) {
						t(".send-idf").html("" + (10 > e ? "0".concat(e) : e) + "秒后重新发送").css({
							cursor: "default",
							"background-position": "0 -208px"
						}), t(".reg_box .item .i_codeP a").css("width", "120px"), a = !1
					}, function() {
						clearInterval(s), a = !0, counter = o, t(".send-idf").html("获取验证码").css({
							cursor: "pointer",
							"background-position": "0 -142px"
						}), t(".reg_box .item .i_codeP a").css("width", "84px")
					}), t("#dialog_bdsj_bangding").find(".close").bind("click", function() {
						clearInterval(s), a = !0, counter = o, t(".send-idf").html("获取验证码").css({
							"background-position": "0 -142px"
						}), t(".reg_box .item .i_codeP a").css("width", "84px")
					}), n.sendMobileCode(i.val(), function() {
						clearInterval(s), a = !0, counter = o, t(".send-idf").html("获取验证码").css({
							"background-position": "0 -142px"
						}), t(".reg_box .item .i_codeP a").css("width", "84px")
					})
				}
			})
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("tbCombine", {
		queue: function() {
			if ("http://www.zhe800.com/" == window.location.href) {
				var e = t.cookie("ppinf");
				if (e) {
					var n = tuanpub.login(),
						i = tuanpub.opts.user.login_stauts,
						a = 4 == n.sigw,
						s = i.phone_number || !1,
						o = "http://i0.tuanimg.com/pp/tb/js/tb1.0.min.js";
					if (void 0 == i.taobao_api || !a) return !1;
					if (1 == i.tb_shop_user) return;
					if ("true" == t.cookie("noTbPop")) return;
					t.cookie("noTbPop", "true", {
						expires: 7
					}), 1 == i.taobao_api && a && (s ? 0 == i.password_setted && t.getScript(o, function() {
						var e = tuanpub.getModule("accountCombine");
						e.exports({
							msg: t.ParseTpl(e.tpl("tbOutBinded"), {
								tel: s
							}),
							close: !0
						})
					}) : t.getScript(o, function() {
						var t = tuanpub.getModule("accountCombine");
						t.exports("tbOutOpen")
					})), 0 == i.taobao_api && a && t.getScript(o, function() {
						var t = tuanpub.getModule("accountCombine");
						t.exports("tbOutClose")
					})
				}
			}
		},
		Init: function() {
			tuanpub.headQueue.push(this.queue)
		}
	})
}(jQuery), function(t) {
	t.tao_dingyue = function() {
		var e = t("#subscribe_email"),
			n = e.val();
		if ("" == n) return alert("请输入您的Email地址。"), !1;
		var i = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
		return i.test(n) ? /\yahoo.cn+$/.test(n) || /\yahoo.com.cn+$/.test(n) ? (alert("雅虎官方关闭邮箱业务，请换其它邮箱"), !1) : (t.post("/email_subscribe", {
			email: n
		}, function(t) {
			alert(t)
		}), !0) : (alert("邮件地址格式错误，请检查。"), !1)
	}
}(jQuery), function(t) {
	t.browser.msie && t.browser.version < 7 && (tuan800ued.addModule("ie6png", {
		OnLoad: function() {
			this.fix(".toolbar a i")
		},
		fix: function(e) {
			var n = this,
				i = "";
			this.fixSel.push(e || ""), i = n.fixSel.toString(), window.DD_belatedPNG ? DD_belatedPNG.fix(i) : t.getScript("http://z0.tuanimg.com/v1/ju/index/js/DD_belatedPNG.min.js", function() {
				DD_belatedPNG.fix(i)
			})
		},
		fixSel: new t.Buffers
	}), tuan800ued.addModule("ie6Fixed", {
		fixed: function(e, n) {
			function i() {
				var t = document.body.scrollTop || document.documentElement.scrollTop;
				e.css("top", t + a)
			}
			if ("boolean" == t.type(arguments[0]) && !arguments[0]) return t(window).unbind("scroll", i), void 0;
			var a = (e.position().left, n || 0);
			t(window).bind("scroll", i)
		}
	}))
}(jQuery), function(t) {
	tuan800ued.addModule("marquee", {
		init: function() {
			function e() {
				var t = $ul.position().left; - t >= a ? $ul.css("left", 0) : (t--, $ul.css("left", t))
			}
			var n = t(".friendly_links"),
				i = n.find(".links");
			$ul = n.find("ul");
			var a = 0,
				s = 0,
				o = 30,
				r = "",
				l = i.width();
			return $ul.find("li").each(function(e, n) {
				a += t(n).outerWidth(!0)
			}), l > a ? !1 : ($ul.append($ul.html()), s = 2 * a, $ul.css("width", s), setTimeout(function() {
				r = setInterval(e, o)
			}, 3e3), i.hover(function() {
				clearInterval(r)
			}, function() {
				r = setInterval(e, o)
			}), void 0)
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("individuation", {
		OnLoad: function() {
			var e = t(".deal"),
				n = 0 === e.length ? t(".dealbox") : e.parent("div");
			n.delegate(".deal a", "click", function() {
				var e = t(this).attr("href"),
					n = /www.zhe800.com\/.*\/fan/;
				if (-1 !== e.indexOf("out.zhe800.com") || n.test(e)) {
					var i = parseInt(t.cookie("oc") || 0),
						a = i + 1;
					t.cookie("oc", a, {
						expires: 90,
						path: "/",
						domain: ".zhe800.com"
					})
				}
			})
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("bindEmailPop", {
		create: function() {
			if (!t("#bind_email").length) {
				var e = new t.Buffers,
					n = t('<div id="bind_email"></div>'),
					i = t(window);
				e.push('<div class="inner"> <a href="javascript:void(0)" class="icon-close close"></a> <div class="bd"> <p>绑定邮箱可获得<em>5积分</em>啦~</p> <div class="triggers"> <a href="http://www.zhe800.com/hd/firsttask/emailandmobile" target="_blank" class="btn">立即去绑定</a> <a href="javascript:void(0)" class="next_time">下次绑定</a> </div> </div> </div>'), n.html(e.toString()), t("#toolbar").after(n), n.css({
					top: (i.height() - n.height()) / 2
				}), n.on("click", ".close", function(t) {
					t.preventDefault(), n.remove()
				}), n.find(".next_time").bind("click", function(t) {
					t.preventDefault(), n.find(".close").trigger("click")
				})
			}
		},
		isBind: function() {
			var e = tuanpub.login() || t.parseJSON(Base64.decode(t.cookie("ppinf").split("|")[3]));
			return 1 === e.idss.id1
		},
		checkCkTimes: function() {
			return !0
		}
	})
}(jQuery), function(t) {
	tuanpub.addModule("hotTags", {
		OnLoad: function() {
			t("#js_hottags .tag-nav a").click(function() {
				var e = t(this),
					n = e.data("tag");
				t("#js_hottags .tag-nav a").removeClass("on"), e.addClass("on");
				var i = t("#js_hottags .tag-lists div");
				i.hide();
				for (var a = 0; a < i.length; a++) {
					var s = i.eq(a).data("tag");
					if (s == n) return i.eq(a).show(), !1
				}
			})
		}
	})
}(jQuery);