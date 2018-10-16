<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
 
class transferencia extends MX_Controller {

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
			"extratos" => $this->model->getUserExtrato($user->id)
		);
		// print_r($data);

		$this->template->title(l('saque'));
		$this->template->build('index', $data);
	}

	public function transferir(){

		$data = post();
		$valor = $data["valor"];
		$destinatario_code = $data["destinatario"];

		$user_id = session("uid");
		
		$usuario = $this->model->getUserResumedInfoById($user_id);
		if($destinatario = $this->model->getUserResumedInfoByCode($destinatario_code)){
		
			if($valor > 0) {
				if($usuario->disponivel >= $valor) {
					if($this->model->doTransfer($usuario, $valor, $destinatario)) {
						$this->model->insertExtrato($usuario, $valor, $destinatario);
					}			
				}
			}
		}
		redirect(url('transferencia'));
	}

}