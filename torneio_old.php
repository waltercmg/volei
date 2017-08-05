<?php
include "conexao.php";
?>
<script type="text/javascript">
    var total = 0;
    function adicionar(id, nome) {
        itens = document.getElementById("list").getElementsByTagName("li");
        var ul = document.getElementById("list");
        existente = false;
        for (var i = 0; i < itens.length; i++) { 
            var id_presente = itens[i].getAttribute("id"); 
            if(id_presente == id){
                ul.removeChild(itens[i]);
                existente = true;
                total--;
                break;
            }
        }
        if(!existente){
            var li = document.createElement("li");
            li.appendChild(document.createTextNode(nome));
            li.setAttribute("id", id); // added line
            ul.appendChild(li);
            total++;
        } 
        //ul.innerHTML = "Total: " + total;
    }
    function salvar(){
        itens = document.getElementById("list").getElementsByTagName("li");
        presenca = "";
        for (var i = 0; i < itens.length; i++) { 
            id_presente = itens[i].getAttribute("id");
            presenca += id_presente + "#";
        }
        form = document.forms.frm;
        ordem = document.getElementById("ordem");
        ordem.value = presenca.substring(0, presenca.length-1);
        form.submit();
        
    }
</script>
<table>
    <tr>
        <td>
            Torneio do dia 
        </td>
    </tr>
    <tr>
        <td colspan=3>
            Jogadores Cadastrados
        </td>
    </tr>
<?php

$result=pg_query($conn, "SELECT * FROM jogador order by nome;");

if($result){
 while ($row = pg_fetch_array($result)){
  echo "<tr>";
  echo "<input type=\"hidden\" name=\"codigo\" value=\"" . $row['id_jogador'] . "\">";
  echo "<td><input type=\"button\" enabled=\"true\" onclick=\"javascript:adicionar(" . $row['id_jogador'] . ",'" . $row['nome'] . "');\" name=\"nome\" value=\"" . $row['nome'] . "\"></td>";
  echo "</tr>";
 }
}?>

<tr>
    <td>
        Ordem de Chegada
    </td>
<tr>
    <td>
        <ul id="list">
<?php
$id_torneio = 0;
$consulta = "SELECT torneio.id_torneio, jogador.id_jogador, jogador.nome, lista_presenca.ordem FROM torneio, lista_presenca, jogador ".
            "where lista_presenca.id_torneio = torneio.id_torneio and lista_presenca.id_jogador = jogador.id_jogador ".
            "and torneio.data=current_date order by ordem asc";
$result=pg_query($conn, $consulta);
if($result){
    while ($row = pg_fetch_array($result)){
        echo "<li id=\"".$row['id_jogador']."\">".$row['ordem']." - ".$row['nome']."</li>";
        $id_torneio = $row['id_torneio'];
    }
    
}
?>
        </ul> 
    </td>
</tr>
</table>
<form name="frm" id="frm" action="atualizar_torneio.php" method="post">
    <input type="hidden" id="ordem" name="ordem"/>
    <input type="hidden" id="id_torneio" name="id_torneio" value="<?=$id_torneio;?>"/>
    <input type="button" value="salvar" onclick="javascript:salvar();"/>
</form>
