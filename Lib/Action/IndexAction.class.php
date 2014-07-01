<?php
class IndexAction extends Action {

	public function login() {
		$loginname = $this->_post('loginname');
		$password = $this->_post('password');
		$Model = M('userinfo');
		$rs = $Model->getByLoginname($loginname);
		if($rs)
		{
			if($rs['password']==md5($password))
			{
				$_SESSION['uid']=$rs['uid'];
				$_SESSION['username']=$rs['username'];
				$_SESSION['loginname']=$rs['loginname'];
				$Model = M('usertype');
				$role = $Model->getByTid($rs['usertypeid']);
				$_SESSION['menu']=$role['menu'];
				$_SESSION['power']=$role['power'];
				$_SESSION['typename']=$role['typename'];
				$Model = M('department');
				$role = $Model->getByDid($rs['departmentid']);
				$_SESSION['departmentname']=$role['departmentname'];
				$this->redirect('__APP__/Index/center');
			} else {
				$this->error('对不起，您的密码有误！');	
			}
		} else {
			$this->error('对不起，您输入的用户名有误！');
		}
	}

	public function logout() {
		unset($_SESSION['username']);
		unset($_SESSION['loginname']);
		unset($_SESSION['menu']);
		$this->redirect('__APP__/Index/index');
    }

	public function fetch_static() {
		$uid = $_SESSION['uid'];
		$groupname1 = '出勤情况';
		$Model = M('unusualtime');
		//获取项目总数
		$rs = $Model->where("uid='$uid' and isapply=0")->count();
		if($rs>0) {
			$rs = '<span style="color:red;">'.$rs.'</span>';	
		}
		$rs = $rs.' <a style="float:right" onclick="openTab(\'异常记录\',\'Project/my_project\')">[查看]</a>';
		$data[] = array('name'=>'异常记录', 'value'=>$rs, 'group'=>$groupname1);
		echo dataToJson($data,count($data));
	}
	
	public function fetch_totalmsg() {
		$uid = $_SESSION['uid'];
		$power = $_SESSION['power'];
		$field = $_SESSION['field'];
		$groupname1 = '消息统计';
		//获取收件箱总数
		$Model = M('mailinbox');
		$rs = $Model->where("touid=$uid")->count();
		$rs = $rs.' <a style="float:right" onclick="openTab(\'收件箱\',\'Mail/mail_inboxshow\')">[查看]</a>';
		$data[] = array('name'=>'收件箱', 'value'=>$rs, 'group'=>$groupname1, 'title'=>'收件箱', 'path'=>'Mail/inbox');
		//获取未读邮件
		$rs = $Model->where("touid=$uid and isread=0")->count();
		$rs = '<span style="color:red">'.$rs.'</span> <a style="float:right" onclick="openTab(\'收件箱\',\'Mail/mail_inboxshow\')">[查看]</a>';
		$data[] = array('name'=>'未读消息', 'value'=>$rs, 'group'=>$groupname1, 'title'=>'收件箱', 'path'=>'Mail/inbox');
		//获取发件箱总数
		$Model = M('mailtobox');
		$rs = $Model->where("fromuid=$uid")->count();
		$rs = $rs.' <a style="float:right" onclick="openTab(\'发件箱\',\'Mail/mail_toboxshow\')">[查看]</a>';
		$data[] = array('name'=>'发件箱', 'value'=>$rs, 'group'=>$groupname1, 'title'=>'发件箱', 'path'=>'Mail/outbox');
		echo dataToJson($data,count($data));
	}
	
}

?>