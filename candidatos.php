
<?php
include "util.php";
?>
<head>
<link rel="stylesheet" type="text/css" href="estilo.css">
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-CJTMQ0YWP1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-CJTMQ0YWP1');
</script>

<table border=0 align=center width="90%">
    <tr>
        <td align=center>
            <font size=30 align=center>PARTICIPANTES</font>
        </td>
    </tr>
<form name="frm" id="frm" action="escalacao.php" method="post">
    
<?php
$limite = getQtdeJogadoresPorTime()*2;
$id_torneio = getTorneioDia();
//echo "TORNEIOOO=".$id_torneio;
$lista = getCandidatosPartida($id_torneio);
//echo "<br>LISTA: ". count($lista);

?>
    <input type="hidden" name="id_torneio" id="id_torneio" value="<?=$id_torneio?>"/>
    <input type="hidden" name="limacite" id="limite" value="<?=$limite?>"/>

<?php
$candidatos=0;
$array_jog_revez = array();
//echo "<br>".count($lista);
for ($x = 0; $x <count($lista); $x++) {
    if(!in_array($lista[$x]->id_jogador,$array_jog_revez )){
        //echo "<br>TESTE".$lista[$x]->id_jog_revez;
        echo "<tr><td align=center>".getHTMLJogadorCandidato($lista[$x],$candidatos,"checked");
        if($lista[$x]->id_jog_revez != ""){
            echo getHTMLJogadorCandidato(getJogador($lista[$x]->id_jog_revez),$candidatos,"");
            array_push($array_jog_revez, $lista[$x]->id_jog_revez);
        }
        $candidatos++;
        echo "</td></tr>";
        if($candidatos >= $limite){
            break;
        }
    }
}
?>
</form>
<tr>
    <td align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
        <input type="button" class="botaoMenu" onclick="javascript:document.forms.frm.submit();" value="ESCALAR TIMES">
    </td>
</tr>





