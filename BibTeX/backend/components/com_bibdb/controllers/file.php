<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: controlador file para el componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.path');

/**
 * Controlador file
 *
 */
class BibdbControllerFile extends JController
{

	/**
	 * Constructor de la clase BibdbControllerFile
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
		// Register Extra tasks
		$this->registerTask( 'upload',		 'upload'		 );
		$this->registerTask( 'createfolder', 'createfolder'	 );
	}

	/**
	 * Crea una carpeta
	 *
	 * @access	public
	 * @return	void
	 */
	function createfolder()
	{
		global $mainframe;

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$folderNew	= JRequest::getCmd( 'foldername', '' );
		$folderCheck	= JRequest::getVar( 'foldername', null, '', 'string', JREQUEST_ALLOWRAW );
		$parent		= JRequest::getVar( 'folderbase', '', '', 'path' );
		$return		= JRequest::getVar( 'return-url', null, 'post', 'base64' );
		$viewBack	= JRequest::getVar( 'viewback', '', '', '' );

		$link = base64_decode($return).'&folder='.$parent;//'';
// 		$mainframe->redirect(base64_decode($return).'&folder='.$folder);

		switch ($viewBack) {
			case 'bibdbmanager:file':
				//$link	= 'index.php?option=com_bibdb&view=uploadfile&tmpl=component&folder='.$parent;

				$paramsC =& JComponentHelper::getParams( 'com_bibdb' );
				$uplFolder = $paramsC->get('upload_folder', 'docs');
				$uplFolder = str_replace('/', DS, JPath::clean($uplFolder));
				$uplFolderRel = str_replace(DS, '/', JPath::clean($uplFolder));

				// armo los paths absolutos y relativos
				$path['orig_abs_ds'] 	= JPATH_ROOT . DS . $uplFolder . DS ;
				$path['orig_abs'] 		= JPATH_ROOT . DS . $uplFolder ;
				$path['orig_rel_ds'] 	= '../' . $uplFolderRel .'/';
			break;

			default:
				$mainframe->redirect('index.php?option=com_bibdb', 'Controller U Error');
			break;
		
		}

		JRequest::setVar('folder', $parent);

		if (($folderCheck !== null) && ($folderNew !== $folderCheck)) {
			$mainframe->redirect($link, JText::_('WARNDIRNAME'));
		}

		if (strlen($folderNew) > 0) {
			$path = JPath::clean($path['orig_abs_ds'].DS.$parent.DS.$folderNew);
			if (!is_dir($path) && !is_file($path))
			{
				JFolder::create($path);
				JFile::write($path.DS."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
				
				$mainframe->redirect($link, JText::_('FOLDERCREATED'));
			} else {
				$mainframe->redirect($link, JText::_('FOLDEREXISTS'));
			}
			//JRequest::setVar('folder', ($parent) ? $parent.'/'.$folder : $folder);
		}

		$mainframe->redirect($link);
	}

	/**
	 * Método utilizado para establecer si un archivo dado puede subirse o no
	 *
	 * @access	private
	 * @param	array	$file	Las propiedades del archivo
	 * @param	string	$err	String para manejar la descripción del error
	 * @return	boolean			Verdadero en caso de que pueda subirse el archivo
	 */
	function _canUpload( $file, &$err )
	{
		$config =& JComponentHelper::getParams( 'com_bibdb' );

		if(empty($file['name']))
		{
			$err = 'WARNEMPTYNAME';
			return false;
		}

		if ($file['name'] !== JFile::makesafe($file['name']))
		{
			$err = 'WARNFILENAME';
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));
		$allowable = explode( ',', $config->get( 'upload_extensions', 'doc,pdf,txt,DOC,PDF,TXT' ) );
		if (!in_array($format, $allowable))
		{
			$err = 'WARNFILETYPE';
			return false;
		}

		$maxSize = (int) $config->get( 'upload_maxsize', 0 );
		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$err = 'WARNFILETOOLARGE';
			return false;
		}

