<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: vista uploadfile para el componente bibdb
 * licencia: GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * Vista uploadfile
 *
 */
class BibdbViewUploadfile extends JView
{
	/**
	 * Método display() de la vista uploadfile
	 *
	 * @return void
	 */
// 	var $typefile=""; 

	function display($tpl = null) {

		global $mainframe;

		$paramsC =& JComponentHelper::getParams( 'com_bibdb' );
		$document =& JFactory::getDocument();

		$document->addStyleSheet('../administrator/templates/system/css/system.css');

		// Do not allow cache
		JResponse::allowCache(false);

		$this->assignRef('files', $this->get('files'));
		$this->assignRef('folders', $this->get('folders'));
		$this->assignRef('state', $this->get('state'));
		
		$this->typefile = JRequest::getVar( 'file', "filename", 'get', 'string' );

		// Upload Form ------------------------------------

		// SETTINGS - Upload size
		$upload_maxsize = (int) $paramsC->get('upload_maxsize', '2000000');

		// END Upload Form ------------------------------------

		$this->assignRef('uploadmaxsize', $upload_maxsize);

		parent::display($tpl);
		echo JHTML::_('behavior.keepalive');
	}

	function setFolder($index = 0) {
		if (isset($this->folders[$index])) {
			$this->_tmp_folder = &$this->folders[$index];
		} else {
			$this->_tmp_folder = new JObject;
		}
	}

	function setFile($index = 0) {
		if (isset($this->files[$index])) {
			$this->_tmp_file = &$this->files[$index];
		} else {
			$this->_tmp_file = new JObject;
		}
	}

}