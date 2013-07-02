<?php

class Site {
	
	public $page_id, $page_title, $page_name, $page_description, $page_body, $page_url, $page_parent_id, $page_last_modified, $page_last_modified_by, $page_created, $department_id, $department_title, $department_code, $department_index_id, $department_directory, $department_path, $menu, $breadcrumbs, $breadcrumb, $banners, $sidebar;
	
	public $department, $page, $content;
	
	global $session;

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
		
		if($this->department->dev_mode == '1' && !isset($_SESSION['user_id']))
		{
			redirect_to('/admin/offline.php?r='.base64_encode($_SERVER['REQUEST_URI']));
		}

		// if ($session->is_logged_in())
		// {
		// 	$this->page->body .= '<div><a href="http://medicine.missouri.edu/admin/page.php?action=edit&id='.$this->page->id.'">Edit</a>';
		// }
		
		// instantiate additional objects
		$this->page->parent				= $this->content->find_by_id($this->page->parent_id);
		$this->page->menu				= $this->content->list_all_parents();
		$this->page->banner				= $this->content->list_all_banners();
		$this->department->index		= $this->content->find_by_id($this->department->index_id);
		$this->page->sidebar			= $this->content->find_by_sql("SELECT * FROM cms.posts WHERE url = 'sidebar' && department = ".$this->department->id." LIMIT 1");
		
		// build content
		$this->site_title();
		$this->menu();
		$this->nav_menu();
		$this->breadcrumbs($this->page->id);
		
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
		$this->sidebar					= $this->page->sidebar[0]->body;
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
			for( $i=0; $i<7; $i++ ):
				if( $this->department->index->id == $this->page->menu[$i]->id ) continue;
				if( $this->page->menu[$i] ) {
					$html .= '<li><a';
					if($this->page->menu[$i]->url == $_GET['url'] || $this->page->parent->id == $this->page->menu[$i]->id)
					{
						$html .= ' class="selected"';
					}
					$ext = (!preg_match("@(https?|ftp)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s]*)?$@iS", $this->page->menu[$i]->url)) ? '.html' : '';
					//$html .= '<li><a href="' . $this->page->menu[$i]->url . $ext .'">' .$this->page->menu[$i]->title . '</a></li>';
					
					$html .= ' href="' . $this->page->menu[$i]->url . $ext. '">' .$this->page->menu[$i]->title . '</a></li>';
				}else{
					$html .= '<li><a href="#">Set Menu Item</a></li>';
				}
			endfor;
		endif;
		$html .= '</ul>';
		
		$this->menu = $html;
	}
	
	private function nav_menu()
	{
		global $db;
		$max = 7;
		
		$selects = array(
			'id'		=> 'postmeta.meta_id AS id',
			'guid'		=> 'b.guid',
			'url'		=> 'b.url',
			'title'		=> 'b.title',
			'position'	=> 'a.menu_order AS position',
			'menuitem'	=> 'postmeta.meta_value AS menuitem'
		);
		
		$sql = "SELECT ".join(', ', array_values($selects))."
				FROM postmeta
					JOIN posts a
						ON a.id = postmeta.post_id
					JOIN posts b
						ON b.id = a.parent_id
					WHERE a.post_type = 'nav-menu-item' && a.department = {$this->department->id} 
					ORDER BY a.menu_order";
		
		$items = instantiate($db->query($sql), array_keys($selects));
		
		if($items)
		{
			foreach($items as $item)
			{
				$item->menuitem = unserialize($item->menuitem);
			}
			$menu = $items;
		}
		
		$html = '<ul class="menu">';
		if( ! $items):
			for( $i=1; $i<=$max; $i++ ):
				$html .= '<li><a href="#">Set Menu Item</a></li>';
			endfor;
		else:
			for( $i=0; $i<=$max-1; $i++ ):
				if($menu[$i]):
					$html .= '<li><a ';
					if(($menu[$i]->url && $_GET['url'] && $menu[$i]->url == $_GET['url']) || ($menu[$i]->menuitem->parent_id && $this->page->parent->id && $menu[$i]->menuitem->parent_id == $this->page->parent->id))
					{
						$html .= 'class="selected "';
					}
					if(!preg_match('/medicine\.missouri\.edu/i', $menu[$i]->guid))
					{
						$html .= 'target="_blank" ';
					}
					
					$html .= 'href="'.$menu[$i]->guid.'">'.$menu[$i]->menuitem['title'];
					$html .= '</a></li>';
				else:
					$html .= '<li><a href="#">Set Menu Item</a></li>';
				endif;
			endfor;
		endif;
		$html .= "</ul>";
		
		$this->menus = $html;
	}
	
	
		
	private function breadcrumbs($id=0)
	{
		
		if( $this->page->id == $this->department->index->id ) {
			return;
		}
		
		if($id != 0)
		{
			if($id == $this->page->id)
			{
				$this->breadcrumb[] = '<li>'.$this->page->title.'</li>';
				$this->breadcrumbs($this->page->parent->id);
			}
			else
			{
				$page = $this->content->find_by_id($id);
				$this->breadcrumb[] = '<li><a href="'.$page->guid.'">'.$page->title.'</a> &raquo;</li>';
				$this->breadcrumbs($page->parent_id);
			}
		}
		else
		{
			$breadcrumb = '<ul id="breadcrumbs">';
			$breadcrumb .= '<li><a href="'.$this->content->find_by_id($this->department->index->id)->guid.'">Home</a> &raquo;</li>';
			if(is_array($this->breadcrumb))
			{
				foreach(array_reverse($this->breadcrumb) as $key=>$crumb)
				{
					if( ! empty($crumb))
					{
						$breadcrumb .= $crumb;
					}
				}
			}
			$breadcrumb .= '</ul>';
			
			$this->breadcrumbs = $breadcrumb;
		}
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