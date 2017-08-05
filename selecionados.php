
<?php
include "util.php";
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
    width: 250px;
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
    width: 250px;
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
    width: 250px;
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
.botaoConvidado {
    width: 250px;
    background-color: yellow;
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
        <td align=center>
            <font size=30 align=center>PARTICIPANTES</font>
        </td>
    </tr>
<form name="frm" id="frm" action="escalacao.php" method="post">
<?php
$lista = getCandidatosPartida();
$array = getQtdePresentes();
$id_torneio = getTorneioDia();
?>
    <input type="hidden" name="id_torneio" id="id_torneio" value="<?=$id_torneio?>"/>
    <input type="hidden" name="limite" id="limite" value="<?=$limite?>"/>

<?php
$candidatos=0;
for ($x = 0; $x <=count($lista); $x++) {
    if($lista[$x]->tipo=="C"){
        if(count($convidadosEscalados) < $qtdeConvidadosSemRevezar){
            echo "<tr><td align=center>".getHTMLJogadorPartida($lista[$x],null,$candidatos)."</td></tr>";
            array_push($convidadosEscalados, $lista[$x]->id_jogador);
            $candidatos++;
        }
        
    }
}
?>
</form>
<tr>
    <td align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
        <input type="button" class="botaoMenu" onclick="location.href = 'torneio.php'" value="TORNEIO">
        <input type="button" class="botaoMenu" onclick="javascript:document.forms.frm.submit();" value="ESCALAR TIMES">
    </td>
</tr>




