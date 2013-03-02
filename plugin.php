<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Download Manager Plugin
 *
 * @package		PyroCMS
 * @subpackage 	Modules
 * @author		Pascal Kleindienst
 * @copyright 	Copyright (c) 2013, Pascal Kleindienst
 * @link		http://www.pascalkleindienst.de
 * @license		LGPLv3
 */
class Plugin_Download_Manager extends Plugin
{
	/**
     * Lex Tag for downloading a file
     * 
     * Usage:
     * {{ download:file slug="myfile" class="myoptionalclass"}}
     *
     * @access public
     * @return string
     */
    public function file()
    {
    	// get the attributes
    	$slug 	= $this->attribute('slug');
        $class 	= $this->attribute('class');
        $target = $this->attribute('target', '_self');
     
     	// get the file by the slug
        $this->load->model('download_manager_m');   
        $file 	= $this->download_manager_m->get_by_slug($slug);
        
        if($file === FALSE) {
        	$this->load->language('download_manager');
			return lang('dm_plugin.file_not_found');
        }
        
		$class .= ($file->type == Download_Manager_m::$TYPE['EXTERNAL']) ? ' external' : '';
        
        // create the anchor tag
        $string = anchor(
			'download_manager/file/'.$file->slug, 
			'Download: ' . $file->name, 
			'title="Download file '.$file->name.'" class="download_link '.$class.'" target="'.$target.'"' 
		);
        
		return $string;
    }
    
    /**
     * Lex Tag for downloading a file, but with more details available
     * 
     * Usage:
     * {{ download:extended slug="myfile" class="myoptionalclass"}} some text about {{ name }} {{ /download:extended }}
     *
     * @access public
     * @return array
     */
    public function extended()
    {
        $this->load->model('download_manager_m');  
    	$slug = $this->attribute('slug'); 
        $file = $this->download_manager_m->get_by_slug($slug);
        
        if($file === FALSE)
        	return array(array());
        
        $file = array(
			array(
				'name' 		=> $file->name,
				'slug' 		=> $file->slug,
				'url'		=> 'download_manager/file/'.$file->slug,
				'downloads' => $file->downloads,
				'status' 	=> $file->status,
				'access' 	=> $file->login,
			)
		);
		
		return $file;
    }
}