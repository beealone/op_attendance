<?php 
	
	class WorksAction extends Action{
		
/*****************************************员工事务申请页面begin*****************************************/

		//获取本产线员工名单
		public function transitionApply(){
			$Model=M('staffinfo');
			$uid=$_SESSION['uid'];
			$tids=$Model->getByUid($uid);
			$tid=$tids['teamid'];
			$rs=$Model->where('teamid="'.$tid.'"')->select();
			$this->assign('teamlist',$rs);
			$this->display();
		}
		//邮件发送
		public function sendMail($mail,$uid){
		}
		
		//申请事务提交
		public function dealApply(){
			
			$begindate=$this->_post('begindate');
			$enddate=$this->_post('enddate');
			$begintime=$this->_post('begintime');
			$endtime=$this->_post('endtime');
			$reason=$this->_post('reason');
			$transdm=$this->_post('transdm');
			$begin=$begindate." ".$begintime;
			$end=$enddate." ".$endtime;
			$uid = $_SESSION['uid'];
			$Model=M('userinfo');
			$tidrow=$Model->getByUid($uid);
			$tid=$tidrow['usertypeid'];
			$Model=M('usertype');
			$powerrow=$Model->getByTid($tid);
			$power=$powerrow['power'];
			$status=1;
			$managerid=2;
			if($power!=2&&$power!=4&&$power!=5){
				$model=M('userinfo');
				$row=$model->getByUid($uid);
				$departmentid=$row['departmentid'];
				
				$rows=$model->field("op_userinfo.uid")
				->join('op_usertype ON op_userinfo.usertypeid=op_usertype.tid ')
				->where("op_usertype.power=4")->select();
				$managerid=$rows[0]['uid'];
			}
			$astatus['uid']=$uid;
			$astatus['transtype']=$transdm;
			$astatus['begintime']=$begin;
			$astatus['status']=$status;
			$astatus['endtime']=$end;
			$astatus['applytime']=date('Y-m-d H:i:s');
			$astatus['departmanagerid']=$managerid;
			$astatus['reason']=$reason;
			
			$model=M('vacationstatus');
			$rs=$model->add($astatus);
			if(!$rs) {
							$model->rollback();
							echo '信息添加失败，请重试！';
							exit;	
			}
			
			$model->commit();
	//		sendMail($reason,$uid);
			echo "1";
			
		}
/*****************************************员工事务申请页面end*****************************************/

/*****************************************审批页面begin*****************************************/

	public function jbAproval(){
		
		$this->display();
	}
	
	public function getTransitionByType(){
		$uid = $_SESSION['uid'];
		$typeid=$this->_get('tid');
		$page = $this->_post('page');
		if($page<1) $page=1;
		$rows = $this->_post('rows');
		if($rows<1) $rows=10;
		$start = ($page-1)*$rows;
		$model=M('vacationstatus');
		$where="status=1 and departmanagerid='".$uid."' and isrejected!='1' and isapproved!='1' and transtype='".$typeid."' ";
		$num=$model->where($where)->count();
	//	echo $model->where($where)->getLastSql();
		$list=$model->field("op_vacationstatus.uid,op_vacationstatus.begintime,op_vacationstatus.endtime,op_vacationstatus.applytime,op_department.departmentname,
		op_teaminfo.teamname,op_staffinfo.username,op_vacationstatus.id")
		->join("op_staffinfo ON op_vacationstatus.uid=op_staffinfo.uid")
		->join("op_teaminfo ON op_staffinfo.teamid=op_teaminfo.tid")
		->join("op_department ON op_staffinfo.departmentid=op_department.did")
		->where($where)
		->order("op_vacationstatus.applytime desc")
		->limit("$start,$rows")
		->select();
		
		echo dataToJson($list,$num);
		
	}
	

/*****************************************审批页面end*****************************************/

/*****************************************事务申请详情页面begin*****************************************/

	public function transitionDetail(){
		$vid=$this->_get('vid');
		$model=M('vacationstatus');
		$detail=$model->
		field("op_vacationstatus.reason,op_vacationstatus.begintime,op_vacationstatus.endtime,op_vacationstatus.applytime,op_staffinfo.username")
		->join("op_staffinfo ON op_vacationstatus.uid=op_staffinfo.uid")
		->where("op_vacationstatus.id='".$vid."'")->select();
		$this->assign('detail',$detail);
		
		
		$this->display();
	}

/*****************************************事务申请页面详情end*******************************************/

/*****************************************驳回申请begin*******************************************/
	public function rejectTrans(){
		$vid=$this->_get('vid');
		$model=M('vacationstatus');
		$rs=$model->getById($vid);
		$rs['isrejected']=1;
		$result=$model->save($rs);
		if(!$result){
			echo "驳回失败,请重试！";
			exit;
		}
		else{
			$data="1";
			echo $data;
		}
	}

/*****************************************驳回申请end*******************************************/		
/*****************************************批准申请begin*******************************************/
	public function approveTrans(){
		
		$id=$this->_get('vid');
		
		$model=M('vacationstatus');
		$rs=$model->getById($id);
		$rs['isapproved']=1;
		$result=$model->save($rs);
		if(!$result){
			echo "操作失败,请重试！";
			exit;
		}
		else{
			$data="1";
			echo $data;
		}
	}

/*****************************************批准申请end*******************************************/		
	
	
	
	
	
	
	}
	
	
	
		

?>