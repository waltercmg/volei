
<?php
include "conexao.php";
?>

<style>

@media only screen and (max-width: 1000px) {
    body {
        background-color: lightblue;
        font-size: 160%;
    }
    a {
        background-color: lighblue;
        font-size: 400%;
    }

    
   
}

.botao1 {
    width: 300px;
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 60px;
    border-radius: 12px;
}

.botao2 {
    width: 300px;
    background-color: #ff8080;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 60px;
    border-radius: 12px;
}
.botao3 {
    width: 300px;
    background-color: gray;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 60px;
    border-radius: 12px;
}
.botaoExcluir {
    background-color: gray;
    border: none;
    color: white;
    padding: 15px 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 60px;
    border-radius: 12px;
}
.botaoMenu {
    background-color: gray;
    border: none;
    color: white;
    padding: 15px 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 60px;
    border-radius: 12px;
}


</style>

<?php

$id_torneio = intval($_POST["id_torneio"]);
$id_jogador = intval($_POST["id_jogador"]);
$ativo = $_POST["ativo"];
echo "<br>J".$id_jogador;
echo "<br>T".$id_torneio;
if($id_jogador != ""){
    echo "ENTROUUU";
    $id_torneio = $_POST["id_torneio"];
    $ativo = $_POST["ativo"];
    atualizarCheckIn($id_torneio, $id_jogador, $ativo);
}
?> 

<script type="text/javascript">
    function atualizar(id_jogador, ativo){
        form = document.forms.frm;
        document.getElementById("id_jogador").value=id_jogador;
        //Inverte o ativo
        if(ativo=="t"){
            ativo = false;
        }else{
            ativo = true;
        }
        document.getElementById("ativo").value=ativo;
        form.submit();
    }
    
    function remover(id_jogador){
        form = document.forms.frm;
        document.getElementById("id_jogador").value=id_jogador;
        document.getElementById("ativo").value="";
        form.submit();
    }
    
</script>

<table border=0 align=center width="90%">
    <tr>
        <td colspan=4 align=center>
            <font size=30 >Ordem de Chegada</font>
        </td>
    </tr>
<form name="frm" id="frm" action="torneio.php" method="post">
    <input type="hidden" name="id_torneio" id="id_torneio" value="<?=getTorneioDia()?>"/>
    <input type="text" name="id_jogador" id="id_jogador"/>
    <input type="hidden" name="ativo" id="ativo"/>
<?php
$lista = getListaPresencaDia();

$quant = count($lista);
if($quant % 2 == 0){
    $limite = $quant/2-1;
    $incremento = $quant/2;
}else{
    $limite = round($quant/2);
    $incremento = round($quant/2+1);
    echo $incremento;
}

for ($x = 0; $x <=$limite ; $x++) {
    echo "<tr><td>".getHTMLJogador($lista[$x], ($x+1))."</td>".
         "<td>".getHTMLJogador($lista[$x+$incremento], ($x+$incremento+1))."</td></tr>";
}
?>
</form>
<tr>
    <td colspan=3 align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
        <input type="button" class="botaoMenu" onclick="location.href = 'candidatos.php'" value="NOVA PARTIDA">
    </td>
</tr>





