<?php

$id = file_get_contents('php://input'); 

$logFile = "view.log";

file_put_contents($logFile, $id."\n", FILE_APPEND);

$a=json_decode($id,true);
// RPML first command, need to access members of $a

$b=json_decode($a[1][0],true);

$n=$b[0][0];

$la['cmd']=array();
$lb=&$la['cmd'];

foreach($a as $k => $c) {
    $lb[]=array($k, gettype($c));
    // $lb[]=gettype($c);
}

// input output need to be string
// final output need to concatenate first output to second output
// or show only final output
// see dbg: below

// $a has current input from ajax
// $la is results of first command
// i.e. retrieve n lines from log

$lc = array();

// main loop for all RPML commands
foreach($a[1] as $k => $c) {

    $lb[]=array($k,$c,gettype($c));
    // $lb[]=gettype($c);

    // put this is the loop, after matching first command
    $j = json_decode($c, true);
    $n = $j[0][0];
    $r = $j[0];

    $l=count($j[0])-1;
    $n=$j[0][$l];

    if ((int)($n)>0) { 
        $la['log'] =  getline($n, $logFile);
        $lc[$k] = 'log';
    }
    // This is output for first command
    // How to pass to next command?
    // use index of array as indicator of output
    else if ($n=='g') {
        $la['grep'] = preg_grep( "/".$r[1]."/", $la['log']);
        $lc[$k] = 'grep';
    }
    else if ($n=='dbg') $lc[$k] = 'dbg';
    // $lb[]=array($j,count($j));
    // $lb[]=array($j[0],count($j[0]));
}

// $la is used to store input output between commands
// Sections of $la may be deleted for shorter final output

$la['seq'] = $lc;
$cc = count($lc)-1;
$kl = $lc[$cc];

// dbg: debug mode, outputs all
// else outputs last result
if ($kl=='dbg') $lf=$la;
else $lf = $la[$kl];

// Send this back to web page:
echo date("Y-m-d H:i:s")."> ".$a[0]." _ ".$a[1][0]." _ ". (is_int($n)?"int":"ltr"). " <p>" . sprintf("%d", (int)($n)). json_encode($lf);
// count($b)." ".

function getline($n, $f)
{
    $a=file($f);
    $l=count($a);

    $b=array();

    for($i=0; $i<$n; $i++)
    $b[]=$a[$l-$i-1];

    return $b;
}
?>
