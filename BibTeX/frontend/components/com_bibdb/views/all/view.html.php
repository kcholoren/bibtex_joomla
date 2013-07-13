<?php
/**
 * PROYECTO FINAL DE CARRERA
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: Vista all para el front-end del componente bibdb
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
	 * @access	public
	 * @param	string	$tpl	El nombre del archivo template a parsear
	 * @return	void
	 */
	function display( $tpl = null )
	{
		global $mainframe, $option;
		
		// inicializo ciertas variables
		$document			=& JFactory::getDocument();
		$uri 				=& JFactory::getURI();
		// getUserStateFromRequest( string $key, string $request, [string $default = null], [string $type = 'none']) 
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'ASC',			'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		// configuración de la página/componente
		$params =& $mainframe->getPageParameters();

		// obtengo los datos desde el modelo
		$items 		=& $this->get( 'Data' );
		$total 		=& $this->get( 'Total' );
		$pagination =& $this->get( 'Pagination' );

		// ordenamiento de tabla
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// filtro de busqueda
		$lists['search']= $search;

		// recupero los parámetros del item de menú activo
		$menus = JSite::getMenu();
		$menu  = $menus->getActive();
		// título de la página
		$page_title = JText::_( 'Referencias bibliográficas' );
		if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if ( !$menu_params->get( 'page_title' ) ) {
				$params->set( 'page_title', $page_title );
			}
		} else {
			$params->set( 'page_title', $page_title );
		}
		$document->setTitle( $params->get( 'page_title' ) );

		/*
		 * icono documento disponible
		 */
		$iconoDocDisp = JHTML::_('image.site', $params->get( 'pdf_enabled_alt', 'pdf-file-enabled-24.png' ), '/images/bibdb/', null, null, 'Bajar');

		/*
		 * icono documento no disponible
		 */
		$iconoDocNoDisp = JHTML::_('image.site', $params->get( 'pdf_disabled_alt', 'pdf-file-disabled-24.png' ), '/images/bibdb/', null, null, 'n/d' );

		/*
		 * icono BibTeX
		 */
		$imagenBib = JHTML::_('image.site', $params->get( 'bib_alt', 'bib-file-24.png' ), '/images/bibdb/', null, null, 'Bajar' );

		/*
		 * parámetros ventana modal con información de un BibTeX
		 */
		// agrego JavaScript al encabezado del documento
		JHTML::_('behavior.modal', 'a.mymodal', array('size' => array('x' => 100, 'y' => 100)));

		$k = 0;
		$count = count($items);
		for($i = 0; $i < $count; $i++)
		{
			$item =& $items[$i];
			
			/*
			 * expandir los autores, separándolos uno por línea
			 */
			$tmp = explode( ' and ', $item->author );
			$item->author = implode( ", ", $tmp );

			/*
			 * armar el detalle de la referencia BibTeX
			 */
			$item->extendida = $this->_formatearInfo( $item );

			/*
			 * enlace de descarga, si el documento electrónico está disponible
			 */
			if( !$item->path ) {
				// documento no disponible, renderizo un icono pdf en escala de grises
				$item->pdf = $iconoDocNoDisp;
			} else {
				// tiene un documento electrónico asociado
				$link = JRoute::_( "index.php?option=com_bibdb&task=download&id=" . $item->id );
				$item->pdf = "<a href=\"$link\" title=\"".JText::_('LABEL_DOWNLOAD_FILE')."\">$iconoDocDisp</a>";
			}

			/*
			 * icono para desplegar la entrada BibTeX completa
			 */
			$link = JRoute::_( "index.php?option=com_bibdb&view=bibpopup&id=" . $item->id );
			$item->bib = "<a class=\"mymodal\" title=\"". JText::_( 'LEGEND_DETALLES' ) ."\" href=\"$link\" rel=\"{handler: 'iframe', size: {x: 600, y: 400}}\">$imagenBib</a>";

			$item->odd 	 = $k;
			$item->count = $i;
			$k			 = 1 - $k;
		}

		$this->assignRef( 'items',		$items );
		$this->assignRef( 'pagination',	$pagination );
		$this->assignRef( 'lists',		$lists );
		$this->assignRef( 'params',		$params );

		$this->assign( 'action', $uri->toString() );

		parent::display( $tpl );
	}

	/**
	 * Concentra la información de un BibTeX, concatenando los valores de ciertos campos siempre que
	 * esten definidos
	 *
	 * @access	private
	 * @param	object	$bibtex	Un objeto que mantiene la información de una referencia bibliográfica
	 * @return	string			Información del BibTeX formateada
	 */
	function _formatearInfo( &$bibtex )
	{
		$res = '';
		// campo chapter, con negrita y cursiva
		if( $bibtex->chapter ) { $res .= "<i><b>" . trim($bibtex->chapter) . "</b></i>, "; }
		// campo título, con negrita y cursiva
		if( $bibtex->title ) {
			$tit_aux = trim($bibtex->title);
			if ( $bibtex->url ) {
				$tit_aux = "<a href=\"" . $bibtex->url . "\">" . $tit_aux . "</a>";
			}
			$res .= "<i><b>" . $tit_aux . "</b></i>, "; 
		}
		// campo autor, en cursiva
		if( $bibtex->author ) { $res .= "<i>" . trim($bibtex->author) . "</i>. "; }
		// campo booktitle o journal, pero no ambos
		if( $bibtex->booktitle ) {
			$res .= trim($bibtex->booktitle) . ", ";
		} elseif ( $bibtex->journal) {
			$res .= trim($bibtex->journal) . ", ";
		}
		// campo editor
		if( $bibtex->editor ) { $res .= trim($bibtex->editor) . ". "; }
		// campo volume
		if( $bibtex->volume ) { $res .= "Vol. " . trim($bibtex->volume) . " "; }
		// campo pages
		if( $bibtex->pages ) { $res .= "pp. " . trim($bibtex->pages) . ". "; }
		// campo año
		if( $bibtex->year ) { $res .= $bibtex->year; }

		return $res;
	}
}
