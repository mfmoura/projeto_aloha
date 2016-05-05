<?php 
	
	include ("../class/usuario.php");

	$usr = new usuario();
	
	try {
	
		$usr->login = new login("ze", "zezezezeze");
		echo "Logado com sucesso!";
	
	} 
	catch (Exception $e) {
		echo $e->getMessage() . "\n";
	}

 ?>