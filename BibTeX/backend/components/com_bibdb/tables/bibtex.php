<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: Objeto que representa la tabla #__bibdb_bibtex
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * clase Tabla BibTeX
 *
 */
class TableBibtex extends JTable
{
	/**
	 * Clave primaria
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * Clave de la categoria
	 *
	 * @var int
	 */
	var $catid = null;

	/**
	 * @var string
	 */
	var $bibtexentrytype = null;
	
	/**
	 * @var string
	 */
	var $bibtexcitation = null;
	
	/**
	 * @var string
	 */
	var $title = null;
	
	/**
	 * @var int
	 */
	var $year = null;

	/**
	 * @var string
	 */
	var $month = null;

	/**
	 * @var string
	 */
	var $note = null;

	/**
	 * @var string
	 */
	var $keywords = null;

	/**
	 * @var string
	 */
	var $abstract = null;

	/**
	 * @var string
	 */
	var $language = null;

	/**
	 * @var string
	 */
	var $isbn = null;

	/**
	 * @var string
	 */
	var $url = null;

	/**
	 * @var string
	 */
	var $contents = null;

	/**
	 * @var string
	 */
	var $series = null;

	/**
	 * @var string
	 */
	var $institution = null;

	/**
	 * @var string
	 */
	var $organization = null;

	/**
	 * @var string
	 */
	var $school = null;

	/**
	 * @var string
	 */
	var $address = null;

	/**
	 * @var string
	 */
	var $journal = null;

	/**
	 * @var string
	 */
	var $volume = null;

	/**
	 * @var string
	 */
	var $number = null;

	/**
	 * @var string
	 */
	var $pages = null;

	/**
	 * @var string
	 */
	var $chapter = null;

	/**
	 * @var string
	 */
	var $issn = null;

	/**
	 * @var string
	 */
	var $author = null;

	/**
	 * @var string
	 */
	var $affiliation = null;

	/**
	 * @var string
	 */
	var $editor = null;

	/**
	 * @var string
	 */
	var $publisher = null;

	/**
	 * @var string
	 */
	var $edition = null;

	/**
	 * @var string
	 */
	var $howpublished = null;

	/**
	 * @var string
	 */
	var $booktitle = null;

	/**
	 * @var string
	 */
	var $annote = null;

	/**
	 * @var string
	 */
	var $detalles = null;

	/**
	 * @var string
	 */
	var $path = null;

	/**
	 * @var string
	 */
	var $path_extra = null;

	
	/**
	 *
	 * @var datetime
	 */
	var $fechaalta = null;

	/**
	 *
	 * @var int
	 */
	var $ordering = null;

	/**
	 * @var int
	 */
	var $published = null;

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableBibtex(& $db) {
		parent::__construct('#__bibdb_bibtex', 'id', $db);
	}
	
	/**
	 * Método check() [redefinido]. Chequear integridad de los datos.
	 *
	 * @access public
	 * @return boolean Verdadero en caso de exito
	 */
	function check()
	{
		if (trim($this->bibtexentrytype) == '') {
			$this->setError(JText::_('El tipo de la referencia no puede ser vacio.'));
			return false;
		}
		
		if (trim($this->title) == '') {
			$this->setError(JText::_('La referencia BibTeX debe poseer un titulo.'));
			return false;
		}

		// en caso de existir una entrada del mismo tipo y titulo que la que intento insertar
		// devuelvo falso.
		$query = 'SELECT id FROM #__bibdb_bibtex WHERE bibtexentrytype = '.$this->_db->Quote($this->bibtexentrytype).' AND title = '.$this->_db->Quote($this->title);
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::_('La referencia BibTeX ya existe'));
			return false;
		}

		return true;
	}	

}