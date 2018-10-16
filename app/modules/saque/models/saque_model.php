<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class saque_model extends MY_Model {
	public function __construct(){
		parent::__construct();
	}
	public function getUserSaldo($id){
		$this->db->select("*");
		$this->db->where("id_usuario", $id);
		$saldo = $this->db->get("saldo")->row(0);
		if(!$saldo){
			$this->db->set("id_usuario", $id);
			$this->db->insert("saldo");
			
			
			$this->db->select("*");
			$this->db->where("id_usuario", $id);
			$saldo = $this->db->get("saldo")->row(0);
		}
		return $saldo;
	}
	public function getUserExtrato($id){
		$this->db->select("*");
		$this->db->where("id_pagante", $id);
		$this->db->or_where("id_recebedor", $id);
		return $this->db->get("extrato")->result();
	}
	
	function getUserContaBancaria($id){
		$this->db->select("*");
		$this->db->where("id_usuario", $id);
		return $this->db->get("conta_bancaria")->row(0);
	}
	function saveUserContaBancaria($conta,$agencia,$banco,$id){
		$this->db->set("conta",$conta );
		$this->db->set("agencia ",$agencia );
		$this->db->set("numeroBanco ", $banco );
		$this->db->where("id_usuario", $id);
		$this->db->update("conta_bancaria");
	}
	function criaUserContaBancaria($conta,$agencia,$banco,$id){
		$this->db->set("conta",$conta );
		$this->db->set("agencia ",$agencia );
		$this->db->set("numeroBanco ", $banco );
		$this->db->set("id_usuario", $id);
		$this->db->insert("conta_bancaria");
	}
	
	
	function solicitarSaque($userId,$nome,$valor,$newDisponivel,$newSacado){
		$this->db->set("id_pagante", $userId);
		$this->db->set("nome_pagante", $nome);
		$this->db->set("valor", $valor);
		$this->db->set("id_recebedor", 0);
		$this->db->set("nome_recebedor", "conta bancaria");
		$this->db->set("razao", "transferencia bancaria");
		$this->db->insert("extrato");
		
		$this->db->set("disponivel ",$newDisponivel );
		$this->db->set("sacado ", $newSacado );
		$this->db->where("id_usuario", $userId);
		$this->db->update("saldo");
		
	}
	
}
