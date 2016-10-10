<?php				
	// Estabelece conexão com o banco
	try {
			$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'root', 'root');
	} catch(PDOException $ex) {
			echo ($ex->getMessage());
	}

	// Consulta os municipios com backbone em fibra
	$sql = 'SELECT * FROM municipios_bd WHERE BACKBONE=?';
	$values = array("Fibra"); // sera substituido em ? - esta é a forma correta de se fazer				
	$db = $connect->prepare( $sql ); // prepara a consulta
	$db->execute( $values );         // executa a consulta

	// envia os resultados da consulta para o javascript que a chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE']. '#' .$row['NOME_MUNICIP']. '#' .$row['UF']. '#';
	}
?>