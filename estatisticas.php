
<?php
include "util.php";

$torneios = getEstatisticasDados();

$label = "";
foreach(array_keys($torneios) as $dt_torneio){
    //echo "<tr><td colspan=4>TORNEIO ".$dt_torneio."</td></tr>";
    $dt = $dt_torneio;
    foreach(array_keys($torneios[$dt_torneio]) as $jogadores){
        $vitorias = $torneios[$dt_torneio][$jogadores][0]+0;
        $derrotas = $torneios[$dt_torneio][$jogadores][1]+0;
        $aprov = number_format($vitorias/($vitorias + $derrotas)*100,2)."%";
        
        if($der != "")
            $der .= "," . $derrotas;
        else
            $der = $derrotas ;
            
        if($vit != "")
            $vit .= "," . $vitorias;
        else
            $vit = $vitorias ;
        
        if($apr != "")
            $apr .= "," . $aprov;
        else
            $apr = $aprov ;
        
        if($label != "")
            $label .= ",'" . $jogadores . "'";
        else
            $label = "'" . $jogadores . "'";
    }
    break;
}


$arrayJogadores = array();
foreach(array_keys($torneios) as $dt_torneio){
    foreach(array_keys($torneios[$dt_torneio]) as $jogadores){
        $vitorias = $torneios[$dt_torneio][$jogadores][0]+0;
        $derrotas = $torneios[$dt_torneio][$jogadores][1]+0;
        $arrayJogadores[$jogadores][0]+=$vitorias;
        $arrayJogadores[$jogadores][1]+=$derrotas;
    }
}

$label_acumulado = "";
foreach(array_keys($arrayJogadores) as $jogador){
    $vitorias = $arrayJogadores[$jogador][0];
    $derrotas = $arrayJogadores[$jogador][1];
    
    if($label_acumulado!= "")
        $label_acumulado .= ",'" . $jogador . "'";
    else
        $label_acumulado = "'" . $jogador . "'";
    
    if($der_acumulado != "")
        $der_acumulado .= "," .$derrotas;
    else
        $der_acumulado = $derrotas ;
        
    if($vit_acumulado != "")
        $vit_acumulado .= "," . $vitorias;
    else
        $vit_acumulado = $vitorias ;

}

?>

<head>
<script src="Chart.js"></script>
</head>


<div class="chart-container" style="position: relative; height:20vh; width:80vw">
    <canvas id="myChart3"></canvas>
</div>
<br><br>
<script>
var ctx = document.getElementById("myChart3").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?=$label?>],
        datasets: [
        {
            label: '# Vitorias',
            data: [<?=$vit?>],
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderWidth: 1
        },    
        {
            label: '# Derrotas',
            data: [<?=$der?>],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderWidth: 1
        }
        ]
    },
    options: {
        title: {
            display: true,
            text: 'Torneio do dia <?=$dt?>',
            fontSize: 20
        }
    }
});
</script>
<br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br>
<br><br><br><br><br><br><br><br><br><br>
<div class="chart-container" style="position: relative; height:20vh; width:80vw">
    <canvas id="myChart4"></canvas>
</div>
<script>
var ctx = document.getElementById("myChart4").getContext('2d');
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
        }
        ]
    },
    options: {
        title: {
            display: true,
            text: 'Acumulado',
            fontSize: 20
        }
    }
});
</script>
