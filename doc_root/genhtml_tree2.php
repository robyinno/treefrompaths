<?php

require_once('treefrompaths.php');
require_once('functions.php');
		
class genhtml_tree_new{
	public static function write_tree2html($pathfile){
		error_log('IN '.$pathfile,3,'/tmp/cippa.log');
		
		## usage
		if (!file_exists($pathfile)) {
			#echo "i have not file paths";
			return false;
		}
		
		$data['arPaths']=array();
	
		$tree = new TreeFromPathsjstre_htm($data);
		$path_info = pathinfo($pathfile);
		
		$filename_html = str_replace(' ','_',trim($path_info['filename'])) . '.html';
		$pathfile_html = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SUB_DIR'] . $GLOBALS['HTML_DIR'] . $filename_html;
		error_log('OUT '.$pathfile_html,3,'/tmp/cippa.log');
		
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
			throw new Exception('Caught exception: ',  $e->getMessage(), "\n");
		}
		return $filename_html;
		
	}
}
