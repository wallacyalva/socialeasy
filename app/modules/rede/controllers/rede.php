<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
 
class rede extends MX_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model(get_class($this).'_model', 'model');
		// permission_view();
	}

	public function index(){
		// permission_view(true);
		$user_id = session("uid");

		$nivel = $_GET["nivel"] ? $_GET["nivel"] : 1;

		if($_GET["usuario"]) {
			$user_id = $_GET["usuario"];
			$user = $this->model->get("*",USER_MANAGEMENT,"codigoIndicacao = '".$_GET["usuario"]."'");
		} else {
			$user = $this->model->get("*",USER_MANAGEMENT,"id = '".$user_id."'");
		}

		$data = array(
			"user" => $user,
			"result" => $this->model->getUserList($user->codigoIndicacao),
			"nivel" => $nivel
		);

		$this->template->title(l('rede view'));
		$this->template->build('index', $data);
	}

	public function nivel($codigo){
		permission_view(true);
		$user_id = session("uid");
		$data = array(
			"blabla" => post(),
			"result" => $this->model->getUserList(),
			"mcodigo" => $codigo
		);
		$this->template->title(l('rede view'));
		$this->template->build('index', $data);
	}


}