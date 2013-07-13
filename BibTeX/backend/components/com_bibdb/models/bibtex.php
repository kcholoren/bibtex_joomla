<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: modelo bibtex del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 * Modelo bibtex
 *
 */
class BibdbModelBibtex extends JModel
{
	/**
	 * ID del BibTeX
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Arreglo asociativo con los datos de un BibTeX
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor que recupera el ID a partir de la entrada BibTeX
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Método utilizado para almacenar el ID dado en el modelo
	 *
	 * @access	public
	 * @param	int Identificador del BibTeX
	 * @return	void
	 */
	function setId($id)
	{
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 * Devuelve un arreglo asociativo que representa un BibTeX vacío
	 *
	 * @access	public
	 * @return	array
	 */
	function &getBibtexVacio()
	{
		$bibtex = array( 'id' => 0,
						 'catid' => 0,
						 'bibtexentrytype' => null,
						 'bibtexcitation' => null,
						 'title' => null,
						 'year' => null,
						 'month' => null,
						 'note' => null,
						 'keywords' => null,
						 'abstract' => null,
						 'language' => null,
						 'isbn' => null,
						 'url' => null,
						 'contents' => null,
						 'series' => null,
						 'institution' => null,
						 'organization' => null,
						 'school' => null,
						 'address' => null,
						 'journal' => null,
						 'volume' => null,
						 'number' => null,
						 'pages' => null,
						 'chapter' => null,
						 'issn' => null,
						 'author' => null,
						 'affiliation' => null,
						 'editor' => null,
						 'publisher' => null,
						 'edition' => null,
						 'howpublished' => null,
						 'booktitle' => null,
						 'annote' => null,
						 'detalles' => null,
						 'path' => null,
						 'path_extra' => null,
						 'fechaalta' => null,
						 'ordering' => 0,
						 'published' => 1,
						);

		return $bibtex;
	}

	/**
	 * Devuelve la consulta que permite recuperar los datos correspondientes a un BibTeX
	 *
	 * @return string La consulta para recuperar los datos de un BibTeX
	 */
	function _buildQuery()
	{
		$consulta = ' SELECT b.*, cc.title AS category, cc.published AS cat_pub, cc.access AS cat_access' .
					' FROM #__bibdb_bibtex AS b LEFT JOIN #__categories AS cc ON cc.id = b.catid' .
					' WHERE b.id = ' . (int) $this->_id;

		return $consulta;
	}

	/**
	 * Método para obtener los datos correspondientes a un BibTeX
	 *
	 * @return array with data
	 */
	function &getData()
	{
		// cargo los datos si aún no han sido cargados
		if(empty($this->_data)) {
			$query = $this->_buildQuery();
			$this->_db->setQuery($query);
			// loadAssoc() devuelve un arreglo de la forma Array ( [id] => 1, [bibtexEntryType] => article, ... )
			// que representa una fila de la tabla #__bibdb_bibtex
			$this->_data = $this->_db->loadAssoc();
		}

		if(!$this->_data) {
			// $this->_data es null
			return JError::raiseNotice(404, JText::_("BibTeX no encontrado"));
		}

		// verifico que el usuario tenga los permisos adecuados para ver los detalles del bibtex
		$user =& JFactory::getUser();
		if( array_key_exists("cat_access", $this->_data) && ($this->_data['cat_access'] > $user->get('aid', 0)) )
		{
			return JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
		}

		// devuelvo los datos del bibtex
		return $this->_data;
	}

	/**
	 * Método para borrar una o más entradas BibTeX de la base de datos
	 *
	 * @access	public
	 * @return	boolean	Verdadero en caso de que puedan borrarse todos los registros requeridos
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getError() );
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Método para publicar una o mas entradas BibTeX
	 *
	 * @access	public
	 * @return	void
	 */
	function publish($publish)
	{
		$cids = JRequest::getVar( 'cid', array(), 'post', 'array' );

		$row =& $this->getTable();
		$user =& JFactory::getUser();

		if (count( $cids )) {
			$row->publish( $cids, $publish, $user->get('id') );
		}
	}

	/**
	 * Método para almacenar los datos correspondientes a un BibTeX
	 *
	 * @access public
	 * @return boolean Verdadero en caso de que puedan insertarse los datos correctamente
	 */
	function store( &$entradaBib )
	{
		$row =& $this->getTable();
		$row->reset();
		$row->set('id', $this->_id);

		// Bind
		if (!$row->bind($entradaBib)) {
			$this->setError( $row->getError() );
			return false;
		}

		// seteo la fecha de creación del bibtex
		$row->set('fechaalta', gmdate('Y-m-d H:i:s'));

		if ( !$row->id ) {
			// ubicar el nuevo bibtex en la última posición, en la categoría apropiada
			$where = 'catid = ' . (int) $row->catid ;
			$row->ordering = $row->getNextOrder( $where );
		}

		// Check
		if (!$row->check()) {
			$this->setError( $row->getError() );
			return false;
		}

		// Store, fuerzo la actualización de los valores nulos
		if (!$row->store(true)) {
			$this->setError( $row->getError() );
			return false;
		}

		return true;
	}

	/**
	 * Método para mover un bibtex
	 *
	 * @access	public
	 * @param	int $direction	Indica la dirección de movimiento
	 * @return	boolean			Verdadero en caso de éxito
	 */
	function move($direction)
	{
		$row =& $this->getTable();

		// cargo el registro correspondiente al bibtex con ID $this->_id dentro del buffer $row
		if (!$row->load($this->_id)) {
			$this->setError( $row->getError() );
			return false;
		}

		// muevo el registro un paso arriba o abajo, según la dirección dada, intercambiando
		// valores del campo ordering entre registros vecinos
		// void move ($dirn, [$where = ]) El where establece condiciones adicionales para el ordenamiento
		$row->move( $direction, ' catid = '.(int) $row->catid.' AND published >= 0 ' );

		return true;
	}

	/**
	 * Method para reacomodar el orden de los items categorizados
	 *
	 * @access	public
	 * @return	boolean	Verdadero en caso de éxito
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& $this->getTable();
		$groupings = array();

		// actualizar los valores de ordering
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->catid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError( $row->getError() );
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('catid = '.(int) $group);
		}

		return true;
	}

}