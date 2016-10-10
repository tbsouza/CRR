<?php 		
	/* exibe as mensagens de erro para o usuario (fins de teste) */
	ini_set('display_errors',1);
	error_reporting(E_ALL);
						
	// Estabelece conexão com o banco
	try {
			$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'root', 'root');
	} catch(PDOException $ex) {
			echo ($ex->getMessage());
	}

	// busca todos os municipios
	$sql = 'SELECT * FROM municipios';
	$db = $connect->prepare( $sql );
	$db->execute();

	// envia os resultados da consulta para o javascript que a chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE']. '#' .$row['NOME_MUNICIP']. '#' .$row['UF']. '#' .$row['POP_2014'] . '#';
	}
?>