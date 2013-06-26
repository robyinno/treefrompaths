<?php
class decompress
{
	public static function extract($path_file){
 		$pathinfo=(pathinfo($path_file));
		if ($pathinfo['extension']=='bzip2' or $pathinfo['extension']=='bz2'){
		  $uncompressed_pathfile = $pathinfo['dirname'].$pathinfo['basename'].'.csv';
		  $compressed_str=file_get_contents($path_file);
		  file_put_contents($uncompressed_pathfile, bzdecompress($compressed_str));
		  unset($compressed_str);
		  $file_tree_name = $uncompressed_pathfile;
		} else {
		  $file_tree_name = $path_file;
		}
		return $file_tree_name;
	}	
}
