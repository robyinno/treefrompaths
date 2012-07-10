<?php 
$arfolder = $_POST['folder_select'];
$filename = $_POST['filename'];
include_once('settings.php');
$pathfile_html = getcwd() . $HTML_DIR.'sel_' .$filename;
file_put_contents($pathfile_html,implode($arfolder,"<br />"));
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: http://$host$uri/" . $HTML_DIR .'sel_'.$filename);
?>
