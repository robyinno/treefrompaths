<?php
abstract class TreeFromPathsAbstract {
	protected $new_segment = Null;
	protected $level = Null;
	protected $is_folder = Null;
	protected $arPaths;
	protected $n_node=0;
	protected $path_separator='\\';
	protected $arCurSegments;
	protected $arLastSegments;
	protected $datet='';
	protected $size='';
	protected $status='';
	
	/**
	* Starting from array of Paths infos return one string render of the whole arPaths
	*/
	abstract public function render($echo,&$arPaths);
	/**
	* ret path complete at current level
	*/	
	protected function _get_path($level){
	    return implode(array_slice($this->arCurSegments,0,$level+1),$this->path_separator);    
	}
	abstract protected function close_folder();
	abstract protected function open_node();
	abstract protected function close_leaf();
	abstract protected function reset_render();
	abstract protected function reset_segment();
	abstract protected function render_segment($return_rendered);
	/**
	* create the html in relation to the wave of variation of the paths
	*/
	protected function waveSegment($return_render=false){
		$this->is_folder = Null;
		$this->new_segment = Null;
		
		#error_log(__FILE__ . __FUNCTION__);
		$max_cur_levels=count($this->arCurSegments)-1; # max levels of current segment
		$max_last_levels=count($this->arLastSegments)-1; # max leveles of last segment
		
		$this->reset_render();		
	
		if ($max_cur_levels<$max_last_levels){
			for($i=1;$i<=($max_last_levels-$max_cur_levels);$i++){ # close as many folders as less level from last segment
				$this->close_folder();				
			}
		}
	
		foreach ($this->arCurSegments as $this->level=>$this->new_segment){ # one clicle every segment
			isset($this->arLastSegments[$this->level]) ? $old_segment=$this->arLastSegments[$this->level] : $old_segment='';
			$this->reset_segment();			
			# open folder and files
			if ($this->new_segment!==$old_segment){
				$this->is_folder = ($this->level != $max_cur_levels);
				# close folder of the changed segment (not leaf) until there where levels in the last segments
				if ($this->level < $max_last_levels && $this->is_folder) $this->close_folder();
				$this->open_node(); #!$is_folder => folder 				
			}
			if ($this->level==$max_cur_levels) $this->close_leaf(); 
		}
		return $this->render_segment($return_render);	
	}
	/**
	* Prepare the leaf of every file
	*/
	protected function prep_leaf_details(&$row){
	    isset($row[1]) ? $this->datet = $row[1] : $this->datet = '';
	    isset($row[2]) ? $this->size = human_filesize($row[2]) : $this->size = '';
	    isset($row[4]) ? $this->status = $row[4] : $this->status = '';
	    $this->icon = $this->get_icon($this->status);
	}
	abstract protected function get_icon($status);
	/**
	* Return or add rendered of single file
	*/
	public function single_row($row,$return_render=false){
	    $path=trim($row[0]);
	    $this->arLastSegments = $this->arCurSegments;
	    $this->arCurSegments = explode($this->path_separator,$path);
	    $this->prep_leaf_details($row);
	    $rendered = $this->waveSegment($return_render);
	    if ($return_render) return $rendered;
	}
	/**
	* Read the the array of files and compile string of render
 	*/
	public function core(){
	    # go in every line of the csv
	    foreach ($this->arPaths as $key=>$row){
		  $this->single_row($row);
	    }
	}
}

class TreeFromPathsJson extends TreeFromPathsAbstract {
	protected $json_array = array();
	
	public function render($echo,&$arPaths){
		
	}	
	protected function render_segment($return_rendered){
		
	}
	
	protected function open_node(){
		!$this->is_folder ? $this->datet.$this->size.$this->status : $label_details='';
		$node = Array('data'=>$label_details);
		if ($this->is_folder) $node['children'] = Null;
		
		if (isset($this->last_node['children']) || array_key_exists('children',$this->last_node)){
			$this->last_node['children'] = &$node;
		} else {
			$this->last_node = &$node;	
		}
		#array_push
		#$this->json_array = $node;
	}
	
	protected function close_folder(){
	}
	
	protected function close_leaf(){
		
	}
	protected function get_icon($status){
		
	}
	protected function reset_render(){
		
	}
	protected function reset_segment(){
		
	}
}

class TreeFromPaths extends TreeFromPathsAbstract {

	protected $var_img_folder = '';
	protected $var_checkbox = '';
	protected $ul_open_done = false;
	
	protected $li_open='<li>';
	protected $li_open_sheet='<li class="dhtmlgoodies_sheet.gif">';
	protected $li_close='</li>';
	protected $ul_close='</ul>';
	protected $ul_open='<ul>';
	protected $checkbox = '<input type="checkbox" name="folder_select[]" value="%s">';
	protected $html_new='';
	protected $html_old='';
	protected $html='';
	protected $img_folder='';
	protected $error_level = array('Header OK'=>'00','Header Corrotto'=>'01', 'Nessuna estensione'=>'02',
				     'Tipo di file ambiguo o sconosciuto'=>'03' ,'File Criptato'=>'04','Errore di accesso' => '05',
			 	     'File troppo piccolo (<512 bytes)'=>'06','File con lunghezza = 0'=>'07',
				     'Nomefile non valuido'=>'08','Risulta come tipo di file'=>'09','Risulta come tipo di  file'=>'09');
	protected $icon_level = array('00'=>'green','01'=>'yellow','02'=>'yellow','03'=>'yellow','04'=>'yellow',
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
	 
	protected function close_folder(){
		$this->html_old.=$this->ul_close."\n".$this->li_close."\n";
	}
	
	protected function open_node(){
		# add img folder if is a folder
		if ($this->html_old=='' && $this->is_folder) $this->var_img_folder = $this->img_folder;
		$this->n_node +=1;
		!$this->is_folder ? $label_details=' '. $this->datet.' <span class="color_size">'.$this->size.'</span>'.$this->status : $label_details='';
		
		$this->icon != '' ?  $class='class="'. $this->icon .'"' : $class='';
		$ico   = '<a href="#" '.$class.'>&nbsp;</a>';
		$label ='<a id="node_'.$this->n_node.'" href="#">'.$this->new_segment.$label_details.'</a></div>';
		
		if (!$this->is_folder) { # leaf
			$li_open=$this->li_open_sheet;
		} else { # folder
			$this->var_checkbox = sprintf($this->checkbox,$this->_get_path($this->level));
			$li_open=$this->li_open; # is a folder
		}
		
		$this->html_new.=$li_open.$this->var_checkbox.$ico.$this->var_img_folder.$label."\n";
		
		if ($this->is_folder) { # folder
			$this->html_new.=$this->ul_open."\n";
			$this->ul_open_done=true;
		}
	}
	
	protected function close_leaf(){
		$this->html_new.=$this->li_close."\n";
	}
	
	protected function reset_render(){
		$this->html_new = '';
		$this->html_old = '';
		$this->ul_open_done = false;
	}
	protected function reset_segment(){
		$this->var_img_folder = '';
		$this->var_checkbox = '';
	}
	protected function render_segment($return_rendered){
		if ($return_rendered){
			return $this->html_old.$this->html_new;
		} else {
			$this->html.=$this->html_old.$this->html_new;
		}
	}	

	/**
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
	* Append to an html file a single file info
	*/
	public function append_html_file($row,$pathfile){
	    return file_put_contents($pathfile,$this->single_row($row,true),FILE_APPEND);
	}		
}
?>
