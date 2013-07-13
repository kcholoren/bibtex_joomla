<?php
/**
 * PROYECTO FINAL DE CARRERA
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: Vista bibtex para el front-end del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
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
	 * 
	 * @return void
	 **/
	function display( $tpl = null )
	{
		if( $this->getLayout() == 'form' ) {
			$this->_displayForm( $tpl );
			return;
		}
	}

	/**
	 * Muestra el form para enviar o eventualmente editar los datos de una referencia bibliográfica
	 *
	 * @return void
	 **/
	function _displayForm($tpl)
	{
		global $mainframe, $option;

		// recupero algunos objetos útiles
		$document	=& JFactory::getDocument();
		$user 		=& JFactory::getUser();
		$uri		=& JFactory::getURI();
		$params		=& $mainframe->getPageParameters();
		$menus		=& JSite::getMenu();
		$menu		= $menus->getActive();

		// controlar que el usuario este logueado
		if( $user->guest )
		{
			// el usuario es un huesped, no esta logueado
			JResponse::setHeader('HTTP/1.0 403',true);
			JError::raiseWarning( 403, JText::_('ALERTNOTAUTH') );
			return;
		}

		// obtengo el título a partir de la opción de menú
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			$title_page = $menu_params->get( 'page_title', null );
			if ( !$title_page ) {
				$params->set('page_title', JText::_('ENVIARBIBTEX'));
			}
		} else {
			$params->set('page_title', JText::_('ENVIARBIBTEX'));
		}
		$document->setTitle( $params->get( 'page_title' ) );

		$lists = array();

		// genero la lista desplegable con los ordenes posibles dentro de la categoría
		$bibtexVacio = new StdClass();
		$bibtexVacio->id = 0;
		$bibtexVacio->catid = 0;
		$bibtexVacio->ordering = 0;

		$query = ' SELECT ordering AS value, CONCAT(bibtexentrytype, " [ ", bibtexcitation, " ]") AS text'
			   . ' FROM #__bibdb_bibtex'
			   . ' WHERE catid = ' . (int) $bibtexVacio->catid
			   . ' ORDER BY ordering';

		$lists['ordering'] = JHTML::_( 'list.specificordering', $bibtexVacio, $bibtexVacio->id, $query );

		// genero la lista desplegable de categorías dentro del componente
		$lists['catid'] = JHTML::_( 'list.category', 'catid', $option, 0 );

		// genero un par de radiobuttons para elegir si las nuevas entradas deben estar publicadas o no
		$lists['published'] = JHTML::_( 'select.booleanlist', 'published', 'class="inputbox"', 1, 'Si', 'No' );

		// genero un par de radiobuttons para que el usuario elija adjuntar un documento o no
		$lists['adjuntar'] = JHTML::_( 'select.booleanlist', 'jformadjuntar', 'class="inputbox"', 1, 'Si', 'No' );

		$this->assignRef( 'lists', $lists );
		$this->assignRef( 'params', $params );
		$this->assign( 'action', $uri->toString() );

		parent::display($tpl);
	}
}