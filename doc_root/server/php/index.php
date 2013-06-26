<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
require_once('../../class/decompress.class.php');
$upload_handler = new UploadHandler();
$file_arr = get_object_vars($upload_handler->initialize_info['files'][0]);
$path_file =  $upload_handler->options['upload_dir']. $file_arr['name'];
require_once('../../settings.php');
require_once('../../class/uploads.error.php');
ini_set('memory_limit','512M');

$GLOBALS['file_tree_name']=decompress::extract($path_file);
require_once('../../genhtml_tree.php');


