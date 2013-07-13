<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: modelo category para el front-end del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.html.pagination' );

/**
 * Modelo category
 *
 */
class BibdbModelCategory extends JModel
{
	/**
	 * ID de la categor�a
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * conjunto de BibTeXs que pertenecen a la categor�a con ID $_id
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Datos de la categor�a
	 *
	 * @var object
	 */
	var $_category = null;

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

		// par�metros de la p�gina
		$params =& $mainframe->getPageParameters();

		// variables de paginaci�n
		$resporpag =($params->def('mostrar_paginacion', 0)) ? $params->def('res_por_pagina', 10) : 0;
		$this->setState('limit', $resporpag);
		//$this->setState('limit', $mainframe->getUserStateFromRequest($option.'limit', 'limit', $config->getValue('config.list_limit'), 'int'));
		$this->setState('limitstart', JRequest::getVar('limitstart', 0, '', 'int'));

		// en caso de que limit haya cambiado, ajustar limitstart adecuadamente
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));

		// recupero el id de la categor�a
		// de los parámetros del menú o del valor de la categoría
		// si quiero mostrar múltiples categoría es la 1er opción.
		// Kcho 2009


		$catid = $params->get('catid', JRequest::getVar('catid', 0, '', 'int'));
		$this->setId($catid);
	}

	/**
	 * M�todo para setear el ID de la categor�a con la que trata el modelo
	 *
	 * @access	public
	 * @param	int		ID de la categor�a
	 */
	function setId( $id )
	{
		$this->_id			= $id;
		$this->_category	= null;
		$this->_data		= null;
	}

	/**
	 * M�todo para cargar los datos de una categor�a
	 *
	 * @access	private
	 * @return	boolean	Verdadero en caso de �xito
	 */
	function _loadCategory()
	{
		if( empty($this->_category) )
		{
			if(is_array($this->_id)) {
				$condition = 'c.id IN (' . implode( ',', $this->_id ) . ')';

			} else {
				$condition = 'c.id = '.$this->_id;
			}

			$query = ' SELECT c.*, ' .
					 ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
					 ' FROM #__categories AS c' .
					 ' WHERE '. $condition .
					 ' AND c.section = "com_bibdb"';

			$this->_category = $this->_getList($query);//print_r($this->_category);
//			$this->_db->setQuery($query, 0, 1);
//			$this->_category = $this->_db->loadObject();
			if ((is_array($this->_category))&&(count($this->_category)>0)){
//die();
				return true;
			}else{
				return false;	
			}
		}

		return true;
	}

	/**
	 * M�todo para devolver la informaci�n de una categor�a
	 *
	 * @access	public
	 */
	function getCategory()
	{
		// intento cargar la informaci�n de la categor�a
		if ($this->_loadCategory())
		{
			// recupero informaci�n del usuario
			$user = &JFactory::getUser();
//die('category');

			for($i=0;$i<count($this->_category);$i++){
				// verificar que la categor�a este publicada
				if (!$this->_category[$i]->published) {
					JError::raiseError(404, JText::_("Resource Not Found"));
					return false;
				}
				// chequear que el nivel de acceso de la categor�a permita al usuario ver los datos
				if ($this->_category[$i]->access > $user->get('aid', 0)) {
					JError::raiseError(403, JText::_("ALERTNOTAUTH"));
					return false;
				}
			}
		}

		return $this->_category;
	}

	/**
	 * Genera la parte WHERE correspondiente a una consulta
	 *
	 * @access	private
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

		// muestro unicamente las referencias bibliogr�ficas publicadas
		$where[] = 'published = 1';

		// me fijo si se desean filtrar publicaciones por un determinado a�o
		$year = (int) $params->def( 'filtrar_anio', '0' );
		if( $year && $year > 1901 && $year < 2155 ) {
			$where[] = "year = $year";
		}

		if ($search) {
			$where[] = 'LOWER(title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ).' OR '
					 . 'LOWER(author) LIKE '. $db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}

		$where = ( count( $where ) ? implode( ' AND ', $where ) : '' );

		return $where;
	}

	/**
	 * Genera la parte ORDER BY correspondiente a una consulta
	 *
	 * @access	private
	 * @return	string	Parte de una consulta SQL
	 */
	function _buildQueryOrderBy()
	{
		global $mainframe, $option;

		// obtengo el campo por el cual se desean ordenar los registros de la consulta y la direcci�n de ordenamiento
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order',		'filter_order',		'ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'ASC',			'word');

		if ($filter_order == 'ordering'){
			$orderby 	= ' ORDER BY ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , ordering ';
		}
		
		// kcho, lo anterior no anda porque comenté el ordenamiento en el html
		$orderby 	= ' ORDER BY year DESC';

		return $orderby;
	}

	/**
	 * Devuelve la consulta
	 *
	 * @access	private
	 * @return	string	La consulta que se utiliza para recuperar los BibTeXs incluidos en una categor�a dada
	 */
	function _buildQuery()
	{
		//  obtengo la clausulas WHERE y ORDER BY de la consulta
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildQueryOrderBy();
		
		if(is_array($this->_id)) {
			$condition = 'catid IN (' . implode( ',', $this->_id ) . ')';

		} else {
			$condition = 'catid = '.(int)$this->_id;
		}

		// genero la consulta para recuperar los BibTeXs
		$consulta = 'SELECT *' .
					' FROM #__bibdb_bibtex' .
					' WHERE ' . $condition . ' AND ' .
					$where .
					$orderby;

		return $consulta;
	}

	/**
	 * Devuelve la consulta para múltiples categorías
	 *
	 * @access	private
	 * @return	string	La consulta que se utiliza para recuperar los BibTeXs incluidos en una categor�a dada
	 */
	function _buildMultiQuery($cat)
	{
		//  obtengo la clausulas WHERE y ORDER BY de la consulta
		$where = $this->_buildContentWhere();
		$orderby = $this->_buildQueryOrderBy();

		// genero la consulta para recuperar los BibTeXs
		$consulta = 'SELECT *' .
					' FROM #__bibdb_bibtex' .
					' WHERE catid = '.(int)$cat . ' AND ' .
					$where .
					$orderby;

		return $consulta;
	}


	/**
	 * M�todo para cargar y devolver los BibTeXs de una categor�a
	 *
	 * @access	public
	 * @return	array	Arreglo de objetos
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			if(is_array($this->_id)) {
				$this->_data = array();
				foreach ($this->_id as $id_cat){
					$query = $this->_buildMultiQuery($id_cat);
//					echo '<br>Multi:'.$query.'<br>\n';
					$this->_data[] = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
				}
			} else {
				$query = $this->_buildMultiQuery($this->_id);
//				echo '<br>No Multi:'.$query.'<br>\n';
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			}
		}
		//print_r($this->_data);
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
			// preparo los valores de paginaci�n
			$total		= $this->getTotal(); // nro total de items que se desean paginar
			$limitstart	= $this->getState('limitstart'); // item con el cual comienza la p�gina
			$limit		= $this->getState('limit'); // numero m�ximo de items por p�gina
      
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