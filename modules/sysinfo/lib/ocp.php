<?php
/*
OCP - Optimizer+ Control Panel, by _ck_
Version 0.0.2
Free for any kind of use or modification, I am not responsible for anything, please share your improvements!

** Quick'n'Dirty first version

* known problems/limitations:
Zend only stores the file timestamp, not when it was first put into the cache  :-(
accelerator_reset function doesn't just clear the cache, it literally restarts the module entirely

* todo:
File sorting/directory filter
Extract variables for prefered ordering instead of just dumping into tables
CSS graph of memory use, free, wasted

*/

ini_set('display_errors','1'); error_reporting(-1);

if ( !empty($_GET['CLEAR']) ) {
	if ( function_exists('accelerator_reset')) { accelerator_reset(); }
	header( 'Location: '.str_replace('?'.$_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI']) );
	return;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>OCP - Optimizer+ Control Panel</title>
<meta name="ROBOTS" content="NOINDEX,NOFOLLOW,NOARCHIVE" />

<style type="text/css">
body {background-color: #ffffff; color: #000000;}
body, td, th, h1, h2 {font-family: sans-serif;}
pre {margin: 0px; font-family: monospace;}
a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
a:hover {text-decoration: underline;}
table {border-collapse: collapse;}
.center {text-align: center;}
.center table { margin-left: auto; margin-right: auto; text-align: left;}
.center th { text-align: center !important; }
td, th { border: 1px solid #000000; font-size: 75%; vertical-align: baseline;}
h1 {font-size: 150%;}
h2 {font-size: 125%;}
.p {text-align: left;}
.e {background-color: #ccccff; font-weight: bold; color: #000000; width:50%; white-space:nowrap;}
.h {background-color: #9999cc; font-weight: bold; color: #000000;}
.v {background-color: #cccccc; color: #000000;}
.vr {background-color: #cccccc; text-align: right; color: #000000;}
img {float: right; border: 0px;}
hr {width: 600px; background-color: #cccccc; border: 0px; height: 1px; color: #000000;}
.meta, .small {font-size: 75%;}
.meta a {margin: 0 10px;}
.buttons {margin:0 0 15px;}
.buttons a {margin:0 15px; background-color: #9999cc; color:#fff; text-decoration:none; padding:0 3px; border:1px solid #000; }
</style>
</head>

<body>
<div class="center">

<h1>Optimizer+ Control Panel</h1>

<div class="buttons">
	<a href="?ALL=1">Detailed</a>
	<a href="?FILES=1">Files Cached</a>
	<a href="?CLEAR=1" onclick="return confirm('RESET cache?')">Clear Cache</a>
</div>

<div class="meta">
	<a href="http://files.zend.com/help/Zend-Server/content/zendoptimizerplus.html">directives guide</a> |
	<a href="http://files.zend.com/help/Zend-Server/content/zend_optimizer+_-_php_api.htm">functions guide</a> |
	<a href="https://github.com/zend-dev/ZendOptimizerPlus/">source</a>
</div>

<?php

// gg
//if (!function_exists('accelerator_get_status')) { die('<h2>Optimizer+ not detected?</h2>'); }

if (!empty($_GET['FILES'])) {
	echo '<h2>files cached</h2>';
	if ( function_exists('accelerator_get_status') && $status=accelerator_get_status() ) {
    		// print '<pre>'; print_r($status['scripts']);
    		$time=time();
    		echo '<table border="0" cellpadding="3" width="780">
          			<tr class="h"><th>script</th><th>hits</th><th>size</th><th>last used</th><th>created</th></tr>';
		if (!empty($status['scripts'])) {
      			foreach ($status['scripts'] as $data) {
        				echo '<tr><td class="v" nowrap>',$data['full_path'],'</td>',
              					'<td class="v" align="right">',$data['hits'],'</td>',
              					'<td class="v" align="right">',round($data['memory_consumption']/1024),'K</td>',
                          				'<td class="v">',time_since($time,$data['last_used_timestamp']),'</td>',
                          				'<td class="v">',empty($data['timestamp'])?'':time_since($time,$data['timestamp']),'</td></tr>';

      				}
    			}
  		}
	return;
}


// some info is only available via phpinfo, so sadly buffering capture has to be used
ob_start(); phpinfo(8); $phpinfo = ob_get_contents(); ob_end_clean();
$find='/module\_Zend Optimizer\+.+?(\<table[^>]*\>.+?\<\/table\>).+?(\<table[^>]*\>.+?\<\/table\>)/s';
if ( !preg_match($find,$phpinfo, $zend) ) { }  // todo

echo '<h2>general</h2>';

if ( function_exists('accelerator_get_configuration') ) {
	$configuration=accelerator_get_configuration();
	if ( !empty($configuration['version']['version']) ) {
		$version=array('Version'=>$configuration['version']['accelerator_product_name'].' '.$configuration['version']['version']);
         	print_table($version);
      	}
}

if ( !empty($zend[1]) ) { echo $zend[1]; }

if (function_exists('accelerator_get_status') && $status=accelerator_get_status()) {
	echo '<h2>memory</h2>';
	print_table($status['memory_usage']);
	if ( !empty($status['accelerator_statistics']['last_restart_time']) ) {
		$status['accelerator_statistics']['last_restart']=time_since(time(),$status['accelerator_statistics']['last_restart_time']);
	}
	unset($status['accelerator_statistics']['last_restart_time']);
	echo '<h2>statistics</h2>';
	print_table($status['accelerator_statistics']);
//   	print_r(accelerator_get_status());
// 	print_r(get_loaded_extensions(true));
}

if (empty($_GET['ALL'])) {return;}

if ( !empty($configuration['blacklist']) ) {
	echo '<h2>blacklist</h2>';
	print_table($configuration['blacklist']);
}

echo '<h2>runtime</h2>';
if ( !empty($zend[2]) ) { echo $zend[2]; }


if ( $functions=get_extension_funcs('zend optimizer+') ) {
	echo '<h2>functions</h2>';
	print_table($functions);
  }

function time_since($time,$original) {
	$text=' ago';
	$time =  $time - $original;
	$day = round($time/86400,0);
	$result = '';
	if ($time < 86400) {
		if ($time < 60) 		   { $result = $time.' second'; }
		elseif ($time < 3600) { $result = floor($time/60).' minute'; }
		else				   { $result = floor($time/3600).' hour'; }
	}
	elseif ($day < 14) 	{ $result = $day.' day'; }
	elseif ($day < 56) 	{ $result = floor($day/7).' week'; }
	elseif ($day < 672) 	{ $result = floor($day/28).' month'; }
	else {			  $result = (intval(2*($day/365))/2).' year'; }

	if (intval($result)!=1) {$result.='s';}
	return $result.$text;
}


function print_table($array) {
	if ( empty($array) || !is_array($array) ) {return;}
  	echo '<table border="0" cellpadding="3" width="600">';
  	foreach ($array as $key=>$value) {
    		echo '<tr>';
    		if (!is_numeric($key)) {
      			$key=ucwords(str_replace('_',' ',$key));
      			echo '<td class="e">',$key,'</td>';
      			if ( is_numeric($value) ) {
        				if ($value>1048576) { $value=round($value/1048576,1).'M'; }
        				elseif ( is_float($value) ) { $value=round($value,1); }
      			}
    		}
    		if ( is_array($value) ) {
      			foreach ($value as $column) {
         			echo '<td class="v">',$column,'</td>';
      			}
      			echo '</tr>';
    		}
    		else { echo '<td class="v">',$value,'</td></tr>'; }
	}
 	echo '</table>';
}

?>
</div>
</body>
</html>