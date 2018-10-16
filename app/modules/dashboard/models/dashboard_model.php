<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class dashboard_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}

	public function getActivitys(){
		$logs_counter = $this->model->fetch("logs_counter",INSTAGRAM_ACCOUNTS, "uid = '".session("uid")."'");
		$result = (object)array();
		$result->like_count        = 0;
		$result->comment_count     = 0;
		$result->follow_count      = 0;
		$result->like_follow_count = 0;
		$result->followback_count  = 0;
		$result->unfollow_count    = 0;
		$result->repost_count      = 0;
		$result->deletemedia_count = 0;
		if(!empty($logs_counter)){
			foreach ($logs_counter as $key => $value) {
				$result->like_count       	+= isset(json_decode($value->logs_counter)->like)?json_decode($value->logs_counter)->like:0;
				$result->comment_count    	+= isset(json_decode($value->logs_counter)->comment)?json_decode($value->logs_counter)->comment:0;
				$result->follow_count     	+= isset(json_decode($value->logs_counter)->follow)?json_decode($value->logs_counter)->follow:0;
				$result->like_follow_count	+= isset(json_decode($value->logs_counter)->like_follow)?json_decode($value->logs_counter)->like_follow:0;
				$result->followback_count 	+= isset(json_decode($value->logs_counter)->followback)?json_decode($value->logs_counter)->followback:0;
				$result->unfollow_count   	+= isset(json_decode($value->logs_counter)->unfollow)?json_decode($value->logs_counter)->unfollow:0;
				$result->repost_count     	+= isset(json_decode($value->logs_counter)->repost)?json_decode($value->logs_counter)->repost:0;
				$result->deletemedia_count	+= isset(json_decode($value->logs_counter)->deletemedia)?json_decode($value->logs_counter)->deletemedia:0;
			}
		}
		return $result;
	}
	
	public function getSaldo(){
		$result = (object)array();
		$result->disponivel        = 0;
		$result->blokeado        = 0;
		$result->acomulado        = 0;
		$result->gasto        = 0;
		$result->sacado        = 0;
		$this->db->select('*');
		$this->db->where('id_usuario', session("uid"));
		$resultado = $this->db->get('saldo')->row(0);
		if($resultado){
			return $resultado;

		}else{
			return $result;
		}
				
	}
			

	public function activity_count($type){
		$this->db->select('status, COUNT(status) as total');
		$this->db->where("category", $type);
		$this->db->where("uid", session("uid"));
		$this->db->group_by('status'); 
		$this->db->order_by('status', 'desc'); 
		$query = $this->db->get(INSTAGRAM_SCHEDULES);
		if($query->result()){
			return $query->result();
		}else{
			return false;
		}
	}
}
