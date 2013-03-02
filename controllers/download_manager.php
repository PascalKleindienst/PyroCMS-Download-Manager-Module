<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Download Manager Frontend Controller
 *
 * @package		PyroCMS
 * @subpackage	Modules
 * @author		Pascal Kleindienst
 * @copyright	Copyright (c) 2013, Pascal Kleindienst
 * @link		http://www.pascalkleindienst.de
 * @license		LGPLv3
 */
class Download_Manager extends Public_Controller
{
	/**
	 * Load the model
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();	
		$this->load->model('download_manager_m');
	}
	
	/**
	 * Download the file by its slug
	 * @access public
	 */
	public function file($fileslug)
	{
		$this->download_manager_m->download($file->$fileslug);
	}
}