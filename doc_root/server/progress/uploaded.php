<?php
### UPLOAD HANDLER ###
error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
require_once('../../class/decompress.class.php');
$upload_handler = new UploadHandler();
$file_arr = get_object_vars($upload_handler->initialize_info['files'][0]);
$path_file =  $upload_handler->options['upload_dir']. $file_arr['name'];

### GEN TREE ###
require_once('../../settings.php');
require_once('../../class/uploads.error.php');
ini_set('memory_limit','512M');
require_once('../../genhtml_tree2.php');

# memorizza il file decompresso da trattare 
$path_file = decompress::extract($path_file);
$filename_html = genhtml_tree_new::write_tree2html($path_file);

$host  = $_SERVER['HTTP_HOST'];
$url_tree = "http://$host" . $GLOBALS['SUB_DIR'] .$GLOBALS['TEMPLATES_DIR']. 'tree_basic_view3.php';


if ($filename_html){
	 echo "<a target=\"_blank\" href=\"$url_tree?fl=$filename_html\">$filename_html</a>";
}