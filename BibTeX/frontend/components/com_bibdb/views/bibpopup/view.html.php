<?php
/**
 * PROYECTO FINAL DE CARRERA
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: Vista bibpopup para el front-end del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
// jimport( 'joomla.utilities.date' );
// jimport( 'joomla.html.html' );

/**
 * Vista bibpopup
 *
 */
class BibdbViewBibPopUp extends JView
{
	/**
	 * Método display() de la vista category
	 *
	 * @return	void
	 */
	function display( $tpl = null )
	{
 		$document =& JFactory::getDocument();
//  		print_r($document);die();
 		$document->setGenerator("");
// die($document->getGenerator());
// 		$document->addStyleSheet('./templates/system/css/system.css');

		// recuperar los datos del BibTeX
		$model =& JModel::getInstance('bibtex', 'bibdbmodel');
		$bibtex =& $model->getData();
		
		if( !JError::isError($bibtex) ) {

			$this->assignRef( 'bibtex', $bibtex );

			parent::display( $tpl );
		}
	}

}
