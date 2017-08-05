<?php
include "conexao.php";

$id_torneio = intval($_POST["id_torneio"]);
$ordem = $_POST["ordem"];

if($ordem){
 if($id_torneio==0){
  echo "entrou porque:".$id_torneio;
  $result = pg_query( $conn, "INSERT INTO TORNEIO (nome, data) VALUES ('DIA: '|| current_date, current_date) RETURNING id_torneio;");
  $row = pg_fetch_row($result);
  $id_torneio = intval($row[0]);
 }
 $params = array($id_torneio);
 $result = pg_query_params($conn, 'DELETE FROM LISTA_PRESENCA WHERE ID_TORNEIO=$1', $params);    
 if(!$result){
  echo "ERRO AO DELETAR LISTA DE PRESENCA";
 }
 $jogadores = split("#", $ordem);
 $cont = 1;
 foreach ($jogadores as &$id_jogador) {
  $params = array($id_torneio,$id_jogador,$cont);
  $result = pg_query_params( $conn, 'INSERT INTO LISTA_PRESENCA (ID_TORNEIO, ID_JOGADOR, ORDEM) VALUES ($1,$2,$3);', $params);    
  if(!$result){
   echo "ERRO AO INCLUIR LISTA DE PRESENCA";
  }
  $cont++;
 }
header("Location: index.php");
}

?>