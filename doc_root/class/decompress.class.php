<?php
class decompress
{
	public static function extract($path_file){
 		$pathinfo=(pathinfo($path_file));
 		$uncompressed_pathfile = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.csv';
 		switch (mime_content_type($path_file)){
 			case 'application/zip':
 				file_put_contents($uncompressed_pathfile, self::zip_decompress($path_file));
 				$file_tree_name = $uncompressed_pathfile;
 				break;
 			case 'application/x-bzip2':
 				file_put_contents($uncompressed_pathfile, self::bz_decompress($path_file));
 				$file_tree_name = $uncompressed_pathfile;
 				break;
 			case 'text/x-c':
 				$file_tree_name = $path_file;
 				break;
 			case 'application/octet-stream':
 				$file_tree_name = $path_file;
 				break;
 			default:
 				$file_tree_name = $path_file;
 				break;
 		}
		return $file_tree_name;
	}
	
	private static function zip_decompress($path_file){
		$zip = zip_open($path_file);
		if (gettype($zip) == 'resource'){
           	$entry = zip_read($zip);
       		// open entry
        	if (zip_entry_open($zip, $entry, "r")){
        		// read entry
        		$zip_str = zip_entry_read($entry, zip_entry_filesize($entry));	
        		return $zip_str;	
			}
		}
	}
	
	private static function bz_decompress($path_file){
		return bzdecompress(file_get_contents($path_file));
		
	}
}
