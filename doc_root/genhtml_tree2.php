<?php

function genhtml_tree2($pathfile) {
	## usage
	if (!file_exists($pathfile)) {
		#echo "i have not file paths";
		return false;
	}
	
	$data['arPaths']=array();
	
	require_once('treefrompaths.php');
	require_once('functions.php');
	$tree = new TreeFromPathsjstre_htm($data);
	$path_info = pathinfo($pathfile);
	
	#$filename_html = str_replace(' ','_',trim($path_info['filename'])) . '.html';
	$pathfile_html = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SUB_DIR'] . $GLOBALS['HTML_DIR'] . 'html_data_gen2.html';

	try{
		
		file_put_contents($pathfile_html,'<li id="root">');
		# body creation
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
		  	$tree->append_html_file($row,$pathfile_html);
		  }
		}
		
		# footer creation
		file_put_contents($pathfile_html,'</li>',FILE_APPEND);

	} catch (Exception $e){
		#echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	$host  = $_SERVER['HTTP_HOST'];
	return "[URL]http://$host/" . $GLOBALS['SUB_DIR'] .'demo_tree_h2.html';
}
?>
