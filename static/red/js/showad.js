function Youa (obj)
{
	this.obj = $(obj);
	this.container = $("container");
	this.control = $("control");
	this.items = $$$("item", this.container);
	this.iCenter = 2;
	this.aSort = [];
	this.timer = null;
	this.oData = [
		{left:0, zIndex:2, opacity:30},
		{left:40, zIndex:3, opacity:60},
		{left:124, zIndex:4, opacity:100},
		{left:208, zIndex:3, opacity:60},
		{left:246, zIndex:2, opacity:30},
		{left:40, zIndex:0, opacity:0}
	];
	this.__create__()	
};
Youa.prototype.__create__ = function ()
{
	var that = this;
	var oSpan = null;
	var i = 0;
	for (i = 0; i < that.items.length; i++)
	{
		that.items[i].number = i;
		that.aSort[i] = that.items[i];
		oSpan = document.createElement("span");
		oSpan.number = i;
		that.control.appendChild(oSpan)
	}
	for (i = 0; i < 2; i++) this.aSort.unshift(this.aSort.pop());
	that.aSpan = $$("span", that.control);	
	that.control.onmouseover = function (ev)
	{
		var oEv = ev || event;
		var oTarget = oEv.target || oEv.srcElement;		
		if (oTarget.tagName.toUpperCase() == "SPAN")
		{
			that.aSort.sort(function (a, b) {return a.number - b.number});
			if (oTarget.number < that.iCenter)
			{
				for (i = 0; i < that.iCenter - oTarget.number; i++) that.aSort.unshift(that.aSort.pop());	
				that.__set__();
				return false
			}
			else if (oTarget.number > that.iCenter)
			{
				for (i = 0; i < oTarget.number - that.iCenter; i++) that.aSort.push(that.aSort.shift());
				that.__set__();
				return false
			}
			else
			{
				that.__set__()
			}
		}
	}
	this.__set__();
	this.__switch__();
	this.__autoPlay__()
};
Youa.prototype.__set__ = function ()
{
	var i = 0;
	for (i = 0; i < this.aSort.length; i++) this.container.appendChild(this.aSort[i]);
	for (i = 0; i < this.aSpan.length; i++) this.aSpan[i].className = "";
	this.aSpan[this.aSort[this.iCenter].number].className = "active";
	for (i = 0; i < this.aSort.length; i++)
	{
		this.aSort[i].index = i;		
		if (i < 5)
		{
			new Animate(this.aSort[i], this.oData[i]);
		}
		else
		{
			new Animate(this.aSort[i], this.oData[this.oData.length - 1])
		}
	}
};
Youa.prototype.__switch__ = function ()
{
	var i = 0;
	var that = this;
	this.container.onclick = function (ev)
	{
		var oEv = ev || event;
		var oTarget = oEv.target || oEv.srcElement;
		var index = findItem(oTarget);
		
		if (index < that.iCenter)
		{
			for (i = 0; i < that.iCenter - index; i++) that.aSort.unshift(that.aSort.pop());
			that.__set__();
			return false
		}
		else if (index > that.iCenter)
		{
			for (i = 0; i < index - that.iCenter; i++) that.aSort.push(that.aSort.shift());
			that.__set__();
			return false
		}		
		function findItem (element)
		{
			return element.className == "item" ? element.index : arguments.callee(element.parentNode)
		}
	};
};
Youa.prototype.__autoPlay__ = function ()
{
	var that = this;
	that.timer = setInterval(function ()
	{
		that.aSort[3].click()
	}, 3000);
	that.obj.onmouseover = function ()
	{
		clearInterval(that.timer)
	};
	that.obj.onmouseout = function ()
	{
		that.timer = setInterval(function ()
		{
			that.aSort[3].click()
		}, 3000)	
	}
};
function $ (id)
{
	return typeof id === "string" ? document.getElementById(id) : id
};
function $$ (tagName, oParent)
{
	return (oParent || document).getElementsByTagName(tagName)	
};
function $$$ (className, element, tagName)
{
	var i = 0;
	var aClass = [];
	var reClass = new RegExp("(^|//s)" + className + "(//s|$)");
	var aElement = $$(tagName || "*", element || document);
	for (i = 0; i < aElement.length; i++) reClass.test(aElement[i].className) && aClass.push(aElement[i]);
	return aClass
};
function css (element, attr, value)
{
	if (arguments.length == 2)
	{
		if (typeof arguments[1] === "string")
		{
			return element.currentStyle ? element.currentStyle[attr] : getComputedStyle(element, null)[attr]	
		}
		else
		{
			for (var property in attr)
			{
				property == "opacity" ?
				(element.style.filter = "alpha(opacity=" + attr[property] + ")", element.style.opacity = attr[property] / 100) :
				element.style[property] = attr[property]
			}
		}
	}
	else if (arguments.length == 3)
	{
		switch (attr)
		{
			case "width":
			case "height":
			case "top":
			case "left":
			case "right":
			case "bottom":
				element.style[attr] = value + "px";
				break;
			case "opacity" :
				element.style.filter = "alpha(opacity=" + value + ")";
				element.style.opacity = value / 100;
				break;
			default :
				element.style[attr] = value;
				break
		}	
	}
	return element
};
function Animate (element, options, fnCallBack)
{
	this.obj = $(element);
	this.options = options;
	this.__onEnd__ = fnCallBack;
	this.__startMove__()
};
Animate.prototype.__startMove__ = function ()
{
	var that = this;
	clearInterval(that.obj.timer);
	that.obj.timer = setInterval(function ()
	{
		that.__doMove__()
	}, 30);
};
Animate.prototype.__doMove__ = function ()
{
	var complete = true;
	var property = null;
	for (property in this.options)
	{
		var iCur = parseFloat(css(this.obj, property));
		property == "opacity" && (iCur = parseInt(iCur.toFixed(2) * 100));
		var iSpeed = (this.options[property] - iCur) / 5;
		iSpeed = iSpeed > 0 ? Math.ceil(iSpeed) : Math.floor(iSpeed);
		this.options[property] == iCur || (complete = false, css(this.obj, property, iSpeed + iCur))
	}
	complete && (clearInterval(this.obj.timer), this.__onEnd__ && this.__onEnd__.apply(this.obj))
};
window.onload = function ()
{
	new Youa("box")
};