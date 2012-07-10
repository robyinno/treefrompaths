<?php
class TreeFromPaths {

	private $arPaths;
	private $path_separator='\\';
	private $li_open='<li>';
	private $li_open_shet='<li class="dhtmlgoodies_sheet.gif">';
	private $li_close='</li>';
	private $ul_close='</ul>';
	private $ul_open='<ul>';
	private $checkbox = '<input type="checkbox" name="folder_select[]" value="%s">';
	private $html_new='';
	private $html_old='';
	private $html='';
	private $arLastSegments=array();
	private $arCurSegments=array();
	private $img_folder='';
	private $n_node=0;
	private $datet='';
	private $size='';
	private $status='';
	private $error_level = array('Header OK'=>'00','Header Corrotto'=>'01', 'Nessuna estensione'=>'02',
				     'Tipo di file ambiguo o sconosciuto'=>'03' ,'File Criptato'=>'04','Errore di accesso' => '05',
			 	     'File troppo piccolo (<512 bytes)'=>'06','File con lunghezza = 0'=>'07',
				     'Nomefile non valuido'=>'08','Risulta come tipo di file'=>'09','Risulta come tipo di  file'=>'09');
	private $icon_level = array('00'=>'green','01'=>'yellow','02'=>'yellow','03'=>'yellow','04'=>'yellow',
				    '05'=>'yellow','06'=>'yellow','07'=>'yellow','08'=>'yellow','09'=>'red');

	public function __construct(&$params){
		if (is_array($params)){
			foreach (array('arPaths','path_separator','li_open','li_close',
				       'ul_open','ul_close','img_folder','error_level','checkbox') as $key){
				if (isset($params[$key])) $this->{$key}=$params[$key];
			}			
		}
	}

	/**
	* Starting from array of Paths infos return one html string
	*/
	public function render($echo=false,&$arPaths=null){
		if ($arPaths){
			$this->arPaths=$arPaths;			
		}
		$this->core();
		return $this->html;
	}

	/**
	* Return the html compiled from array of Paths
	*/
	public function get_html(){
	    return $this->html;
	}

	/**
	* ret path complete at current level
	*/	
	private function _get_path($level){
	    return implode(array_slice($this->arCurSegments,0,$level+1),$this->path_separator);    
	}

	/*
	* create the html in relation to the wave of variation of the paths
	*/
	private function waveSegment($returnHTML=false){
		
		$max_cur_levels=count($this->arCurSegments)-1; # max levels of current segment
		$max_last_levels=count($this->arLastSegments)-1; 
		$already_closed=false;
		$this->html_new = '';
		$this->html_old = '';
		$ul_open_done=false;

		if ($max_cur_levels<$max_last_levels){
		    for($i=1;$i<=($max_last_levels-$max_cur_levels);$i++){ # close the li and ul of the different levels
		      $this->html_old.=$this->ul_close."\n".$this->li_close."\n";
		    }
		}		
		
		foreach ($this->arCurSegments as $level=>$new_segment){ # one clicle every segment 
			isset($this->arLastSegments[$level]) ? $old_segment=$this->arLastSegments[$level] : $old_segment='';
			$img_folder = '';
			$checkbox = '';

			# open folder and files
			if ($new_segment!==$old_segment){
			      $dif_level=($level!=$max_cur_levels);

			      # close li and ul of the changed segment until there where levels in the last segments 
			      if ($level<$max_last_levels && $dif_level) $this->html_old.=$this->ul_close."\n".$this->li_close."\n";

			      # add img folder if is a folder
			      if ($this->html_old=='' && $dif_level) $img_folder = $this->img_folder; 	
			      $this->n_node +=1;
			      !$dif_level ? $label_details=' '. $this->datet.' <span class="color_size">'.$this->size.'</span>'.$this->status : $label_details='';

			      $this->icon != '' ?  $class='class="'. $this->icon .'"' : $class='';
			      $ico   = '<a href="#" '.$class.'>&nbsp;</a>';
			      $label ='<a id="node_'.$this->n_node.'" href="#">'.$new_segment.$label_details.'</a></div>';
			      
			      if (!$dif_level) {
			          $li_open=$this->li_open_shet;
			      } else {
				  $path = $this->_get_path($level);
				  $checkbox = sprintf($this->checkbox,$this->_get_path($level));
				  $li_open=$this->li_open; # is a leaf
		 	      }
			      $this->html_new.=$li_open.$checkbox.$ico.$img_folder.$label."\n";

			      if ($dif_level) { # if is not leaf
				  $this->html_new.=$this->ul_open."\n"; // "<ul class='$level'>"."\n";
				  $ul_open_done=true;
			      }
			}
			
			if ($level==$max_cur_levels) $this->html_new.=$this->li_close."\n";
		}
		if ($returnHTML){
		    return $this->html_old.$this->html_new;
		} else {
		    $this->html.=$this->html_old.$this->html_new;
		}
	}

	/*
	* Prepare the leaf of every file
	*/
	protected function prep_leaf_details(&$row){
	    isset($row[1]) ? $this->datet = $row[1] : $this->datet = '';
	    isset($row[2]) ? $this->size = human_filesize($row[2]) : $this->size = '';
	    isset($row[4]) ? $this->status = $row[4] : $this->status = '';
	    $this->icon = $this->get_icon($this->status);
	}

	/*
	* Get icon color from Status
	*/
	protected function get_icon($status){
	    $arStatus = explode(':',$status);
	    $tmp_status = $arStatus[0];
	    isset($this->error_level[$tmp_status]) ? $icon = $this->error_level[$tmp_status] : $icon = '';
	    isset($this->icon_level[$icon]) ? $icon_color = $this->icon_level[$icon] : $icon_color = '';
	    return $icon_color;
	}

	/**
	* Return or add in $this->html the html of single file
	*/
	public function single_row($row,$returnHTML=false){
	    $path=trim($row[0]);
	    $this->arLastSegments=$this->arCurSegments;
	    $this->arCurSegments=explode($this->path_separator,$path);
	    $this->prep_leaf_details($row);
	    $html=$this->waveSegment($returnHTML);
	    if ($returnHTML) return $html;
	}

	/**
	* Append to an html file a single file info
	*/
	public function append_html_file($row,$pathfile){
	    return file_put_contents($pathfile,$this->single_row($row,true),FILE_APPEND);
	}

	/**
	* Read the the array of files and compile html in $this->html
 	*/
	public function core(){
	    # go in every line of the csv
	    foreach ($this->arPaths as $key=>$row){
		  $this->single_row($row);
	    }
	}		
}
?>
