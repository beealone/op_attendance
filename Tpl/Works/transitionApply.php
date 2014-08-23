<div class="main_include">
    <div id="ext_mosearchmanage"  style="padding:10px;height:auto">
     <form id="transitionApply_Form" method="post">
     <?php if ($teamlist[0]['teamid']!=1){ ?>
        选择员工
        <select id="uid">
            <?php for($i=0;$i<count($teamlist);$i++){
                        echo "<option value='".$teamlist[$i]['uid']."'>".$teamlist[$i]['username'];	
                  }
            ?>	  
        </select>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php }?>
        选择事务：
        <select id="transName">
        		<option value=''></option>
        		<option value="1">休假申请</option>
                <option value="2">加班申请</option>
                <option value="3">出差申请</option>
        </select>
        开始日期：
            <input id="trans_begin_date" type="text" style="width:100px" />
        开始时间：
        	<input type="text" id="trans_starttime" class="easyui-timespinner"  value="08:30" />
        结束日期：
            <input id="trans_end_date" type="text" style="width:100px" />
        结束时间：
        	<input type="text" id="trans_endtime" class="easyui-timespinner"  value="17:30" />
        <br/><br />
        申请理由：<br />
        	&nbsp;&nbsp;&nbsp;&nbsp; 
            &nbsp;&nbsp;&nbsp;&nbsp;
            <textarea id="reason" rows="8" cols="100"></textarea><br />
            &nbsp;&nbsp;&nbsp;&nbsp; 
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a class="easyui-linkbutton" iconCls="icon-search" onclick="apply()">提交</a>
       	
     </form> 
    </div>
    <script>
	$('#trans_begin_date').datebox({	
			formatter:timeformatter,
			parser:timeparser,
			editable:false,
			width: 100
	});
	$('#trans_end_date').datebox({
			formatter:timeformatter,
			parser:timeparser,
			editable:false,
			width: 100		
	});
	function sendMEmail(content,emailAddress){}
	function apply(){
		var transdm=$("#transName").val();
		if(transdm==""){
			$.messager.alert('提示','请选择事务类型！');
			return false;
		}
		var begindate=$('#trans_begin_date').combo("getValue");
		var enddate=$("#trans_end_date").combo("getValue");
		var begintime=$('#trans_starttime').val();
		var endtime=$("#trans_endtime").val();
		if(begindate==""||enddate==""){
			$.messager.alert("提示","请同时填写开始日期和结束日期！");
			return false;
		}
		if(enddate<begindate){
			$.messager.alert("提示","结束日期不能早于开始日期！");
			return false;
		}
		if((enddate==begindate)&&(endtime<begintime)){
			$.messager.alert("提示","同一天结束时间不能早于开始时间！");
			return false;
		}
		var reason=$("#reason").val();
		if(reason==""){
			$.messager.alert("提示","请填写申请理由！");
			return false;
		}
		var begin=begindate+" "+begintime;
		var end=enddate+" "+endtime;
		var diff=computeDateDiff(begin,end);
		$.ajax({
			url: "__APP__/Works/dealApply",
			data:{
				begindate:begindate,
				enddate:enddate,
				begintime:begintime,
				endtime:endtime,
				reason:reason,
				transdm:transdm
			},
			type:'POST',
			success:function(data){
				if(data=="1"){
					$.messager.alert("提示","提交成功！");
				}				
			},
			error:function(XMLHttpRequest,textStatus,errorThrown){
				alert(''+errorThrown)
			}
		});
		
		
		
	}
	function computeDateDiff(begin,end){
		var begintime=begin.replace(/-/g,"/");
		var endtime=end.replace(/-/g,"/");
		begintime=new Date(begintime);
		endtime=new Date(endtime);
		var diff=parseInt((endtime.getTime()-begintime.getTime()));
		diff=diff/(1000*60*60);
	//	alert(diff);
		return diff;
	}
	
	</script>
    
</div>