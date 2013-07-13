<?php
/**
 * PROYECTO FINAL DE CARRERA
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: Vista all para el componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.utilities.date' );
jimport( 'joomla.html.html' );

/**
 * Vista all
 *
 */
class BibdbViewAll extends JView
{
	/**
	 * Método display() de la vista all
	 *
	 * @return void
	 */
	function display( $tpl = null )
	{
		global $mainframe, $option;

		// hoja de estilo
		JHTML::stylesheet( 'estilo.css', 'administrator/components/com_bibdb/assets/' );

		// título de la barra de herramientas
		JToolBarHelper::title( JText::_( 'TITULO_TOOLBAR' ), 'ver-referencias' );

		// botones de la barra de herramientas
		JToolBarHelper::publish();
		JToolBarHelper::unpublish();
		JToolBarHelper::deleteList('CONF_DELETELIST');
		JToolBarHelper::editList();
		// Opciones de configuración del componente config.xml
		JToolBarHelper::preferences( 'com_bibdb', '250', '570', 'Preferences', '' );

		// submenú
		JSubMenuHelper::addEntry(JText::_('SUBMENU_VERREF'), 'index.php?option=com_bibdb', true);
		JSubMenuHelper::addEntry(JText::_('SUBMENU_INGREF'), 'index.php?option=com_bibdb&controller=inputbib' );
		JSubMenuHelper::addEntry(JText::_('SUBMENU_BIBCAT'), 'index.php?option=com_categories&section=com_bibdb' );

		// getUserStateFromRequest( string $key, string $request, [string $default = null], [string $type = 'none']) 
		$filter_state		= $mainframe->getUserStateFromRequest( $option.'filter_state',		'filter_state',		'',				'word' );
		$filter_catid		= $mainframe->getUserStateFromRequest( $option.'filter_catid',		'filter_catid',		'',				'int' );
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'ASC',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// filtro para el estado, published o unpublished
		$lists['state'] = JHTML::_('grid.state', $filter_state );

		// construir lista de categorias
		$javascript 	= 'onchange="document.adminForm.submit();"';
		$lists['catid'] = JHTML::_('list.category', 'filter_catid', $option, intval( $filter_catid ), $javascript );

		// ordenamiento de tabla
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] = $filter_order;

		// filtro de búsqueda
		$lists['search'] = $search;

		// obtengo los datos desde el modelo
		$items 		=& $this->get( 'Data' );
		$total 		=& $this->get( 'Total');
		$pagination =& $this->get( 'Pagination' );

		// obtengo la zona horaria del usuario, si es que existe
		$user 		=& JFactory::getUser();
		$usersTZ = $user->getParam( 'timezone' );

		$this->assignRef( 'items',		$items );
		$this->assignRef( 'pagination',	$pagination );
		$this->assignRef( 'lists',		$lists );
		$this->assignRef( 'usersTZ',	$usersTZ );

		parent::display( $tpl );
	}
}