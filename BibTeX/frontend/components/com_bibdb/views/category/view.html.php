<?php
/**
 * PROYECTO FINAL DE CARRERA
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: Vista category para el front-end del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.utilities.date' );
jimport( 'joomla.html.html' );

/**
 * Vista category
 *
 */
class BibdbViewCategory extends JView
{
	/**
	 * Método display() de la vista category
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
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order', 'ordering', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'ASC', 'word' );
		$search				= $mainframe->getUserStateFromRequest( $option.'search', 'search', '', 'string' );
		$search				= JString::strtolower( $search );

		// configuración de la página/componente
		$params =& $mainframe->getPageParameters();

		// obtengo los datos desde el modelo
		$items 		=& $this->get( 'Data' );
		$category	=& $this->get( 'Category' );//print_r($category);die();
		$total 		=& $this->get( 'Total' );
		$pagination =& $this->get( 'Pagination' );

		// ordenamiento de tabla
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// filtro de busqueda
		$lists['search']= $search;
		$is_multicategory = ((is_array($items))&&(is_array($items[0])));
		// recupero los parámetros del item de menú activo
		if(!$is_multicategory){
			$menus =& JSite::getMenu();
			$menu  = $menus->getActive();
			// título de la página
			if (is_object( $menu )) {
				$menu_params = new JParameter( $menu->params );
				if ( !$menu_params->get( 'page_title' ) ) {
					// no se ha establecido aún un título para la página.
					// A continuación lo seteo de manera que coincida con el nombre de la categoría
					$params->set( 'page_title', $category[0]->title );
				}
			} else {
				$params->set( 'page_title', $category[0]->title );
			}
			
			if ( $params->get( 'filtrar_anio' ) != 0 ) {
				$document->setTitle( $params->get( 'page_title' ) . '('.$params->get( 'filtrar_anio' ).')' );
			} else {
				$document->setTitle( $params->get( 'page_title' ) );
			}
			// descripción de la categoría
			$category[0]->description = JHTML::_('content.prepare', $category[0]->description);
			// imagen de la categoría
			if (isset( $category[0]->image ) && $category[0]->image != '')
			{
				$attribs['align']  = $category[0]->image_position;
				$attribs['hspace'] = 6;

				// construyo el tag de la imagen usando la librería JHTML
				$category[0]->image = JHTML::_('image', 'images/stories/'.$category[0]->image, JText::_('IMAGE'), $attribs);
			}

			$this->_arreglarBibTeX($items,$params);
		}else{
			foreach($items as $one_cat){
				$this->_arreglarBibTeX($one_cat,$params);
			}
		}


		
		$this->assignRef( 'items',		$items );
		$this->assignRef( 'category',	$category);
		$this->assignRef( 'pagination',	$pagination );
		$this->assignRef( 'lists',		$lists );
		$this->assignRef( 'params',		$params );

		$this->assign( 'action', $uri->toString() );
		
		$style = 'img.bib, img.is_pdf, img.no_pdf, img.is_ext{height: 24px;width: 24px;background-image:url('.$this->baseurl.'/images/bibdb/bib-files.png);}
img.bib{background-position: 0 0;}
img.is_pdf{background-position: -73px 0;}
img.no_pdf{background-position: -49px 0;}
img.is_ext{background-position: -25px 0;}';
		
		$document->addStyleDeclaration( $style );
		/*
		 * parámetros ventana modal con información de un BibTeX
		 */
		// agrego JavaScript al encabezado del documento
		JHTML::_('behavior.modal', 'a.mymodal', array('size' => array('x' => 100, 'y' => 100)));

		parent::display( $tpl );
	}

	function _arreglarBibTeX(& $items,$params){
	
		/*
		 * icono documento disponible
		 */
		$iconoDocDisp = JHTML::_('image.site', 'img_trans.gif', '/images/bibdb/', null, null, JText::_('LABEL_DOWNLOAD_FILE'), 'class="is_pdf" width="1" height="1"');

		$iconoExtraDisp = JHTML::_('image.site', 'img_trans.gif', '/images/bibdb/', null, null, JText::_('LABEL_DOWNLOAD_FILE'), 'class="is_ext" width="1" height="1"');

		
		/*
		 * icono documento no disponible
		 */
		$iconoDocNoDisp = JHTML::_('image.site', 'img_trans.gif', '/images/bibdb/', null, null, JText::_('LABEL_NO_FILE'), 'class="no_pdf" width="1" height="1"' );

		/*
		 * icono BibTeX
		 */
		$imagenBib = JHTML::_('image.site', 'img_trans.gif', '/images/bibdb/', null, null, JText::_('LABEL_DOWNLOAD_BIB'), 'class="bib" width="1" height="1"' );

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

			if( !isset($item->path_extra) ) {
				// documento no disponible, renderizo un icono pdf en escala de grises
				$item->pdf_extra = $iconoDocNoDisp;
			} else {
				// tiene un documento electrónico asociado
				$link = JRoute::_( "index.php?option=com_bibdb&task=downloadextra&id=" . $item->id );
				$item->pdf_extra = "<!--  --><a href=\"$link\" title=\"".JText::_('LABEL_DOWNLOAD_FILE')."\">$iconoExtraDisp</a>";
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
