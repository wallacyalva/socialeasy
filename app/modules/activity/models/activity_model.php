<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class activity_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}

	public function getAllSchedules(){
		$this->db->select("activity.*, user.avatar, user.checkpoint,user.logs_counter");
		$this->db->from(INSTAGRAM_ACTIVITY." as activity");
		$this->db->join(INSTAGRAM_ACCOUNTS." as user", 'user.id = activity.account_id');
		$this->db->where("activity.uid = '".session("uid")."'");
		$keyword = clean(get('keyword'));
		if($keyword != ""){
			$this->db->like('account_name', $keyword);
		}

		switch (get('filter')) {
			case 'started':
				$this->db->where(" activity.status = 5  ");
				break;

			case 'stoped':
				$this->db->where(" activity.status = 3  ");
				break;

			case 'stoped':
				$this->db->where(" activity.status = 1 ");
				break;
		}

		switch (get('sort')) {
			case 'username':
				$this->db->order_by("activity.account_name", "asc");
				break;

			case 'time':
				$this->db->order_by("activity.created", "desc");
				break;

			default:
				$this->db->order_by("activity.id", "asc");
				break;
		}
		$query = $this->db->get();
		$result = $query->result();
		return $result;
	}
}
