<?php
/**
 * PROYECTO FINAL DE CARRERA
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: Vista inputbib para el componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.html' );

/**
 * Vista inputbib
 *
 */
class BibdbViewInputbib extends JView
{
	/**
	 * 
	 * @return void
	 **/
	function display( $tpl = null )
	{
		global $mainframe, $option;
	
		// hoja de estilo
		JHTML::stylesheet( 'estilo.css', 'administrator/components/com_bibdb/assets/' );
	
		// título de la barra de herramientas
		JToolBarHelper::title( JText::_( 'TITULO_TOOLBAR' ), 'ingresar-referencias' );

		// submenú
		JSubMenuHelper::addEntry(JText::_( 'SUBMENU_VERREF' ), 'index.php?option=com_bibdb' );
		JSubMenuHelper::addEntry(JText::_( 'SUBMENU_INGREF' ), 'index.php?option=com_bibdb&controller=inputbib', true );
		JSubMenuHelper::addEntry(JText::_( 'SUBMENU_BIBCAT' ), 'index.php?option=com_categories&section=com_bibdb' );

		$lists = array();

		// obtengo el layout establecido para la vista
		$layName = $this->getLayout();
		if( $layName == 'default' )
		{
			// preparo las opciones de la lista desplegable
			$options = array();
			$options[] = JHTML::_('select.option', 'formstr', 'Pegar string con BibTeXs');
			$options[] = JHTML::_('select.option', 'formfile', 'Archivo BibTeX [*.bib]');

			// armo la lista desplegable
			$lists['entrada'] = JHTML::_('select.genericlist', $options, 'inputList', null, 'value', 'text', '1');
		}
		else
		{
			$bibtexVacio = new StdClass();
			$bibtexVacio->id = 0;
			$bibtexVacio->catid = 0;
			$bibtexVacio->ordering = 0;
			$bibtexVacio->published = 1;

			// genero la lista desplegable con los ordenes posibles dentro de la categoría
			$query = ' SELECT ordering AS value, CONCAT(bibtexentrytype, " [ ", bibtexcitation, " ]") AS text'
				   . ' FROM #__bibdb_bibtex'
				   . ' WHERE catid = ' . (int) $bibtexVacio->catid
				   . ' ORDER BY ordering';

			$lists['ordering'] 			= JHTML::_( 'list.specificordering', $bibtexVacio, $bibtexVacio->id, $query );

			// genero la lista desplegable de categorías dentro del componente
			
			$lists['catid'] 			= JHTML::_( 'list.category', 'catid', $option, 0);
			// esto mueve la opción selected a la primera categoría si hay una sola opción
			// y evita tener que elegir una opción cuando es única. Kcho 2012
			$pepe = $lists['catid'];
			if (substr_count($pepe, '<option') < 3) {
				$pos = strpos($pepe, 'selected');
				$new_pepe1 = substr($pepe, 0, $pos);
				$lists['catid'] = $new_pepe1;
				$new_pepe2 = substr($pepe, $pos +1); 

				$pos = strpos($new_pepe2, '>');
				$pos2 = strpos($new_pepe2, '<option');

				$lists['catid'] .=  substr($new_pepe2, $pos, $pos2-$pos);
				$new_pepe3 = substr($new_pepe2, $pos2);
				$pos3 = strpos($new_pepe3, '>');
				$lists['catid'] .= substr($new_pepe3, 0, $pos3);
			  
				$lists['catid'] .= ' selected="selected"';// .substr($new_pepe2, $pos3); echo "\n";
				$lists['catid'] .= substr($new_pepe3, $pos3); 
			}
			// genero el par de radiobuttons para elegir si las nuevas entradas deben estar publicadas o no
			$lists['published'] 		= JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', 1 );
		}

		$this->assignRef('lists', $lists);

		parent::display($tpl);
	}
}