
<?php
include "util.php";
?>

<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>

<script type="text/javascript">
    function encerrarPartida(vitorioso){
        form = document.forms.frm;
        document.getElementById("vitorioso").value=vitorioso;
        document.getElementById("acao").value="ENCERRAR";
        form.submit();
    }
</script>

<?php

$id_torneio = intval($_POST["id_torneio"]);
$id_partida = intval($_POST["id_partida"]);
$limite = intval($_POST["limite"]);
$acao = $_POST["acao"];
$vitorioso = $_POST["vitorioso"];
//echo "<br>ACAO:::".$acao;
if($acao == "ENCERRAR"){
    encerrarPartida($id_partida, $vitorioso);
} elseif($acao == "NOVA") {

    $time_a = array();
    $time_b = array();
    $ids_time_a = array();
    $ids_time_b = array();
    
    $arrayLetras = array("A", "B");
    for($i=0;$i<2;$i++){
        for ($x = 0; $x <$limite/2; $x++){
            $jog = new jogador();
            $jog->id_jogador = intval($_POST["jog_".$arrayLetras[$i].$x]);
            $jog->abreviatura = $_POST["abr_".$arrayLetras[$i].$x];
            $jog->nota = $_POST["not_".$arrayLetras[$i].$x];
            $jog->tipo = $_POST["tip_".$arrayLetras[$i].$x];
        
            if($arrayLetras[$i]=="A"){
                array_push($ids_time_a, $jog->id_jogador);
                array_push($time_a, $jog);
            }elseif($arrayLetras[$i]=="B"){
                array_push($ids_time_b, $jog->id_jogador);
                array_push($time_b, $jog);
            }
        }
    }
    $array = iniciarPartida($id_torneio, $ids_time_a, $ids_time_b);
    $id_partida = $array[0];
}elseif($acao == "ATUAL"){

$array = getPartidaAtual();
$id_partida = $array[0];
$time_a = $array[1];
$time_b = $array[2];

}
?>

<table border=0 align=center width="90%">
    <tr>
        <td colspan=2 align=center>
            <font size=30 >PARTIDA #<?=$id_partida?> INICIADA (<?=$array[1]?>)</font>
        </td>
    </tr>
    <tr>
        <td align=center>
            <font size=30 align=center>TIME A</font>
        </td>
        <td align=center>
            <font size=30 align=center>TIME B</font>
        </td>
    </tr>
    
<form name="frm" id="frm" action="partida.php" method="post">
    <input type="hidden" name="acao" id="acao" value="">
    <input type="hidden" name="id_torneio" id="id_torneio" value="">
    <input type="hidden" name="id_partida" id="id_partida" value="<?=$id_partida?>">
    <input type="hidden" name="vitorioso" id="vitorioso" value="">
<?php

for($x=0;$x<count($time_a);$x++){
    
    echo "<tr><td align=center>".getHTMLJogadorEscalado($time_a[$x],"A",$x).
         "</td><td align=center>".getHTMLJogadorEscalado($time_b[$x],"B",$x).
         "</td></tr>";
}
?>

</form>
<tr>
    <td align=center>
        <input type="button" class="botaoMenu" onclick="javascript:encerrarPartida('A');" value="TIME A VENCEU">
    </td>
    <td align=center>
        <input type="button" class="botaoMenu" onclick="javascript:encerrarPartida('B');" value="TIME B VENCEU">
    </td>
</tr>

<script type="text/javascript">
    
</script>
<tr>
    <td colspan=2 align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
    </td>
</tr>
</table>



