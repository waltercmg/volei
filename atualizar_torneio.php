<?php
include "conexao.php";

$id_torneio = intval($_POST["id_torneio"]);
$id_jogador = intval($_POST["id_jogador"]);
$ativo = $_POST["ativo"];

if($id_torneio==0){
 $result = pg_query( $conn, "INSERT INTO TORNEIO (nome, data) VALUES ('DIA: '|| current_date, current_date) RETURNING id_torneio;");
 $row = pg_fetch_row($result);
 $id_torneio = intval($row[0]);
}

$params = array($id_torneio, $id_jogador);

if($ativo != ""){
 $result = pg_query_params( $conn, 'INSERT INTO LISTA_PRESENCA (ID_TORNEIO, ID_JOGADOR, HORA_CHEGADA, ATIVO) VALUES ($1,$2,now(),true);', $params);    
 if(!$result){
  $params = array($id_torneio, $id_jogador, $ativo);
  $result = pg_query_params( $conn, 'UPDATE LISTA_PRESENCA SET ATIVO=$3 WHERE ID_TORNEIO=$1 AND ID_JOGADOR=$2;', $params);
  //$result = pg_query_params( $conn, 'DELETE FROM LISTA_PRESENCA WHERE ID_TORNEIO=$1 AND ID_JOGADOR=$2;', $params);    
 } 
}else{
 $params = array($id_torneio, $id_jogador);
 $result = pg_query_params( $conn, 'DELETE FROM LISTA_PRESENCA WHERE ID_TORNEIO=$1 AND ID_JOGADOR=$2;', $params);
}


header("Location: torneio.php");
?>