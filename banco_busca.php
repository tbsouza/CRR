<?php
	// Parametros recebidos do javascript
	$value = $_POST['text'];     // Valor a ser pesquisado
	$value = "%" . $value . "%";
	 
	$from = $_POST['from'];      // Onde pesquisar (coluna do banco)


	// Estabelece conexão com o banco
	try {
		// PDO - PHP Data Object - Prepared Statements
		$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'user', 'user');
	}catch(PDOException $ex) {
		echo ($ex->getMessage());
	}


	// Consulta o banco
	$sql = 'SELECT COD_IBGE, NOME_MUNICIP, UF, '. $from .' FROM municipios_2015 WHERE COD_IBGE LIKE ? OR NOME_MUNICIP LIKE ? OR UF LIKE ? OR '. $from .' LIKE ?';
	$values = array($value, $value, $value, $value); // Sera substituido pelas interrogacoes na querry
	$db = $connect->prepare($sql); 					 // prepara a consulta 
	$db->execute($values);							 // executa a consulta 


	// Envia os resultados da consulta para o javascript que chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE'] . '#' . $row['NOME_MUNICIP'] . '#' . $row['UF'] . '#' . $row[($from)] . '#';
	}
?>