<?php

function genhtml_tree($pathfile) {
	## usage
	#isset($GLOBALS['file_tree_name']) ? $pathfile = $GLOBALS['file_tree_name'] : $pathfile = getcwd().'paths.csv';
	
	if (!file_exists($pathfile)) {
		#echo "i have not file paths";
		return false;
	}
	
	$data['arPaths']=array();
	
	require_once('treefrompaths.php');
	require_once('functions.php');
	$tree=new TreeFromPaths($data);
	$path_info = pathinfo($pathfile);
	
	$filename_html = str_replace(' ','_',trim($path_info['filename'])) . '.html';
	
	$pathfile_html = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SUB_DIR'] . $GLOBALS['HTML_DIR'] . $filename_html;
	$header_path_file = $_SERVER['DOCUMENT_ROOT'] .$GLOBALS['SUB_DIR'] . '/header.html';
	# header creation
	try{
		if (file_exists($header_path_file)){
			$header_html = file_get_contents($header_path_file);
			file_put_contents($pathfile_html,$header_html);
		}
		
		file_put_contents($pathfile_html,'<ul class="dhtmlgoodies_tree" id="dhtmlgoodies_tree">',FILE_APPEND);
		file_put_contents($pathfile_html,'<form name="select_dir" action="../save_dirs_selected.php" method="post"><input type="submit"><input name="filename" type="hidden" value="' . $filename_html . '" />',FILE_APPEND);
	
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
		file_put_contents($pathfile_html,'</form>',FILE_APPEND);
		file_put_contents($pathfile_html,'</ul>',FILE_APPEND);
		$footer_path_file = $_SERVER['DOCUMENT_ROOT'] .$GLOBALS['SUB_DIR'] . '/header.html';
		
		if (file_exists($footer_path_file)){
			$footer_html = file_get_contents($footer_path_file);
			file_put_contents($pathfile_html,$footer_html,FILE_APPEND);			
		}
	} catch (Exception $e){
		#echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
	$host  = $_SERVER['HTTP_HOST'];
	#$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	return "[URL]http://$host/" . $GLOBALS['SUB_DIR'] . $GLOBALS['HTML_DIR'] .$filename_html;
}

#header("Location: http://$host$uri/" . $HTML_DIR .$filename_html);

?>
