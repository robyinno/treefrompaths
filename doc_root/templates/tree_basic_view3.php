<?php 
if (isset($_GET['fl'])){
	$html_out_file = $_GET['fl'];	
}
?>
<link rel="stylesheet" href="../dist/themes/default/style.min.css" />
<link rel="stylesheet" type="text/css" href="../css/treefrompaths.css">
<script src="../dist/libs/jquery.js"></script>
<script src="../dist/jstree.min.js"></script>

<script type="text/javascript">
$(function () {
	$("#tree").jstree({ 
		"plugins" : [ "themes", "html_data", "checkbox", "sort", "ui","types","state" ],
		"core" : {
        	"data" : {
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