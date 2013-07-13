<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: modelo all para el front-end del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.html.pagination' );

/**
 * Modelo all
 *
 */
class BibdbModelAll extends JModel
{
	/**
	 * Arreglo de entradas BibTeX
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Objeto instancia de la clase JPagination
	 *
	 * @var object
	 */
	var $_pagination = null;
  
	/**
	 * Nro total de items devueltos por la consulta
	 *
	 * @var int
	 */
	var $_total;

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{
		global $mainframe, $option;

		parent::__construct();

		// recupero el registro
		// $config =& JFactory::getConfig();

		// parámetros de la página
		$params =& $mainframe->getPageParameters();

		// variables de paginación
		$resporpag =($params->def('mostrar_paginacion', 0)) ? $params->def('res_por_pagina', 10) : 0;
		$this->setState('limit', $resporpag);
		//$this->setState('limit', $mainframe->getUserStateFromRequest($option.'limit', 'limit', $config->getValue('config.list_limit'), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// en caso de que limit haya cambiado, ajustar limitstart adecuadamente
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));
	}

	/**
	 * Genera la parte WHERE correspondiente a una consulta
	 *
	 * @return	string	Parte de una consulta SQL
	 */
	function _buildContentWhere()
	{
		global $mainframe, $option;

		$db		=& JFactory::getDBO();
		$params =& $mainframe->getPageParameters();
		$search	= $mainframe->getUserStateFromRequest($option.'search', 'search', '', 'string');
		$search	= JString::strtolower( $search );

		$where = array();

		// muestro unicamente las referencias bibliográficas publicadas en categorías publicadas
		$where[] = 'a.published = 1';
		$where[] = 'cc.published = 1';

		// me fijo si se desean filtrar publicaciones por un determinado año
		$year = (int) $params->def( 'filtrar_anio', '0' );
		if( $year && $year > 1901 && $year < 2155 ) {
			$where[] = "a.year = $year";
		}

		if ($search)
		{
			$where[] = 'LOWER(a.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ).' OR '
					 . 'LOWER(a.author) LIKE '. $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
  
	/**
	 * Genera la parte ORDER BY correspondiente a una consulta
	 *
	 * @return	string	Parte de una consulta SQL
	 */
	function _buildQueryOrderBy()
	{
		global $mainframe, $option;

		// obtengo el campo por el cual se desean ordenar los registros de la consulta y la dirección de ordenamiento
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'a.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'ASC',			'word');

		if ($filter_order == 'a.ordering'){
			$orderby 	= ' ORDER BY category, a.ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , category, a.ordering ';
		}

		return $orderby;
	}

	/**
	 * Devuelve la consulta
	 *
	 * @access	private
	 * @return	string	La consulta que se utiliza para recuperar todos los BibTeXs
	 */
	function _buildQuery()
	{
		//  obtengo la clausulas WHERE y ORDER BY de la consulta
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildQueryOrderBy();

		$consulta = ' SELECT a.*, cc.title AS category '
				  . ' FROM #__bibdb_bibtex AS a '
				  . ' LEFT JOIN #__categories AS cc ON cc.id = a.catid '
				  . $where
				  . $orderby;

		return $consulta;
	}

	/**
	 * Recuperar de la base de datos los BibTeX teniendo en cuenta los valores de paginación
	 *
	 * @access	public
	 * @return	array	Lista de objetos que contiene todos los BibTeXs
	 */
	function getData()
	{
		if (empty($this->_data))
		{
			$query			= $this->_buildQuery();
			$limitstart		= $this->getState('limitstart');
			$limit			= $this->getState('limit');
			$this->_data	= $this->_getList($query, $limitstart, $limit);
		}

		return $this->_data;
	}

	/**
	 * Get a pagination object
	 *
	 * @access	public
	 * @return	JPagination
	 */
	function getPagination()
	{
		if (empty($this->_pagination))
		{
			// preparo los valores de paginación
			$total		= $this->getTotal(); // nro total de items que se desean paginar
			$limitstart	= $this->getState('limitstart'); // item con el cual comienza la página
			$limit		= $this->getState('limit'); // numero máximo de items por página

			// creo el objeto JPagination y lo cacheo
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}

		return $this->_pagination;
	}

	/**
	 * Obtener el numero total de items BibTeX devueltos por la consulta
	 *
	 * @access	public
	 * @return	integer
	 */
	function getTotal()
	{
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
}