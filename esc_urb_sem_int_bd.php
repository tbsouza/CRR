<?php		
	// Estabelece conexão com o banco
	try {
			$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'root', 'root');
	} catch(PDOException $ex) {
			echo ($ex->getMessage());
	}

	// busca todos os municipios
	$sql = 'SELECT * FROM municipios_bd';
	$db = $connect->prepare( $sql );
	$db->execute();

	// envia os resultados da consulta para o javascript que a chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE']. '#' .$row['NOME_MUNICIP']. '#' .$row['UF']. '#' .$row['ESC_URB_S_INTERNET'] . '#';
	}
?>