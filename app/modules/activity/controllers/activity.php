<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
 
class activity extends MX_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		permission_view();
	}

	public function index(){
		$data = array(
			"result"     => $this->model->getAllSchedules(),
		);
		$this->template->title(l('Auto activity')); 
		$this->template->build('index', $data);
	}

	public function settings(){
		$id = (int)get("id");
		$item = $this->model->get("*", INSTAGRAM_ACTIVITY, "id = '".$id."'  AND uid = '".session("uid")."'");
		$accounts = $this->model->fetch("*", INSTAGRAM_ACCOUNTS, "uid = '".session("uid")."' AND checkpoint = 0 ");
		if(!empty($item)){
			$accounts = $this->model->fetch("*", INSTAGRAM_ACCOUNTS, "id = '".$item->account_id."' AND uid = '".session("uid")."'  AND checkpoint = 0 ");
		}
		$data = array(
			"accounts"   => $accounts,
			"item"       => $item,     
		);
		
		$this->template->title(l('Auto activity')); 
		$this->template->build('update', $data);
	}

	public function disconnect(){
		$id = (int)post("id");
		$item = $this->model->get("*", INSTAGRAM_ACTIVITY, "id = '".$id."' AND uid = '".session("uid")."'");
		if(!empty($item)){
			$this->db->delete(INSTAGRAM_SCHEDULES, "account_id = '".$item->account_id."' AND (category = 'like_follow' OR category = 'like' OR category = 'comment' OR category = 'follow' OR category = 'followback' OR category = 'unfollow' OR category = 'repost' OR category = 'deletemedia')");
			$this->db->delete(INSTAGRAM_HISTORY, "account_id = '".$item->account_id."'");
			$this->db->delete(INSTAGRAM_ACTIVITY, "account_id = '".$item->account_id."'");
		}

		ms(array(
			'st' 	=> 'success',
			'txt' 	=> l('Successfully')
		));
	}
}