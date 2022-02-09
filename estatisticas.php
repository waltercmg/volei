
<?php
include "util.php";
//echo "get estatisticas";
$torneios = getEstatisticasDadosObj();
//echo "OK get estatisticas";
//$label = "";
/*echo "<br>DATA 0 :".$torneios[0]->data;
echo "<br>DATA 1 :".$torneios[1]->data;
echo "<br>DATA 2 :".$torneios[2]->data;

echo "<br>QT 0 :".$torneios[0]->qtPartidas;
echo "<br>QT 1 :".$torneios[1]->qtPartidas;
echo "<br>QT 2 :".$torneios[2]->qtPartidas;*/

$arrayJogadores = array();

for($i=0;$i<count($torneios);$i++){
    $torneio = $torneios[$i];
    $data[$i] = $torneio->data;    
    foreach(array_keys($torneio->jogadores) as $jogadores){
        $vitorias = $torneio->jogadores[$jogadores][0]+0;
        $derrotas = $torneio->jogadores[$jogadores][1]+0;
        $aprov = number_format($vitorias/($vitorias + $derrotas),2);

        $arrayJogadores[$jogadores][0]+=$vitorias;
        $arrayJogadores[$jogadores][1]+=$derrotas;


        if($qtPartidasJog[$i] != "")
            $qtPartidasJog[$i] = $qtPartidasJog[$i] . "," . ($derrotas + $vitorias);
        else
            $qtPartidasJog[$i] =  ($derrotas + $vitorias);
        
        /*echo "<br>I: " . $i;
        echo "<br>Label I: " . $label[$i];
        echo "<br>JOGADORES" . $jogadores;*/
        
        if($label[$i] != ""){          
            echo "<br>entrou if";
            $label[$i] = $label[$i] . ", '" . $jogadores ."'";            
        }
        else{
            echo "<br>entrou else";
            $label[0] = "'". $jogadores ."'";            
        }
        
        if($qtPartidasDia[$i] != "")
            $qtPartidasDia[$i] = $qtPartidasDia[$i] . ",'" . $torneio->qtPartidas . "'";
        else
            $qtPartidasDia[$i] = "'" . $torneio->qtPartidas . "'";
        
        if($qtVitoriasJog[$i] != "")
            $qtVitoriasJog[$i] = $qtVitoriasJog[$i] . ",'" . $vitorias . "'";
        else
            $qtVitoriasJog[$i] = "'" . $vitorias . "'";
        
        if($qtDerrotasJog[$i] != "")
            $qtDerrotasJog[$i] = $qtDerrotasJog[$i] . ",'" . $derrotas . "'";
        else
            $qtDerrotasJog[$i] = "'" . $derrotas . "'";
            
        if($aprovJog[$i] != "")
            $aprovJog[$i] = $aprovJog[$i] . ",'" . $aprov . "'";
        else
            $aprovJog[$i] = "'" . $aprov . "'";    
    }
}

foreach(array_keys($arrayJogadores) as $jogador){
    $vitorias = $arrayJogadores[$jogador][0];
    $derrotas = $arrayJogadores[$jogador][1];
    $aprov = number_format($vitorias/($vitorias + $derrotas),2);
    
    if($label_acumulado!= "")
        $label_acumulado = $label_acumulado . ",'" . $jogador . "'";
    else
        $label_acumulado = "'" . $jogador . "'";
    
    if($der_acumulado != "")
        $der_acumulado = $der_acumulado . "," .$derrotas;
    else
        $der_acumulado = $derrotas ;
        
    if($vit_acumulado != "")
        $vit_acumulado = $vit_acumulado . "," . $vitorias;
    else
        $vit_acumulado = $vitorias ;
        
    if($apr_acumulado != "")
        $apr_acumulado = $apr_acumulado . "," . $aprov;
    else
        $apr_acumulado = $aprov ;

}

?>

<head>
<script src="Chart.js"></script>
<link rel="stylesheet" type="text/css" href="estilo.css">

</head>


<table width=100%>
<tr><td>
    <canvas id="qtPartidasDia"></canvas>
</td></tr>
<tr><td>
    <canvas id="vitDerDia"></canvas>
</td></tr>
<tr><td>
    <canvas id="vitDerAcum"></canvas>
</td></tr>
<tr>
    <td align=center>
        <input type="button" class="botaoMenu" onclick="location.href = 'index.php'" value="MENU">
    </td>
</tr>
</table>
<br><br>

<script>
var ctx = document.getElementById("qtPartidasDia").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?=$label[0]?>],
        datasets: [
        {
            label: '# Partidas por jogador',
            data: [<?=$qtPartidasJog[0]?>],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderWidth: 1
        },    
        {
            label: '# Partidas do dia',
            data: [<?=$qtPartidasDia[0]?>],
            borderWidth: 1,
            type: 'line'
        }
        ]
    },
    options: {
        title: {
            display: true,
            text: 'Quantidade de Partidas em <?=$data[0]?>',
            fontSize: 20
        },
        scales: {
            yAxes: [{
                ticks: {
                    min: 0,
                    stepSize: 1
                }
            }]
        }
    }
});
</script>


<script>
var ctx = document.getElementById("vitDerDia").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?=$label[0]?>],
        datasets: [
        {
            label: '# Vitorias',
            data: [<?=$qtVitoriasJog[0]?>],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderWidth: 1
        },    
        {
            label: '# Derrotas',
            data: [<?=$qtDerrotasJog[0]?>],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderWidth: 1
        }, {
            label: 'Aproveitamento',
            data: [<?=$aprovJog[0]?>],
            borderWidth: 1,
            type: 'line'
        }
        ]
    },
    options: {
        title: {
            display: true,
            text: 'Resumo do dia <?=$data[0]?>',
            fontSize: 20
        },
        scales: {
            yAxes: [{
                ticks: {
                    min: 0,
                    stepSize: 1
                }
            }]
        }
    }
});
</script>

<script>

var ctx = document.getElementById("vitDerAcum").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?=$label_acumulado?>],
        datasets: [
        {
            label: '# Vitorias',
            data: [<?=$vit_acumulado?>],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderWidth: 1
        },    
        {
            label: '# Derrotas',
            data: [<?=$der_acumulado?>],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderWidth: 1
        },
        {
            label: 'Aproveitamento',
            data: [<?=$apr_acumulado?>],
            borderWidth: 1,
            type: 'line'
        }
        ]
    },
    options: {
        title: {
            display: true,
            text: 'Acumulado (<?=count($torneios)?> torneios)',
            fontSize: 20
        }
    }
});
</script>

