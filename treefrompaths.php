<?php
class TreeFromPaths {

	private $arPaths;
	private $path_separator='\\';
	private $li_open='<li>';
	private $li_open_shet='<li class="dhtmlgoodies_sheet.gif">';
	private $li_close='</li>';
	private $ul_close='</ul>';
	private $ul_open='<ul>';
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

	public function __construct(&$params){
		if (is_array($params)){
			foreach (array('arPaths','path_separator','li_open','li_close','ul_open','ul_close','img_folder') as $key){
				if (isset($params[$key])) $this->{$key}=$params[$key];
			}			
		}
	}

	public function render($echo=false,&$arPaths=null){
		if ($arPaths){
			$this->arPaths=$arPaths;			
		}
		$this->core();
		return $this->html;
	}

	public function get_html(){
	    return $this->html;
	}

	/*
	* creare the html in relation to the wave of variation of the paths
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
			$img_folder='';
			# open folder and files
			if ($new_segment!==$old_segment){
			      $dif_level=($level!=$max_cur_levels);

			      # close li and ul of the changed segment until there where levels in the last segments 
			      if ($level<$max_last_levels && $dif_level) $this->html_old.=$this->ul_close."\n".$this->li_close."\n";

			      # add img folder if is a folder
			      if ($this->html_old=='' && $dif_level) $img_folder=$this->img_folder;
			      $this->n_node +=1;
			      !$dif_level ? $label_details=' '. $this->datet.' '. $this->size.' '.$this->status : $label_details='';
			      strpos($this->status,'OK')!==FALSE ? $class='class="green_color"' : $class='';
			      $label='<a '.$class.' id="node_'.$this->n_node.'" href="#">'.$new_segment.$label_details.'</a></div>';
			      
			      !$dif_level ? $li_open=$this->li_open_shet : $li_open=$this->li_open; # is a leaf
			      $this->html_new.=$li_open.$img_folder.$label."\n";

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

	protected function prep_leaf_details(&$row){
	    isset($row[1]) ? $this->datet = $row[1] : $this->datet = '';
	    isset($row[2]) ? $this->size = human_filesize($row[2]) : $this->size = '';
	    isset($row[4]) ? $this->status = $row[4] : $this->status = '';
	}

	public function single_row($row,$returnHTML=false){
	    $path=trim($row[0]);
	    $this->arLastSegments=$this->arCurSegments;
	    $this->arCurSegments=explode($this->path_separator,$path);
	    $this->prep_leaf_details($row);
	    $html=$this->waveSegment($returnHTML);
	    if ($returnHTML) return $html;
	}

	public function core(){
	    # go in every line of the csv
	    foreach ($this->arPaths as $key=>$row){
		  $this->single_row($row);
	    }
	}		
}
?>
