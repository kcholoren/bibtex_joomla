<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Agosto de 2009
 * Utilidad: controlador
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'PARSEENTRIES.php' );
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'common.php' );

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.application.component.helper' );
jimport( 'joomla.filesystem.file' );

class BibdbControllerBibTeX extends JController
{
	/**
	 * constructor (registrar tareas a métodos)
	 *
	 * @return	void
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask ( 'save', 'save' );
	}

	/**
	 * verificar las condiciones necesarias para poder subir un archivo
	 *
	 * @access	private
	 * @param	array	$file	La información del archivo que se intenta subir
	 * @param	string	$err	Almacena la información detallada del error producido
	 * @return	boolean			Verdadero en caso de que pueda subirse el archivo
	 */
	function _canUpload( $file, &$err )
	{
		// recupero los parámetros de configuración del componente
		$config =& JComponentHelper::getParams('com_bibdb');

		if(empty($file['name'])) {
			$err = JText::_('WARNEMPTYNAME');
			return false;
		}

		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$err = JText::_('WARNFILENAME');
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));
		$allowable = explode( ',', $config->get( 'upload_extensions', 'doc,pdf,txt,DOC,PDF,TXT' ) );
		if (!in_array($format, $allowable)) {
			$err = JText::_('WARNFILETYPE');
			return false;
		}

		$maxSize = (int) $config->get( 'upload_maxsize', 0 );
		if ($maxSize > 0 && (int) $file['size'] > $maxSize) {
			$err = JText::_('WARNFILETOOLARGE');
			return false;
		}

		// puede efectuarse la subida
		return true;
	}

	/**
	 * Subir un archivo
	 *
	 * @access	private
	 * @param	string	$err		Almacena la información detallada del error producido
	 * @param	string	$filename	Nombre del archivo en el servidor
	 * @return	boolean				Verdadero en caso de que se haya subido con éxito el archivo
	 */
	function _upload( &$err, &$filename )
	{
		// obtengo el contenido del archivo
		$file = JRequest::getVar( 'jformuploadedfile', '', 'files', 'array' );

		// recupero los parámetros de configuración del componente
		$config =& JComponentHelper::getParams('com_bibdb');
		$dir_upload = $config->get('upload_folder', 'docs');

		if ((!empty($file)) && (!$file['error'])) {
			// vuelvo seguro el nombre del archivo
			$file['name'] = JFile::makeSafe($file['name']);

			// chequeo que pueda subirse el archivo
			if (!$this->_canUpload($file, $err)) {
				return false;
			}

			// puede subirse el archivo, a continuación verifico la existencia o no del mismo en el servidor
			$newname = JPath::clean(JPATH_SITE.DS.$dir_upload.DS.strtolower($file['name']));
			if (JFile::exists($newname)) {
				$err = JText::_('WARNFILEEXISTS');
				return false;
			}

			// finalmente intento mover el archivo temporal a su ubicación final
			if (!JFile::upload($file['tmp_name'], $newname)) {
				$err = JText::_('WARNJFILEUPLOAD');
				return false;
			}

			// no se ha producido ningún error
			$filename = strtolower($file['name']);
			return true;
		} else {
			$err = JText::_('WARNEMPTYNAME');
			return false;
		}
	} // fin de upload()

	/**
	 * Método encargado de recuperar cada una de las entradas bibliográficas reconocidas
	 * y mediante el modelo almacenarlas en la base de datos
	 *
	 * @access	private
	 * @return	int		Cantidad de referencias bibliográficas almacenadas
	 */
	function _guardarEntradas( &$entries1 )
	{
		// recupero del string query la categoría a la que se quieren agregar los BibTeXs
		$catid = JRequest::getInt( 'catid', 0, 'post' );
		// recupero del string query si el usuario desea que las entradas aparezcan como publicadas o no
		$publish = JRequest::getInt( 'published', 1, 'post' );
		// recupero del string query el orden dado al item dentro de la categoría
		$order = JRequest::getInt( 'ordering', 0, 'post' );

		// instancio el modelo bibtex
		$model = $this->getModel( 'bibtex' );

		// mantengo la cuenta de los BibTeXs insertados
		$bibtex_ins = 0;
		// a continuación almaceno los datos de una entrada BibTeX completa a la vez
		for( $i=0, $n=count( $entries1 ); $i<$n; $i++)
		{
			$fila =& $entries1[$i];
			// paso a minúsculas las claves del arreglo $entries1[$i]
			$fila = array_change_key_case( $fila, CASE_LOWER );
			// agrego el estado y la categoría a los datos del bibtex
			$fila['catid'] = $catid;
			$fila['published'] = $publish;
			$fila['ordering'] = $order;

			// almaceno el BibTeX usando el modelo
			if ( $model->store( $fila ) )
			{
				// se han podido insertar los datos del BibTeX
				$bibtex_ins += 1;
			}
		}

		// devuelvo la cantidad de BibTeX(s) insertados
		return $bibtex_ins;
	}

	/**
	 * 
	 *
	 * @access	public	
	 * @return	void	Almacena los datos correspondientes a un BibTeX junto con su documento electrónico
	 */
	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// recupero algunos datos remitidos por el usuario
		$bibtex_data	= JRequest::getVar( 'jformbibtexarea', '', 'post', 'string' );
		$subir			= JRequest::getInt( 'jformadjuntar', 1, 'post' );

		// redirección
		$link = 'index.php?option=com_bibdb&view=bibtex&layout=form';

		// chequeo que el área de texto con los datos del BibTeX no este vacía
		if( empty($bibtex_data) ) {
			$msg = JText::_( 'WARNEMPTYTEXTAREA' );
			$this->setRedirect( $link, $msg, 'notice' );
			return;
		}

		// recupero en $entries1 los datos de los BibTeXs como un arreglo de arreglos asociativos
		$parse = new PARSEENTRIES();
		$parse->expandMacro = TRUE;
		$parse->loadBibtexString($bibtex_data);
		$parse->extractEntries();
		list($preamble, $strings, $entries1, $undefinedStrings) = $parse->returnArrays();

		$raws =& _refCruda( $entries1 );
		for( $i=0, $n=count($raws); $i<$n; $i++ ) {
			$entries1[$i]['detalles'] = $raws[$i];
		}

		if( count( $entries1 ) >= 1 )
		{
			// al menos una entrada válida
			$filename = null;
			if( $subir && !$this->_upload( $msg, $filename ) ) {
				// se desea subir un archivo pero falla la operación
				$this->setRedirect( $link, $msg, 'notice' );
				return;
			}

			// a continuación inserto los datos del primer BibTeX, el resto los ignoro
			$entries1 = array_slice( $entries1, 0, 1, TRUE );
			// agrego el path de la publicación que corresponde al BibTeX
			$entries1[0]['path'] = $filename;

			$bibtex_ins = $this->_guardarEntradas( $entries1 );

			$msg = JText::sprintf( 'ENT_ALMACENADAS', $bibtex_ins );
			$this->setRedirect( $link, $msg );
			return;
		}
		else {
			$msg = JText::_( 'WARNSINTAXIS' );
			$this->setRedirect( $link, $msg, 'notice' );
			return;
		}
	}

	/**
	 * Cierra el form y muestra la lista de entradas bibliográficas
	 *
	 * @access	public
	 * @return	void
	 */
	function cancel()
	{
		// chequeo de seguridad, pedidos falsificados
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_bibdb&view=all&layout=form' );
	}
}
