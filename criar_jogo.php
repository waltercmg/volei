<?php
include "conexao.php";
?>



<?
$result=pg_query($conn, "SELECT * FROM jogador order by nome;");

echo "<table>";
if($result){
 echo "<tr><td colspan=3> JOGADORES CADASTRADOS</td></tr>";
 while ($row = pg_fetch_array($result)){
  echo "<tr>";
  echo "<input type=\"hidden\" name=\"codigo\" value=\"" . $row['codigo'] . "\">";
  echo "<td><input type=\"text\" disabled=\"true\"  name=\"nome\" value=\"" . $row['nome'] . "\"></td>";
  echo "<td><input type=\"text\" disabled=\"true\" name=\"nota\" value=\"" . $row['nota'] . "\"></td>";
  echo "<td><input type=\"text\" disabled=\"true\"  name=\"tipo\" value=\"" . $row['tipo'] . "\"></td>";
  echo "<td><input type=\"checkbox\" value=\"Chegou\" onclick=\"javascript:chegou(\"" . $row['codigo'] . "\")\"></td>";
  echo "</tr>";
 }
}
echo "<tr><td colspan=3>ORDEM DE CHEGADA CADASTRADOS</td></tr>";

echo "</table>";

?>