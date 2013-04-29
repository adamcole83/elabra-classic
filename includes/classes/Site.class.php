<?php

class Site {
	
	public $page_id, $page_title, $page_name, $page_description, $page_body, $page_url, $page_parent_id, $page_last_modified, $page_last_modified_by, $page_created, $department_id, $department_title, $department_code, $department_index_id, $department_directory, $department_path, $menu, $breadcrumbs, $banners;
	
	public $department, $page, $content;
	
	function Site($department=null)
	{	
		//instantiate department and page
		if( $department == null ) {
			$dir = explode('/', $_SERVER['REQUEST_URI']);
			$this->department = Department::find_by_dir($dir[1]);
		}
		else
		{
			$this->department = Department:: find_by_dir($department);
		}
		$this->content = new Content();
		$this->content->department = $this->department->id;
		$this->page = $this->content->getContent();
		
		// Check if development
		/*
echo $this->department;
		if($this->department->dev_mode == '1')
		{
			redirect_to('/admin/offline.php');
		}
*/
		
		// instantiate additional objects
		$this->page->parent				= $this->content->find_by_id($this->page->parent_id);
		$this->page->menu				= $this->content->list_all_parents();
		$this->page->banner				= $this->content->list_all_banners();
		$this->department->index		= $this->content->find_by_id($this->department->index_id);
		
		// build content
		$this->site_title();
		$this->menu();
		$this->breadcrumbs();
		
		// build public strings
		$this->page_id					= $this->page->id;
		$this->page_name				= $this->page->title;
		$this->page_description			= $this->page->description;
		$this->block_content			= $this->page->body;
		$this->page_url					= $this->page->url;
		$this->page_last_modified		= $this->page->updated;
		$this->page_last_modified_by	= $this->page->updatedBy;
		$this->page_created				= $this->page->post_created;
		$this->department_id			= $this->department->id;
		$this->department_title			= $this->department->name;
		$this->department_code			= $this->department->code;
		$this->department_index_id		= $this->department->index_id;
		$this->department_directory		= $this->department->subdir;
		$this->department_path			= PUBLIC_ROOT.DS.$this->department->subdir.DS;
		
	}
	
	private function site_title()
	{
		if(!preg_match('/home/i', $this->page->title))
			$string = $this->page->title." | ";
	
		$this->page_title = $string.$this->department->name." | University of Missouri School of Medicine";
	}
	
	private function menu()
	{
		$html = '<ul class="menu">';
		if( !$this->page->menu ):
			for( $i=0; $i<8; $i++ ):
				$html .= '<li><a href="#">Set Menu Item</a></li>';
			endfor;
		else:
			for( $i=0; $i<8; $i++ ):
				if( $this->department->index->id == $this->page->menu[$i]->id ) continue;
				if( $this->page->menu[$i] ) {
					$ext = ( ! preg_match("/(https?|ftp)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s]*)?$/iS", $this->page->menu[$i]->url) ) ? '.html' : '';
					$html .= '<li><a href="' . $this->page->menu[$i]->url . $ext .'">' .$this->page->menu[$i]->title . '</a></li>';
				}else{
					$html .= '<li><a href="#">Set Menu Item</a></li>';
				}
			endfor;
		endif;
		$html .= '</ul>';
		
		$this->menu = $html;
	}
	
	private function breadcrumbs()
	{
		$is_home = ($this->page->id == $this->department->index->id) ? true : false;
		$html = ( !$is_home ) ? '<ul id="breadcrumbs">' : null;
		// home
		if( !$is_home )
			$html .= '<li><a href="'.$this->department->index->url.'.html">'.$this->department->index->title.'</a> &raquo;</li>';
		// parent
		if( !$is_home && $this->page->id != $this->page->parent->id )
			$html .= '<li><a href="'.$this->page->parent->url.'.html">'.$this->page->parent->title.'</a> &raquo;</li>';
		// self
		if( !$is_home )
			$html .= '<li>'.$this->page->title.'</li>';
		
		$html .= ( !$is_home ) ? '</ul>' : null;
		
		$this->breadcrumbs = $html;
	}
	
	public function banners($random=false)
	{
		$random = ($random == true) ? 'random' : null;
		$count = 1;
		
		$html = '<div id="slide-holder">';
			$html .= '<div id="slide-runner" class="'.$random.'">';
				$js = '<script type="text/javascript">if(!window.slider) var slider={};slider.data=[';
				foreach( $this->page->banner as $banner ):
					$html .= '<a href="'.$banner->description.'"><img id="slide-img-'.$count.'" src="'.DOMAIN.$banner->url.'" class="slide" alt="'.$banner->body.'" /></a>';
					$js_a[] = '{"id":"slide-img-'.$count.'","client":"'.$banner->title.'","desc":"'.$banner->body.'"}';
					$count++;
				endforeach;
				$js .= @join(',',$js_a).'];</script>';
			$html .= '</div>';
			$html .= '<div id="slide-controls"><span id="slide-client"><strong>Click image for more...</strong></span><p id="slide-nav"></p></div>';
		$html .= '</div>';
		
		echo $html.$js;
	}
	
}

//$site = new Site;

?>