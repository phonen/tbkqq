var t = Date.parse(new Date());
var sToday = new Date(XDTOOL.getCookie('server_time') * 1000) - t;
var JP_time = function(d){
	var Today;
	var Temp;
	

	var showtime = function () {

		Today = new Date((new Date()).getTime() + sToday);
        var left = d.getTime() - Today.getTime();
        left = parseInt(left/1000);
        var Dateleft = parseInt(left/86400);
        left = left%86400;
        var Hourleft = parseInt(left/3600);
        left = left%3600;
        var Minuteleft = parseInt(left/60);
        left = left%60;
        var Secondleft = left;

        Temp={D:Dateleft,H:Hourleft,M:Minuteleft,S:Secondleft};
        if(d > Today ){
            return {timerRunning:true,data:Temp};
        }else{
            return {timerRunning:false,data:Temp};
        }

	}
	
	var timerID = null;   
	var timerRunning = false;   
	var stopclock = function() {   
		if(timerRunning) {clearTimeout(timerID)};   
		timerRunning = false;   
	}   
	var startclock = function(){   
		stopclock(); 
		showtime();   
	}

	return showtime();
}
