<?php 

	setlocale('LC_ALL', 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');

	$conn = new mysqli("localhost", "root", "", "aloha_sistema");

	if (!$conn) {
    	die('Erro de conexão (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}


 ?>