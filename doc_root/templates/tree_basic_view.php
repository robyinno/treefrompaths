<?php 
if (isset($_GET['fl'])){
	$html_out_file = $_GET['fl'];	
}
?>
<script type="text/javascript" src="http://static.jstree.com/v.1.0pre/jquery.js"></script>
<link rel="canonical" href="http://www.jstree.com/documentation/checkbox" />	<link type="text/css" rel="stylesheet" href="http://static.jstree.com/v.1.0pre/_docs/syntax/!style.css"/>
<script type="text/javascript" src="http://static.jstree.com/v.1.0pre/_docs/syntax/!script.js"></script>
<link rel="stylesheet" type="text/css" href="http://static.jstree.com/v.1.0pre/_docs/!style.css" />
<script type="text/javascript" src="http://static.jstree.com/v.1.0pre/jquery.cookie.js"></script>
<script type="text/javascript" src="http://static.jstree.com/v.1.0pre/jquery.hotkeys.js"></script>
<script type="text/javascript" src="http://static.jstree.com/v.1.0pre/jquery.jstree.js"></script>
<link rel="stylesheet" type="text/css" href="../css/treefrompaths.css"</script>

<script type="text/javascript">
$(function () {
	$("#tree").jstree({ 
		"plugins" : [ "themes", "html_data", "checkbox", "sort", "ui","types" ],
		"html_data" : {
        	"ajax" : {
        		"url" : "../html_gen/<?php echo $html_out_file; ?>",
        	}
        },
        "types" : {		
			"valid_children" : ["default","folder"],
			"types" : {
		           // The default type
		            "default" : {
	 		                "valid_children" : "none",
			                "icon" : {
			                    "image" : "../js/types/file.png"
			                }
			            },
			       // The `folder` type
		            "folder" : {
			                // can have files and other folders inside of it, but NOT `drive` nodes
			                "valid_children" : [ "default", "folder" ],
			                "icon" : {
			                    "image" : "../js/types/folder.png"
			                }
			            },
					},
		},
	});
});
</script>

<div id="tree">
</div>
