<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: modelo bibtex para el front-end del componente bibdb
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
	 * Objeto con los datos del BibTeX
	 *
	 * @var object
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

		$tablepath = JPATH_COMPONENT_ADMINISTRATOR.DS.'tables';
		$this->addTablePath( $tablepath );

		$id_b = JRequest::getVar('id', 0, '', 'int');
		$this->setId($id_b);
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
			// cargo la primer fila de la consulta dentro de un objeto
			$this->_data = $this->_db->loadObject();
		}

		if( (!$this->_data) || (!$this->_data->cat_pub) || (!$this->_data->published) ) {
			// no se ha encontrado el BibTeX requerido o no está publicado o su categoría no esta publicada
			return JError::raiseNotice( 404, JText::_("BibTeX no encontrado") );
		}
		
		// verifico que el usuario tenga los permisos adecuados para ver los detalles del bibtex
		$user =& JFactory::getUser();
		if( $this->_data->cat_access > $user->get('aid', 0) )
		{
			return JError::raiseError( 403, JText::_('ALERTNOTAUTH') );
		}

		// devuelvo los datos del bibtex
		return $this->_data;
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
}