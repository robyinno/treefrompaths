<?php

## usage
isset($GLOBALS['file_tree_name']) ? $pathfile = $GLOBALS['file_tree_name'] : $pathfile = getcwd().'paths.csv';

if (!file_exists($pathfile)) {
	echo "i have not file paths";
	die;
}

$data['arPaths']=array();

require_once('treefrompaths.php');
require_once('functions.php');
$tree=new TreeFromPaths($data);
echo '<ul class="dhtmlgoodies_tree" id="dhtmlgoodies_tree">';


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
  if (isset($row[0]) && count(explode('\\',$row[0]))>1) {
    echo $tree->single_row($row,true);
  }
}

echo '</ul>';

?>
