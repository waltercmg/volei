<?php
include "util.php";
?>
<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>

<table border=0 align=center width="70%">
 <tr>
  <td class="linha_tit">NOME</td>
  <td class="linha_tit">ABREV.</td>
  <td class="linha_tit">NOTA</td>
  <td class="linha_tit">TIPO</td>
  <td class="linha_tit">CONVIDANTE</td>
 </tr>
<?php
carregarArrayMensalistas();

$result=pg_query($conn, "SELECT * FROM jogador order by nome;");

if($result){
 while ($row = pg_fetch_array($result)) {
 ?>
  <tr>
  <form name="jog_<?=$row['codigo']?>" action="atualizar_jogador.php" method="post">
  <input type="hidden" class="jogadores" name="codigo" size=1 value="<?=$row['id_jogador']?>">
  <td><input type="text" class="jogadores"  name="nome" size=5 value="<?=$row['nome']?>"></td>
  <td><input type="text"  class="jogadores" name="abreviatura" size=4 value="<?=$row['abreviatura']?>"></td>
  <td><input type="text" class="jogadores"  name="nota" size=3 value="<?=$row['nota']?>"></td>
  <td><input type="text"  class="jogadores" name="tipo" size=1 value="<?=$row['tipo']?>"></td>
  <td><select name="convidante" class="jogadores" >
  <option value="0">-----</option>
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
  
  <td><input class="botaoJogadores" type="submit" value="atualizar"></td>
  </form>
  </tr>
  <?php
 }
}

?>
<tr>
  <form name="jog_novo" action="atualizar_jogador.php" method="post">
  <input type="hidden" name="codigo"  value="novo">
  <td><input type="text" class="jogadores" size=5 name="nome"></td>
  <td><input type="text" class="jogadores" size=4  name="abreviatura"></td>
  <td><input type="text" class="jogadores" size=3 name="nota"></td>
  <td><input type="text" class="jogadores" size=1 name="tipo"></td>
  <td><select name="convidante"  class="jogadores" >
   <option value="0" selected>-----</option>
  <?php
  for($x=0;$x<count($mensalistas);$x++){
   ?>
   <option value="<?=$mensalistas[$x]->id_jogador?>"><?=$mensalistas[$x]->nome?></option>
  <?php 
  }
  ?>
  </select></td>
  <td><input type="submit" class="botaoJogadores" value="INCLUIR"></td>
  </form>
  </tr>
  <tr><td colspan=5 align=center><br><br>
   <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
  </td></tr>
 </table>
 
