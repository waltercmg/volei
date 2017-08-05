<?php
include "util.php";

$codigo = $_POST["codigo"];
$nome = $_POST["nome"];
$abreviatura = $_POST["abreviatura"];
$nota = $_POST["nota"];
$tipo = $_POST["tipo"];
$convidante = $_POST["convidante"];
//echo $convidante;
echo $codigo." - ".$nome." - ".$nota." - ".$tipo." - ".$convidante;
if($tipo=="C" && $convidante==""){
 echo "PREENCHER CONVIDANTE";
}else{
 if($codigo and $nome and $nota and $tipo){
  echo "TESTE";
  if($codigo == "novo"){
   if($convidante>0){
    echo "0";
    $params = array($nome, $abreviatura, $nota, $tipo, $convidante);
    $result = pg_query_params( $conn, 'INSERT INTO JOGADOR (nome, abreviatura, nota, tipo, convidante) VALUES ($1,$2,$3,$4, $5)', $params);
   }else{
    echo "-1";
    $params = array($nome, $abreviatura, $nota, $tipo);
    $result = pg_query_params( $conn, 'INSERT INTO JOGADOR (nome, abreviatura, nota, tipo) VALUES ($1,$2,$3,$4)', $params);
   }
  } else {
   if($convidante>0){
    $params = array($nome, $abreviatura, $nota, $tipo, $convidante, $codigo);
    $result = pg_query_params( $conn, 'UPDATE JOGADOR SET nome=$1, abreviatura=$2, nota=$3, tipo=$4, convidante=$5 where id_jogador=$6', $params);
    echo "1";
    
   }else{
    $params = array($nome, $abreviatura, $nota, $tipo, $codigo);
    echo "2";
    $result = pg_query_params( $conn, 'UPDATE JOGADOR SET nome=$1, abreviatura=$2, nota=$3, tipo=$4 where id_jogador=$5', $params);
   }
    
   }
  if($result){
   header("Location: jogadores.php");
  } else {
   echo "ERRO AO ATUALIZAR JOGADOR.";
  }
 }else{
  echo "CAMPOS OBRIGATÓRIOS NAO PREENCHIDOS";
 }
}
?>