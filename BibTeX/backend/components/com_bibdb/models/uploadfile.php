<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: modelo uploadfile del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Modelo uploadfile
 *
 */
class BibdbModelUploadFile extends JModel
{
	function getState($property = null) {
		static $set;

		if (!$set) {
			$folder		= JRequest::getVar( 'folder', '', '', 'path' );

			$this->setState('folder', $folder);

			$parent = str_replace("\\", "/", dirname($folder));
			$parent = ($parent == '.') ? null : $parent;
			$this->setState('parent', $parent);
			
			$set = true;
		}
		return parent::getState($property);
	}

	function getFiles() {
		$list = $this->getList();
		return $list['files'];
	}

	function getFolders() {
		$list = $this->getList();
		return $list['folders'];
	}
	
	function getTypeFile() {
		return "";
	}

	function getList() {
		static $list;

		//Params
		$paramsC	= &JComponentHelper::getParams( 'com_bibdb' );

		// Only process the list once per request
		if (is_array($list)) {
			return $list;
		}

		// Get current path from request
		$current = $this->getState('folder');

		// If undefined, set to empty
		if ($current == 'undefined') {
			$current = '';
		}
		
		$uplFolder = $paramsC->get('upload_folder', 'docs');
		$uplFolder = str_replace('/', DS, JPath::clean($uplFolder));
		$uplFolderRel = str_replace(DS, '/', JPath::clean($uplFolder));

		// armo los paths absoluto y relativos
		$path['orig_abs_ds'] 	= JPATH_ROOT . DS . $uplFolder . DS ;
		$path['orig_abs'] 	= JPATH_ROOT . DS . $uplFolder ;
		$path['orig_rel_ds'] 	= '../' . $uplFolderRel .'/';
		
		// Initialize variables
		if (strlen($current) > 0) {
			$orig_path = $path['orig_abs_ds'].$current;
		} else {
			$orig_path = $path['orig_abs_ds'];
		}
		$orig_path_server 	= str_replace(DS, '/', $path['orig_abs'] .'/');
		
		$files 		= array ();
		$folders 	= array ();

		// Get the list of files and folders from the given folder
		$file_list 		= JFolder::files($orig_path);
		$folder_list 	= JFolder::folders($orig_path, '', false, false, array());
		
		// Iterate over the files if they exist
		//file - abc.img, file_no - folder/abc.img
		if ($file_list !== false) {
			foreach ($file_list as $file) {
				if (is_file($orig_path.DS.$file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html') {			
						$tmp 					= new JObject();
						$tmp->name 				= basename($file);
						$tmp->path_with_name 			= str_replace(DS, '/', JPath::clean($orig_path . DS . $file));
						$tmp->path_without_name_relative	= $path['orig_rel_ds'] . str_replace($orig_path_server, '', $tmp->path_with_name);
						$tmp->path_with_name_relative_no	= str_replace($orig_path_server, '', $tmp->path_with_name);	
						$files[] = $tmp;
						
				}	
			}
		}

		
		// Iterate over the folders if they exist
		if ($folder_list !== false) {
			foreach ($folder_list as $folder)
			{
				$tmp 					= new JObject();
				$tmp->name 				= basename($folder);
				$tmp->path_with_name 			= str_replace(DS, '/', JPath::clean($orig_path . DS . $folder));
				$tmp->path_without_name_relative	= $path['orig_rel_ds'] . str_replace($orig_path_server, '', $tmp->path_with_name);
				$tmp->path_with_name_relative_no	= str_replace($orig_path_server, '', $tmp->path_with_name);	

				$folders[] = $tmp;
			}
		}

		$list = array('folders' => $folders, 'files' => $files);
		return $list;
	}

}