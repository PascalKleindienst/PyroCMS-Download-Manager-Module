<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Download Manager Model
 *
 * @package		PyroCMS
 * @subpackage	Modules
 * @author		Pascal Kleindienst
 * @copyright	Copyright (c) 2013, Pascal Kleindienst
 * @link		http://www.pascalkleindienst.de
 * @license		LGPLv3
 */
 error_reporting(E_ALL);
class Download_Manager_m extends MY_Model 
{
	/**
	 * @access protected
	 * @var array $botlist - Contains a list of bots 
	 */
	protected $botlist = array(
		"Teoma", "alexa", "froogle", "Gigabot", "inktomi",
	    "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
		"Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
		"crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
		"msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
		"Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
		"Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
		"Butterfly","Twitturls","Me.dium","Twiceler"
	);

	/**
	 * @access public
	 * @staticvar array $STATUS - Status of a download file 
	 */
	public static $STATUS = array( 
		'DISABLED' 	=> 0, 
		'ENABLED' 	=> 1
	);
	
	/**
	 * @access public
	 * @staticvar array $ACCESS - Accessibility of a download file 
	 */
	public static $ACCESS = array(
		'LOGIN' => 0,
		'ALL' 	=> 1
	);
	
	/**
	 * @access public
	 * @staticvar array $TYPE - Type of a download file 
	 */
	public static $TYPE = array(
		'EXTERNAL' 	=> 0,
		'LOCAL' 	=> 1
	);
	
	/**
	 * Set the table name
	 * @access public
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_table_name('pk_download_manager');
	}
		
	/**
	 * Get a download file from the database
	 * @access public
	 * @param int $id - ID of the download file
	 * @return mixed $file
	 */
	public function get(int $id)
	{
		$file = parent::get($id);
		
		if($file->type == self::$TYPE['LOCAL'])
		{
			$this->load->library('files/files');
			$fileinfo = Files::get_file($file->file);
			
			if($fileinfo['status'])
				$file->file = $fileinfo['data'];
		}
		
		return $file;
	}
	
	/**
	 * Get a download file from the database by its slug
	 * @access public
	 * @param string $slug - The slug of the download file
	 * @return mixed $file
	 */
	public function get_by_slug(string $slug)
	{
		$file = $this->db->get_where($this->_table, array('slug' => $slug), 1)->row();
		
		if($file->type == self::$TYPE['LOCAL'])
		{
			$this->load->library('files/files');
			$fileinfo = Files::get_file($file->file);
			
			if($fileinfo['status'])
				$file->file = $fileinfo['data'];
		}
		
		return $file;
	}
	
	/**
	 * Get the default folder
	 * @access public
	 * @return int $id
	 */
	public function get_folder()
	{
		$folder = $this->db->select('folder')->get('pk_download_manager_settings', 1)->row();
		return $folder->folder;
	}
	
	/**
	 * Download a file
	 * @access public
	 * @param string $slug - The slug of the file
	 */
	public function download(string $slug)
	{
		$file = $this->get_by_slug($slug);
		
		// Download is disabled, so raise an 404 error
		if($file->status == self::$STATUS['DISABLED'])
		{
			show_404();
		}
		
		// Only loggedin users are able to download the file
		if($file->login == self::$ACCESS['LOGIN'] && ! $this->ion_auth->logged_in())
		{
			die('Action not allowed!');			
		}
		
		
		$this->download_manager_m->increase_download($file->id);
		
		// Download a local file
		if($file->type == self::$TYPE['LOCAL'])
		{
			if(file_exists('uploads/default/files/'.$file->file->filename)) 
			{		
				$this->load->helper('download');
			
				force_download(
					$file->slug . $file->file->extension, 
					@file_get_contents('uploads/default/files/'.$file->file->filename)
				);		
			}
		}
		// Redirect because it's an external file
		else 
		{
			redirect($file->file);
		}
	}
	
	/**
	 * Increase the download counter
	 * @access protected
	 * @param int $id - File download id
	 * @return bool
	 */
	protected function increase_download(int $id)
	{
		if($this->is_bot())
			return false;
			
		$file = $this->get($id);
		$this->update($file->id, array('downloads' => $file->downloads + 1) );
		
		return true;
	}
	
	/**
	 * Check if user is a bot
	 * @access protected
	 * @return bool
	 */
	protected function is_bot()
	{
		foreach($this->botlist as $bot)
		{
			if( strpos($this->input->server('HTTP_USER_AGENT', TRUE), $bot) !== FALSE)
				return TRUE;	
		}
		
		return FALSE;
	}
}