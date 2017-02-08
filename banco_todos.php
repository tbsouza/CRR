<?php

	// Parametro recebido do javascript

	// Onde pesquisar (coluna do banco) 
	$coluna = $_POST['coluna']; // POP_2015


	// Estabelece conexão com o banco
	try {
		$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'user', 'user');
	} catch(PDOException $ex) {
		echo ($ex->getMessage());
	}


	// Consulta o banco
	$sql = 'SELECT COD_IBGE, NOME_MUNICIP, UF, ' . $coluna .' FROM municipios_2015';
	//$values = array($from); // Sera substituido pelas interrogacoes
	$db = $connect->prepare($sql); 					 // prepara a consulta 
	$db->execute();							 // executa a consulta 


	// envia os resultados da consulta para o javascript que a chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE'] . '#' . $row['NOME_MUNICIP'] . '#' . $row['UF'] . '#' . $row[ ($coluna) ] . '#';
	}

?>