<?php
	// Parametro recebito do javascript
	$text = $_POST['text'];
	$value = "%" . $text . "%";

	// Estabelece conexão com o banco
	try {
		$connect = new PDO('mysql:host=127.0.0.2;dbname=dados_municipios;charset=utf8mb4', 'root', 'root');
	} catch(PDOException $ex) {
		echo ($ex->getMessage());
	}

	// Consulta o banco
	$sql = 'SELECT COD_IBGE, NOME_MUNICIP, UF, POP_2015 FROM municipios_2015 WHERE COD_IBGE LIKE ? OR NOME_MUNICIP LIKE ? OR UF LIKE ? OR POP_2015 LIKE ?';
	$values = array($value, $value, $value, $value); // Sera substituido pelas interrogacoes
	$db = $connect->prepare($sql); 					 // prepara a consulta 
	$db->execute($values);							 // executa a consulta 

	// envia os resultados da consulta para o javascript que a chammou
	while ( $row = $db->fetch() ) {
	  	echo $row['COD_IBGE'] . '#' . $row['NOME_MUNICIP'] . '#' . $row['UF'] . '#' . $row['POP_2015'] . '#';
	}
?>