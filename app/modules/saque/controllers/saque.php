<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
 
class saque extends MX_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		// permission_view();
	}

	public function index(){
		// permission_view(true);
		$user_id = session("uid");
		$user = $this->model->get("*",USER_MANAGEMENT,"id = '".$user_id."'");
		$data = array(
			"user" => $user,
			"saldo" => $this->model->getUserSaldo($user->id),
			"extratos" => $this->model->getUserExtrato($user->id),
			"conta" => $this->model->getUserContaBancaria($user_id)
		);
		// print_r($data);

		$this->template->title(l('saque'));
		$this->template->build('index', $data);
	}
	public function sacar(){
		$valor = post('money');
		$user_id = session("uid");
		$user = $this->model->get("*",USER_MANAGEMENT,"id = '".$user_id."'");
		$resposta = array();
		$userSaldo = $this->model->getUserSaldo($user_id);
		if($valor >= 100 ){
			if($userSaldo->disponivel >= 100 or $userSaldo->disponivel >= $valor){
				$resposta["status"] = $this->model->solicitarSaque($user_id,$user->fullname,$valor,$userSaldo->disponivel - $valor, $userSaldo->sacado + $valor);
			}else{
				$resposta["status"] = false;
				$resposta["mensagem"] = "saldo insuficiente";
			}
		}else{
			$resposta["status"] = false;
			$resposta["mensagem"] = "valor invalido";
		}
		// return $resposta;
		// print_r($resposta);
		redirect("saque");
	}
	public function salvarConta(){
		$conta = post("conta");
		$agencia = post("agencia");
		$banco = post("banco");
		$user_id = session("uid");
		print_r($conta);
		print_r($agencia);
		print_r($banco);
		$userConta = $this->model->getUserContaBancaria($user_id);
		if($userConta){
			$this->model->saveUserContaBancaria($conta,$agencia,$banco,$user_id);
		}else{
			$this->model->criaUserContaBancaria($conta,$agencia,$banco,$user_id);
		}
		redirect("saque");

	}

	


}