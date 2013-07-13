<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: controlador por defecto para el gestor de BibTeXs
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.application.component.helper' );
jimport( 'joomla.filesystem.file' );

class BibdbController extends JController
{
	/**
	 * constructor (registrar tareas a métodos)
	 *
	 * @return	void
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask( 'download', 'forceDownload' );
		$this->registerTask( 'downloadextra', 'forceDownloadextra' );
	}

	/**
	 * Muestra la vista elegida por el usuario
	 *
	 * @access	public
	 * @return	void
	 */
	function display()
	{
		// establecer una vista por defecto
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'all' );
		}

		parent::display();
	}

	
	function forceDownloadextra(){
		$this->forceDownload(true);
	}
	
	/**
	 * Método utilizado al descargar un archivo desde el servidor
	 *
	 * @access	public
	 * @return	void
	 */
	function forceDownload($extra=false)
	{
		global $mainframe;
		$user		=& JFactory::getUser();
		$aid		= (int) $user->get('aid', 0);
		$paramsC	=& $mainframe->getParams();

		//JResponse::allowCache(false);

		$model =& $this->getModel('Bibtex', 'BibdbModel');

		$bibtex =& $model->getData();

		if( JError::isError($bibtex) )
		{
			JError::raiseError (500, "ERROR.FILE_NOT_FOUND");
			exit;
		}

		session_cache_limiter('public');

		// to avoid an error notice of an undefined index.
		if(empty($_SERVER['HTTP_REFERER'])) $_SERVER['HTTP_REFERER']='NoRef';
		if(empty($_SERVER['REMOTE_ADDR'])) $_SERVER['REMOTE_ADDR']='1.1.1.1';

		// Clean them variables boys  (always clean variables at the start of your script to prevent injection attacks. Always limit input to expected chars and patterns.)
		if(preg_match('/^([A-Za-z0-9.?=_\-\/:\s(%20)]{1,255})$/', stripslashes($_SERVER['HTTP_REFERER']), $matchref))
		{
			$tempvar = $matchref[0];
		} else {
			$tempvar='NoRef';
		}
		define('HTTP_REF', $tempvar);

		if(preg_match('/^([0-9.]{7,24})$/', stripslashes($_SERVER['REMOTE_ADDR']), $matchadd))
		{
			$tempvar = $matchadd[0];
		} else {
			$tempvar='1.1.1.1';
		}

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))
		  ini_set('zlib.output_compression', 'Off');

		if( is_null($bibtex->path) || empty($bibtex->path) )
		{
			// $bibtex->path es null o esta vacío
			JError::raiseError (500, "File name not specified");
		} else {
			
			$uplFolder = $paramsC->get('upload_folder', 'docs');
			
			$uplFolder = str_replace('/', DS, JPath::clean($uplFolder));
			
			if ($extra) {
				$filename = JPATH_SITE . DS . $uplFolder . DS . $bibtex->path_extra;
			} else {
				$filename = JPATH_SITE . DS . $uplFolder . DS . $bibtex->path;
			}
			//echo $filename; die();
			if (!JFile::exists($filename))
			{
				JError::raiseError (500, "Oops. File not found");
				exit;
			}
		}

		session_write_close();

		//IE Bug in download name workaround
		if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
			@ini_set( 'zlib.output_compression','Off' );
		}

		if (!$this->_downloadFile($filename)) {
			JError::raiseError('', 'The file transfer failed');
		}

		die(); //if you do not

	}

	/**
	 * Método privado que se utiliza para descargar un archivo
	 *
	 * @access	private
	 * @param	string 	$fil	El nombre del archivo que se desea descargar
	 * @return	boolean			Verdadero en caso de que la descarga se haya podido efectuar
	 */
	function _downloadFile($fil,$p=null)
	{	
		// only show errors and remove warnings from corrupting file
		error_reporting(E_ERROR);
		
		ob_clean();
		if (connection_status()!=0) return(FALSE);

		$fn = basename($fil);
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
		header("Content-Transfer-Encoding: binary");
		
		//TODO:  Not sure of this is working
		if (function_exists('mime_content_type')) {
			$ctype = mime_content_type($fil);
		}
		else if (function_exists('finfo_file')) {
		        $finfo    = finfo_open(FILEINFO_MIME);
		        $ctype = finfo_file($finfo, $fil);
		        finfo_close($finfo);
		}
		else {
			$ctype = "application/octet-stream";
		}
		
		header('Content-Type: ' . $ctype);

		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
		{
			//workaround for IE filename bug with multiple periods / multiple dots in filename
			//that adds square brackets to filename - eg. setup.abc.exe becomes setup[1].abc.exe
			$iefilename = preg_replace('/\./', '%2e', $fn, substr_count($fn, '.') - 1);
			header("Content-Disposition: attachment; filename=\"$iefilename\"");
		}
		else
		{
			header("Content-Disposition: attachment; filename=\"$fn\"");
		}

		header("Accept-Ranges: bytes");

		$range = 0; // default to begining of file
		//TODO make the download speed configurable
		$size=filesize($fil);

		//check if http_range is set. If so, change the range of the download to complete.
		if(isset($_SERVER['HTTP_RANGE']))
		{
			list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']);
			str_replace($range, "-", $range);
			$size2=$size-1;
			$new_length=$size-$range;
			header("HTTP/1.1 206 Partial Content");
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range$size2/$size");
		}
		else
		{
			$size2=$size-1;
			header("HTTP/1.0 200 OK");
			header("Content-Range: bytes 0-$size2/$size");
			header("Content-Length: ".$size);
		}

		//check to ensure it is not an empty file so the feof does not get stuck in an infinte loop.
		if ($size == 0 ) {
			JError::raiseError (500, 'ERROR.ZERO_BYE_FILE');
			exit;
		}
		set_magic_quotes_runtime(0); // in case someone has magic quotes on. Which they shouldn't as good practice.
		
		// we should check to ensure the file really exits to ensure feof does not get stuck in an infite loop, but we do so earlier on, so no need here.
		$fp=fopen("$fil","rb");

		//go to the start of missing part of the file
		fseek($fp,$range);
		if (function_exists("set_time_limit")) 
			set_time_limit(0);
		while(!feof($fp) && connection_status() == 0)
		{
			//reset time limit for big files
			if (function_exists("set_time_limit")) 
				set_time_limit(0);
			print(fread($fp,1024*8));
			flush();
			ob_flush();	
		}
		sleep(1);
		fclose($fp);
		return((connection_status()==0) and !connection_aborted());
	}
}