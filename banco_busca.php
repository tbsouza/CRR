<?php
	// Parametros recebidos do javascript

	// Valor a ser pesquisado
	$value = $_POST['text'];
	$value = "%" . $value . "%";

	// Onde pesquisar (coluna do banco) 
	$from = $_POST['from']; // Ex: IDHM_2010

	// Estabelece conexão com o banco
	try {
		$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'user', 'user');
	} catch(PDOException $ex) {
		echo ($ex->getMessage());
	}

	// Consulta o banco
	$sql = 'SELECT COD_IBGE, NOME_MUNICIP, UF, '. $from .' FROM municipios_2015 WHERE COD_IBGE LIKE ? OR NOME_MUNICIP LIKE ? OR UF LIKE ? OR '. $from .' LIKE ?';
	$values = array($value, $value, $value, $value); // Sera substituido pelas interrogacoes
	$db = $connect->prepare($sql); 					 // prepara a consulta 
	$db->execute($values);							 // executa a consulta 

	// Envia os resultados da consulta para o javascript que a chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE'] . '#' . $row['NOME_MUNICIP'] . '#' . $row['UF'] . '#' . $row[($from)] . '#';
	}
?>