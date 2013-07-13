<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: vista bibtex para el componente bibdb
 * licencia: GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.html' );

/**
 * Vista bibtex
 *
 */
class BibdbViewBibtex extends JView
{
	/**
	 * Método display() de la vista bibtex
	 *
	 * @return void
	 */
	function display($tpl = null)
	{
		global $option;

		// hoja de estilo
		JHTML::stylesheet( 'estilo.css', 'administrator/components/com_bibdb/assets/' );

		// título de la barra de herramientas
		JToolBarHelper::title( JText::_( 'TITULO_TOOLBAR' ) . ': <small><small>[ Editar ]</small></small>', 'edit-bibtex' );

		// datos obtenidos desde el modelo
		$bibtex =& $this->get( 'Data' );

		// si no se ha devuelto un objeto instancia de la clase JError
		if( !JError::isError($bibtex) )
		{
			// armo la barra de herramientas con los botones guardar, aplicar y cerrar
			JToolBarHelper::save();
			JToolBarHelper::apply();
			// renombro el boton cancel a Close
			JToolBarHelper::cancel( 'cancel', 'Close' );

			$lists = array();

			// construyo la lista desplegable con los ordenes posibles dentro de la categoría
			$query = ' SELECT ordering AS value, CONCAT(bibtexentrytype, " [ ", bibtexcitation, " ]") AS text'
				   . ' FROM #__bibdb_bibtex'
				   . ' WHERE catid = ' . (int) $bibtex['catid']
				   . ' ORDER BY ordering';

			$bibtex_obj = (object)$bibtex;
			$lists['ordering'] 			= JHTML::_( 'list.specificordering', $bibtex_obj, $bibtex_obj->id, $query );

			// construyo la lista de categorías
			$lists['catid'] 			= JHTML::_( 'list.category', 'catid', $option, intval( $bibtex['catid'] ) );

			// construyo los radiobuttons para indicar si deben o no publicarse los datos del BibTeX
			$lists['published'] 		= JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', $bibtex['published'] );

			// botón para elegir que publicación enlazar
			$linkFile = 'index.php?option=com_bibdb&amp;view=uploadfile&amp;file=filename&amp;tmpl=component';
			$buttonFile = new JObject();
			$buttonFile->set('modal', true);
			$buttonFile->set('link', $linkFile);
			$buttonFile->set('text', JText::_( 'Archivo' ));
			$buttonFile->set('name', 'image');
			$buttonFile->set('modalname', 'modal-button-file');
			$buttonFile->set('options', "{handler: 'iframe', size: {x: 620, y: 400}}");

			// botón archivo extra
			$linkFile = 'index.php?option=com_bibdb&amp;view=uploadfile&amp;file=filename_extra&amp;tmpl=component';
			$buttonFile_extra = new JObject();
			$buttonFile_extra->set('modal', true);
			$buttonFile_extra->set('link', $linkFile);
			$buttonFile_extra->set('text', JText::_( 'Archivo extra' ));
			$buttonFile_extra->set('name', 'image');
			$buttonFile_extra->set('modalname', 'modal-button-extra');
			$buttonFile_extra->set('options', "{handler: 'iframe', size: {x: 620, y: 400}}");
			
			// botón para copiar DOI
			//$linkFile = 'index.php?option=com_bibdb&amp;view=uploadfile&amp;tmpl=component';
			$buttonDOI = new JObject();
			$buttonDOI->set('modal', false);
			//$buttonDOI->set('link', $linkFile);
			$buttonDOI->set('text', JText::_( 'Copiar DOI' ));
			$buttonDOI->set('name', 'image');
			$buttonDOI->set('modalname', 'modal-button2');
			//$buttonDOI->set('options', "{handler: 'iframe', size: {x: 620, y: 400}}");

			
			// asigno los datos al template
			$this->assignRef( 'bibtex',		$bibtex );
			$this->assignRef( 'lists',		$lists );
			$this->assignRef( 'buttonfile',	$buttonFile );
			$this->assignRef( 'buttonfileextra',	$buttonFile_extra );
			$this->assignRef( 'buttondoi',		$buttonDOI );

			parent::display();
		}
	}
}