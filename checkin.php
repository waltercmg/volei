
<?php
include "util.php";
?>

<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>

<?php

$id_torneio = intval($_POST["id_torneio"]);
echo "<br>CHECKIN -> ID TORNEIO: " . $id_torneio;
$id_jogador = intval($_POST["id_jogador"]);
echo "<br>CHECKIN -> ID JOGADOR: " . $id_jogador;
$presente = $_POST["presente"];
echo "<br>CHECKIN -> PRESENTE: " . $presente;
$ativo = $_POST["ativo"];
echo "<br>CHECKIN -> ATIVO: " . $ativo;
if($id_jogador != ""){
    atualizarCheckIn($id_torneio, $id_jogador, $ativo, $presente);
}
if($id_torneio == ""){
    $id_torneio = getTorneioDia();
    echo "<br>CHECKIN -> ID_TORNEIO_N: " . $id_torneio;
}
?> 

<script type="text/javascript">
    function atualizar(id_jogador, ativo, presente){
        form = document.forms.frm;
        document.getElementById("id_jogador").value=id_jogador;
        //Inverte o ativo
        if(ativo=="t"){
            ativo = false;
        }else{
            ativo = true;
        }
        document.getElementById("ativo").value=ativo;
        document.getElementById("presente").value=presente;
        form.submit();
    }
    
    function remover(id_jogador){
        form = document.forms.frm;
        document.getElementById("id_jogador").value=id_jogador;
        document.getElementById("ativo").value="";
        form.submit();
    }
    
</script>

<table border=0 align=center width="70%">
    <tr>
        <td colspan=4 align=center>
            <font size=30 >Ordem de Chegada</font>
        </td>
    </tr>
<form name="frm" id="frm" action="checkin.php" method="post" >
    <input type="hidden" name="id_torneio" id="id_torneio" value="<?=$id_torneio?>"/>
    <input type="hidden" name="id_jogador" id="id_jogador"/>
    <input type="hidden" name="ativo" id="ativo"/>
    <input type="hidden" name="presente" id="presente"/>
<?php
$lista = getListaPresencaDia($id_torneio);

$quant = count($lista);
if($quant % 2 == 0){
    $limite = $quant/2-1;
    $incremento = $quant/2;
}else{
    $limite = round($quant/2);
    $incremento = round($quant/2+1);
    //echo $incremento;
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
    </td>
</tr>





