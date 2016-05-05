<?php 

	// TODO
	// Função para criar Novo usuário
	// Função para deletar usuário


	/**
	* Informações do usuário
	*/
	class usuario
	{

		private $id;
		public $nome;
		public $email;
		public $ultimo_login;
		private $ativo;
		private $excluido;

		function recuperaInfo($id)
		{
			include ("../config/conn.php");

			$usuario = $conn->prepare("SELECT nome, email, ultimo_login, ativo, excluido FROM usuarios WHERE id = ?");
			$usuario->bind_param("i", $id);

			$usuario->execute();

			$usuario->bind_result($this->nome, $this->email, $this->ultimo_login, $this->ativo, $this->excluido);
			$this->id = $id;
			$usuario->fetch();
			$usuario->close();
			
		}// fim do recuperaInfo

		function validaUsuario($id){

			if($this->excluido == 1){
				throw new Exception("Este usuário está excluído e portanto não pode ser ativado", 1);
			}

			else if($this->ativo == 1){
				throw new Exception("Usuário já está ativo", 2);
			}

			else {

				include ("../config/conn.php");

				$validacao = $conn->prepare("UPDATE usuarios SET ativo = 1  WHERE id = ?");
				$validacao->bind_param("i", $id);
				$validacao->execute();

				return $this->ativo == 1;

			}

		}// fim da função validaUsuario()


	}//fim da classe 'usuario'


	/**
	* Login no sistema
	*/
	class login extends usuario
	{

		private $id;
		public $usuario;
		private $senha;

		
		function __construct($usuario, $senha)
		{
			include ("../config/conn.php");

			$entrar = $conn->prepare("SELECT id FROM login WHERE usuario = ? AND senha = ?");
			$entrar->bind_param("ss", $usuario, $senha);

			$entrar->execute();
			$entrar->store_result();

			if ($entrar->num_rows == 1){

				$entrar->bind_result($this->id);
				$entrar->fetch();

				parent::recuperaInfo($this->id);
				$this->ultimo_login = date('Y-m-d H:i:s');

				$update = $conn->prepare("UPDATE usuarios SET ultimo_login = ? WHERE id = ?");
				$update->bind_param("si", $this->ultimo_login, $this->id);

				$update->execute();

				return true;

			}

			else {

				throw new Exception("Usuário ou senhas inválidos", 3);
				
			}
		
		} // fim do __construct

		private function logout($id){

			include ("../config/conn.php");

			$update = $conn->prepare("UPDATE usuarios SET logado = 0 WHERE id = ?");
			$update->bind_param($id);
			$update->execute();

			return true;

		} // Fim do logout

		function novaSenha($id, $nova_senha, $velha_senha = null, $token = null){

			if (is_null($velha_senha) && !is_null($token)){ // Se o token já foi verificado no objeto de token de email

				include ("../config/conn.php");

				$senha = $conn->prepare("UPDATE login SET senha = ? WHERE id = ?");
				$senha->bind_param("si", $nova_senha, $id);
				$senha->execute();
				
				if ($senha->affected_rows == 1){
					return true;
				}
				else{
					throw new Exception("Erro não esperado", 3);
				}
			}
			else if (!is_null($velha_senha) && is_null($token)){ // Vai ainda verificar a senha velha
				
				include ("../config/conn.php");

				$senha = $conn->prepare("UPDATE login SET senha = ? WHERE id = ? and senha = ?");
				$senha->bind_param("ssi", $novaSenha, $velhaSenha);

				$senha->execute();

				if ($senha->affected_rows == 1){
					return true;
				}
				else{
					throw new Exception("Senha antiga incorreta", 4);
				}

			}//

		} // Fim da função novaSenha

	} // Fim da classe login

 ?>