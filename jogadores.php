<?php
include "util.php";
?>
<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>

<table border=0 align=center width="70%">
<?php
carregarArrayMensalistas();

$result=pg_query($conn, "SELECT * FROM jogador order by nome;");

if($result){
 while ($row = pg_fetch_array($result)) {
 ?>
  <tr>
  <form name="jog_<?=$row['codigo']?>" action="atualizar_jogador.php" method="post">
  <td><input type="text" class="jogadores" name="codigo" disabled=true size=1 value="<?=$row['id_jogador']?>"></td>
  <td><input type="text" class="jogadores"  name="nome" size=3 value="<?=$row['nome']?>"></td>
  <td><input type="text"  class="jogadores" name="abreviatura" size=2 value="<?=$row['abreviatura']?>"></td>
  <td><input type="text" class="jogadores"  name="nota" size=1 value="<?=$row['nota']?>"></td>
  <td><input type="text"  class="jogadores" name="tipo" size=1 value="<?=$row['tipo']?>"></td>
  <td><select name="convidante" class="jogadores" >
  <?php
  for($x=0;$x<count($mensalistas);$x++){
   if($row['convidante']==$mensalistas[$x]->id_jogador)
    $selected = "selected";
   else
    $selected = "";
   ?>
   <option value="<?=$mensalistas[$x]->id_jogador?>" <?=$selected?>><?=$mensalistas[$x]->nome?></option>
  <?php 
  }
  ?>
  </select></td>
  
  <td><input class="botaoJogadores" type="submit" disabled="true" value="atualizar"></td>
  </form>
  </tr>
  <?php
 }
}

?>
<tr>
  <form name="jog_novo" action="atualizar_jogador.php" method="post">
  <input type="hidden" name="codigo"  value="novo">
  <td></td>
  <td><input type="text" class="jogadores" size=3 name="nome"></td>
  <td><input type="text" class="jogadores" size=2  name="abreviatura"></td>
  <td><input type="text" class="jogadores" size=1 name="nota"></td>
  <td><input type="text" class="jogadores" size=1 name="tipo"></td>
  <td><select name="convidante"  class="jogadores" >
  <?php
  for($x=0;$x<count($mensalistas);$x++){
   ?>
   <option value="<?=$mensalistas[$x]->id_jogador?>" <?=$selected?>><?=$mensalistas[$x]->nome?></option>
  <?php 
  }
  ?>
  </select></td>
  <td><input type="submit" class="botaoJogadores" value="INCLUIR"></td>
  </form>
  </tr>
 </table>
<input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">