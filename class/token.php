<?php 

	// TODO
	// Fazer essa classe enviar email de fato

	/**
	* Gera tokens e verifica os mesmos se são válidos, e os envia por e-mail
	*/
	class tokenEmail
	{

		private $id;
		public $usuario;
		public $data_inicial;
		public $tipo; // 0 - Confirmação 1 - Mudança de senha 2 - Exclusão
		private $status;
		
		function confereToken($token)
		{
			if ($args[] = json_decode(base64_decode($token), TRUE)) {

				$this->id = $args['id'];
				$this->usuario = $args['usuario'];
				$this->data_inicial = $args['data_inicial'];
				$this->tipo = $args['tipo'];

				include ("../config/conn.php");

				$confere = $conn->prepare("SELECT status 
					FROM 
						token 
					WHERE 
						id = ? AND
						usuario = ? AND
						(data_inicial = ? AND data_inicial >= DATE_SUB(NOW(), INTERVAL 30 day)) AND
						tipo = ?");

				$confere->bind_param("issi", $this->id, $this->usuario, $this->data_inicial, $this->tipo);

				$confere->execute();

				$confere->store_result();

				if ($confere->num_rows == 1){

					$confere->bind_result($this->status);
					$confere->fetch();

					return TRUE;

				}

				else{
					throw new Exception("Token inválido", 5);
					
				}


			}
			else{
				
				throw new Exception("Token inválido", 5);

			}


		}

		function geraToken($usuario, $tipo){

			include ("../config/conn.php");

			$this->usuario = $usuario;
			$this->data_inicial = date('Y-m-d H:i:s');
			$this->tipo = $tipo;

			$insere_token = $conn->prepare("INSERT INTO token (usuario, data_inicial, tipo, status) VALUES ?, ?, ?, 0");
			$insere_token->bind_param("isi", $this->usuario, $this->data_inicial, $this->tipo);

			$insere_token->execute();

			$this->id = $insere_token->insert_id;

			$args = array('id' => $this->id, 'usuario' => $this->usuario, 'data_inicial' => $this->data_inicial, 'tipo' => $this->tipo, 'status' => 0);

			return json_encode($args);

		}
	}

 ?>