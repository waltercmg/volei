
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
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 40px;
    border-radius: 12px;
}

.botao2 {
    background-color: #ff8080;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 40px;
    border-radius: 12px;
}
.botao3 {
    background-color: gray;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 40px;
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
    font-size: 16px;
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
<table>
    <tr>
        <td colspan=4>
            Torneio do dia
        </td>
    </tr>
<form name="frm" id="frm" action="atualizar_torneio.php" method="post">
    <input type="hidden" name="id_torneio" id="id_torneio" value="<?=getTorneioDia()?>"/>
    <input type="hidden" name="id_jogador" id="id_jogador"/>
    <input type="hidden" name="ativo" id="ativo"/>
<?php
$lista = getListaPresencaDia();
$quant = count($lista);
if($quant % 2 == 0){
    $limite = $quant/2-1;
    $incremento = $quant/2;
}else{
    $limite = $quant/2;
    $incremento = $quant/2+1;
}

for ($x = 0; $x <=$limite ; $x++) {
    echo $lista[$x]->abreviatura." - ".$lista[$x+$incremento]->abreviatura;
    //echo $lista[$x]->abreviatura."<br>";
    echo "<br>";
}
?>



