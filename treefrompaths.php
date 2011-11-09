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
	* genera l'html in base all'onda della variazione dei percorsi
	*/
	private function waveSegment(){
		$max_cur_levels=count($this->arCurSegments)-1; # livelli massimi del segmento corrente
		$max_last_levels=count($this->arLastSegments)-1; 
		$already_closed=false;
		$this->html_new = '';
		$this->html_old = '';
		foreach ($this->arCurSegments as $level=>$new_segment){ # un ciclo per ogni segmento 
			isset($this->arLastSegments[$level]) ? $old_segment=$this->arLastSegments[$level] : $old_segment='';

			# apri directory e files
			if ($new_segment!==$old_segment){
			      $this->html_new.=$this->li_open."\n".$new_segment."\n";
			      if ($level!=$max_cur_levels) { # se non è foglia
				  $this->html_new.="<ul class='$level'>"."\n";
			      }
			}
			
			if ($level==$max_cur_levels) $this->html_new.=$this->li_close."\n";
			# chiudi sempre il file
			#if ($level==$this->arCurSegments) $this->html_new.=$this->el_close."\n"; 				
			#}
		}
		
		if ($max_cur_levels<$max_last_levels){
		    for($i=1;$i<=($max_last_levels-$max_cur_levels);$i++){ # chiude gli li e ul del dislivello
		      $this->html_old.=$this->ul_close."\n".$this->li_close."\n";
		    }
		}
		$this->html.=$this->html_old.$this->html_new;
	}

	private function core(){
		# scorre tutte le righe del csv
		foreach ($this->arPaths as $row){
			$path=trim($row[0]);
			#print $path;
			$this->arLastSegments=$this->arCurSegments;
			$this->arCurSegments=explode($this->path_separator,$path);			
			$this->waveSegment();
		}
	}		
}

## usage

$pathfile=getcwd(). '/paths.csv';

if (!file_exists($pathfile)) {
	echo "non ho il file paths";
	die;
}

$data['arPaths']=array();
#foreach (file($pathfile) as $line_mb){
foreach (file($pathfile) as $line_mb){
  $encode_from=mb_detect_encoding($line_mb);
  if (!$encode_from){
    $encode_from='UTF-16LE';
  } 
  $line=mb_convert_encoding($line_mb,'UTF-8',$encode_from);
  $row=explode(',',$line);
  foreach ($row as $key=>&$value){
	  $value = preg_replace('/"(.*)"/','${1}',$value);
	  $value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
  }
  $data['arPaths'][]=$row;
}
$tree=new TreeFromPaths($data);
echo $tree->render();
?>
