<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class transferencia_model extends MY_Model {
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

	public function getUserResumedInfoById($id) {
        $this->db->select("usuarios.id as userID, fullname, saldo.*");
        $this->db->where("usuarios.id", $id);
        $this->db->join("saldo", "usuarios.id = saldo.id_usuario", "left");
        return $this->db->get("usuarios")->row(0);
    }

    public function getUserResumedInfoByCode($code) {
        $this->db->select("usuarios.id as userID, fullname, saldo.*");
        $this->db->where("usuarios.codigoIndicacao", $code);
        $this->db->join("saldo", "usuarios.id = saldo.id_usuario", "left");
        return $this->db->get("usuarios")->row(0);
    }
	
	public function doTransfer($pagante, $valor, $recebedor) {

        $pagante_novo_saldo = $pagante->disponivel - $valor;
        $recebedor_novo_saldo = $recebedor->disponivel + ($valor - (($valor * 5) / 100));

        $result_pagante = $this->setSaldo($pagante->userID, $pagante_novo_saldo);
        $result_recebedor = $this->setSaldo($recebedor->userID, $recebedor_novo_saldo);

        return $result_pagante && $result_recebedor;
    }

    public function setSaldo($who, $qtd) {
        $this->db->set("disponivel", $qtd);
		$this->db->where("id_usuario", $who);
		return $this->db->update("saldo");
    }

	public function insertExtrato($pagante, $valor, $recebedor) {
        $this->db->set("id_pagante", $pagante->userID);
        $this->db->set("nome_pagante", $pagante->fullname);
        $this->db->set("valor", ($valor - (($valor * 5) / 100)));
        $this->db->set("id_recebedor", $recebedor->userID);
        $this->db->set("nome_recebedor", $recebedor->fullname);
        $this->db->set("razao", "transferencia");
        return $this->db->insert("extrato");
    }
	
	public function getUserExtrato($id){
		$this->db->select("*");
		$this->db->where("id_pagante", $id);
		$this->db->or_where("id_recebedor", $id);
		return $this->db->get("extrato")->result();
	}

}
