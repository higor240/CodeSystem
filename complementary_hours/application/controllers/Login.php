<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	private $dados;

	function __construct(){
		parent::__construct();
		$this->load->model('Login_model');

		$this->dados['tentou']   = FALSE;
		$this->dados['mensagem'] = "";
	}

	public function index(){
		$this->load->view('login', $this->dados);
		$this->load->view('include/footer');
	}

	private function falha_na_autenticacao(){
		$this->dados['tentou']   = TRUE;
		$this->dados['mensagem'] = "Email ou senha incorretos, tente novamente!";
		$this->index();
	}
	
	public function autenticar() {
		$usuario_email = $this->input->post('email',TRUE);
		$usuario_senha = $this->input->post('senha',TRUE);
		$result        = $this->Login_model->checar_usuario($usuario_email);
		if ($result->num_rows() > 0) {
			$dados = $result->row_array();
			if (password_verify($usuario_senha, $dados['usuario_senha'])) {
				$dados_sessao = array(
					'nome'      => $dados['pessoa_nome'],
					'sobrenome' => $dados['pessoa_sobrenome'],
					'campus'    => $dados['campus_descricao'],
					'curso'     => $dados['curso_descricao'],
					'telefone'  => $dados['pessoa_telefone'],
					'email'     => $dados['usuario_email'],
					'senha'     => $dados['usuario_senha'],
					'nivel'     => $dados['usuario_nivel'],
					'logado'    => TRUE
				);
		
				$this->session->set_userdata($dados_sessao);
				redirect(base_url('Home'));
			} else {
				$this->falha_na_autenticacao();
			}
		} else {
			$this->falha_na_autenticacao();
		}
	}

	function logout() {
		$this->session->sess_destroy();
		redirect(base_url());
	}
}
