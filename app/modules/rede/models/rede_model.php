<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class rede_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}


	public function getAllAccount(){
		$cate   = $this->model->get("*", CATEGORIES, "id = '".session("category")."' AND category = 'post'");

		$this->db->select("*");

		if(session("category") && !empty($cate)){
			$group_id = json_decode($cate->data);
			if(!empty($group_id)){
				$this->db->where_in("username", $group_id);
			}
		}
		
		$this->db->where("status = 1");
		$this->db->where("uid = '".session("uid")."'");
		$this->db->order_by("id", "desc");
		$query = $this->db->get(INSTAGRAM_ACCOUNTS);
		$result = $query->result();

		return $result;
	}

	public function getID($indicator) {
		$this->db->select("usuarios.id");
		$this->db->where("codigoIndicacao", $indicator);
		return $this->db->get("usuarios")->row(0);
	}

	public function getUserList($code){
		$this->db->select("*");
		$this->db->where("codigo_pai ", $code);
		$result = $this->db->get("usuarios")->result();
		if(!empty($result)){
			foreach ($result as $key => $row) {
				$package = $this->model->get("*", PACKAGE, "id = '".$row->package_id."' AND status = 1");
				if(!empty($package)){
					$result[$key]->package_name = $package->name;
				}
			}
		}
		return $result;
	}

	public function mCodigo($id){
		$this->db->select("usuarios.codigoIndicacao");
		$this->db->where("id", $id);
		return $this->db->get("usuarios")->row(0);
	}
}
