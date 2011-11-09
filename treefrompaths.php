<?php
class TreeFromPaths {

	private $arPaths;
	private $path_separator='\\';
	private $li_open='<li>';
	private $li_close='</li>';
	private $ul_close='</ul>';
	private $ul_open='<ul>';
	private $html_new='';
	private $html_old='';
	private $html='';
	private $arLastSegments=array();
	private $arCurSegments=array();


	public function __construct(&$params){
		if (is_array($params)){
			foreach (array('arPaths','path_separator') as $key){
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
	/*
	* creare the html in relation to the wave of variation of the paths
	*/
	private function waveSegment(){
		$max_cur_levels=count($this->arCurSegments)-1; # max levels of current segment
		$max_last_levels=count($this->arLastSegments)-1; 
		$already_closed=false;
		$this->html_new = '';
		$this->html_old = '';
		foreach ($this->arCurSegments as $level=>$new_segment){ # one clicle every segment 
			isset($this->arLastSegments[$level]) ? $old_segment=$this->arLastSegments[$level] : $old_segment='';

			# open folder and files
			if ($new_segment!==$old_segment){
			      $this->html_new.=$this->li_open."\n".$new_segment."\n";
			      if ($level!=$max_cur_levels) { # if is not leaf
				  $this->html_new.="<ul class='$level'>"."\n";
			      }
			}
			
			if ($level==$max_cur_levels) $this->html_new.=$this->li_close."\n";
		}
		
		if ($max_cur_levels<$max_last_levels){
		    for($i=1;$i<=($max_last_levels-$max_cur_levels);$i++){ # close the li and ul of the different levels
		      $this->html_old.=$this->ul_close."\n".$this->li_close."\n";
		    }
		}
		$this->html.=$this->html_old.$this->html_new;
	}

	private function core(){
		# go in every line of the csv
		foreach ($this->arPaths as $row){
			$path=trim($row[0]);
			#print $path;
			$this->arLastSegments=$this->arCurSegments;
			$this->arCurSegments=explode($this->path_separator,$path);			
			$this->waveSegment();
		}
	}		
}
?>
