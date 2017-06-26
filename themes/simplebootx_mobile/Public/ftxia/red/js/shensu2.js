
function huishensu(did){
  
         $("#didcc").attr('value',did);
         
        $(".huishen").show();
		$(".zhezhao").show();
  }

function guanbi()
  {     $(".huishen").hide();
        $(".chashen").hide();
		$(".zhezhao").hide();
		$(".huishen").hide();
  }
  
  $(function(){
  
	$(".wodel").click(function (e){
	      $(this).css({"background":"#DCDCDC"});
		  $(".wodel").css({"border":"1px solid red"});
		  $(".woder").css({"border":"0"});
		  $(".woder").css({"background":"#FFF"});
	     $("#jifga").show();
		$("#jifg").hide();
		
	});
   $(".woder").click(function (e){
        $(this).css({"background":"#DCDCDC"});
	    $(".wodel").css({"background":"0"});
		$(".woder").css({"border":"1px solid red"});
		 $(".wodel").css({"border":"0"});
        $("#jifg").show();
		$("#jifga").hide();	
	});
	
	
});

 function chasheni(did)
  
  {  
cahshe(did);
 // $(".didhidden").attr("value",did);
        $(".chashen").show();
		$(".zhezhao").show();
  }
   function guanbi()
  {
        $(".chashen").hide();

		$(".zhezhao").hide();
		 $(".huishen").hide();
		// alert("sss");
  }
  
  
  function cahshe(did)
  {
 
    
	    var obj=this;
	
		
		var ajax_url="?deala=dela&didju="+did;
	 
		$.ajax({
			type:'get',
			url:ajax_url,
			dataType: "html",
			success:function (data){
			
			/*$("#qing1").html("<td >"+data.qianggou+"	</td><td>«¿π∫…Ã∆∑</td>");
			if(data.t_dan !=null){
			  $("#qing2").html("<td >"+data.t_dan+"	</td><td>ÃÓ–¥µ•∫≈</td>");
			}else{
			 $("#qing2").html("<td >	</td><td></td>");
			}
		  */
			  $("#xingn").html(data);
			}
		})
  }
  
  
  function wosheni(did)
  
  {  

         wohshe(did);
        $(".chashen").show();
		$(".zhezhao").show();
  }
   function guanbi()
  {
        $(".chashen").hide();
		
		$(".zhezhao").hide();
			 $(".huishen").hide();
  }
  
  
  function wohshe(did)
  {
 
    
	    var obj=this;
	
		
		var ajax_url="?wodeala=dela&wodidju="+did;
	 
		$.ajax({
			type:'get',
			url:ajax_url,
			dataType: "html",
			success:function (data){
			
			
		    $("#xingn").html(data);
		
			}
		})
  }