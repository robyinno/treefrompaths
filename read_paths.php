<?php

## usage

$pathfile=getcwd(). '/paths.csv';

if (!file_exists($pathfile)) {
	echo "non ho il file paths";
	die;
}

$data['arPaths']=array();
#foreach (file($pathfile) as $line_mb){
foreach (file($pathfile) as $line_mb){
  $encode_from=mb_detect_encoding($line_mb);
  if (!$encode_from){
    $encode_from='UTF-16LE';
  } 
  $line=mb_convert_encoding($line_mb,'UTF-8',$encode_from);
  $row=explode(',',$line);
  foreach ($row as $key=>&$value){
	  $value = preg_replace('/"(.*)"/','${1}',$value);
	  $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
  }
  if (isset($row[0]) && count(explode('\\',$row[0]))>1) $data['arPaths'][]=$row;
}

require_once('treefrompaths.php');
require_once('functions.php');
#$data['ul_open']='<ul class="dhtmlgoodies_tree">';
#$data['li_open']='<li class="dhtmlgoodies_treeNode">';
#$data['img_folder']='<img src="images/dhtmlgoodies_folder.gif">';
$tree=new TreeFromPaths($data);
echo '<ul class="dhtmlgoodies_tree" id="dhtmlgoodies_tree">';
echo $tree->render();
echo '</ul>';

?>