		// puede efectuarse la subida
		return true;
	}

	/**
	 * Método utilizado para subir un archivo al servidor
	 *
	 * @access	public
	 * @return	void
	 */
	function upload()
	{
		global $mainframe;
	
		// Check for request forgeries
		JRequest::checkToken( 'request' ) or jexit( 'Invalid Token' );

		$file 		= JRequest::getVar( 'Filedata', '', 'files', 'array' );
		$folder		= JRequest::getVar( 'folder', '', '', 'path' );
		$format		= JRequest::getVar( 'format', 'html', '', 'cmd');
		$return		= JRequest::getVar( 'return-url', null, 'post', 'base64' );
		$viewBack	= JRequest::getVar( 'viewback', '', '', '' );
		$err			= null;
		
		switch ($viewBack) {
			case 'bibdbmanager:file':
				$link	= 'index.php?option=com_bibdb&view=uploadfile&tmpl=component&folder='.$folder;

				$paramsC =& JComponentHelper::getParams( 'com_bibdb' );
				$uplFolder = $paramsC->get('upload_folder', 'docs');
				$uplFolder = str_replace('/', DS, JPath::clean($uplFolder));
				$uplFolderRel = str_replace(DS, '/', JPath::clean($uplFolder));

				// armo los paths absoluto y relativos
				$path['orig_abs_ds'] 	= JPATH_ROOT . DS . $uplFolder . DS ;
				$path['orig_abs'] 		= JPATH_ROOT . DS . $uplFolder ;
				$path['orig_rel_ds'] 	= '../' . $uplFolderRel .'/';
			break;
			
			default:
				$mainframe->redirect('index.php?option=com_bibdb', 'Controller U Error');
			break;
		}

		// Make the filename safe
		if (isset($file['name'])) {
			$file['name']	= JFile::makeSafe($file['name']);
		}

		// All HTTP header will be overwritten with js message
		if (isset($file['name'])) {
			$filepath = JPath::clean($path['orig_abs_ds'].$folder.DS.strtolower($file['name']));
			if (!$this->_canUpload( $file, $err )) {
				
				if ($format == 'json') {
					switch ($err) {
						case 'WARNFILETOOLARGE':
							header('HTTP/1.0 413 Request Entity Too Large');
							jexit('Error. The File Is Too Large!');
						break;
						
						default:
							header('HTTP/1.0 415 Unsupported Media Type');
							jexit('Error. Unsupported Media Type!');
						break;
					}	
				} else {
					JError::raiseNotice(100, JText::_($err));
					// REDIRECT
					if ($return) {
						$mainframe->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			}

			if (JFile::exists($filepath)) {
				if ($format == 'json') {
					header('HTTP/1.0 409 Conflict');
					jexit('Error. File already exists');
				} else {
					JError::raiseNotice(100, JText::_('WARNFILEEXISTS'));
					// REDIRECT
					if ($return) {
						$mainframe->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			}

			if (!JFile::upload($file['tmp_name'], $filepath)) {
				if ($format == 'json') {
					header('HTTP/1.0 406 Not Acceptable');
					jexit('Error. Unable to upload file');
				} else {
					JError::raiseWarning(100, JText::_('WARNJFILEUPLOAD'));
					// REDIRECT
					if ($return) {
						$mainframe->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			} else {
				if ($format == 'json') {
					header('HTTP/1.0 400');// With 400 error will be not displayed (?? - ok)
					jexit('Upload complete');
				} else {
					$mainframe->enqueueMessage(JText::_('SUCCESSUPLOAD'));
					// REDIRECT
					if ($return) {
						$mainframe->redirect(base64_decode($return).'&folder='.$folder);
					}
					return;
				}
			}
		} else {
			$msg = JTEXT::_('WARNEMPTYNAME');
			if ($format == 'json') {
					header('HTTP/1.0 415 Unsupported Media Type');
					jexit('Error. Unable to upload file');
				} else {
				if ($return) {
					$mainframe->redirect(base64_decode($return).'&folder='.$folder, $msg);
				} else {
					$mainframe->redirect($link, $msg);
				}
			}
		}
	}

}
