<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: controlar la carga de referencias bibliográficas. Se hace uso de la librería
 *			 PARSEENTRIES.php para procesar los BibTeXs.
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

// importar PARSEENTRIES.php
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'PARSEENTRIES.php' );

jimport( 'joomla.application.component.controller' );
jimport( 'joomla.filesystem.file' );

/**
 * Controlador inputbib del componente bibdb
 *
 */
class BibdbControllerInputbib extends JController
{

	/**
	 * Constructor del controlador.
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		// tarea por defecto en caso de que no se especifique ninguna
		$this->registerDefaultTask( 'display' );

		// tareas usadas al cargar un conjunto de entradas BibTeX
		$this->registerTask( 'savestr',		'saveString' );
		$this->registerTask( 'savefile',	'saveFile' );

		// tareas usadas al actualizar una entrada BibTeX específica
		$this->registerTask( 'save',		'update' );
		$this->registerTask( 'apply',		'update' );
	}

	/**
	 * Muestra la vista inpubib usando el layout identificado como default.
	 *
	 * @access	public
	 * @return	void
	 */
	function display()
	{
		JRequest::setVar( 'view',	JRequest::getVar( 'view',	'inputbib' ) );
		JRequest::setVar( 'layout',	JRequest::getVar( 'layout',	'default' ) );

		parent::display();
	}

	/**
	 * Permite mostrar el formulario adecuado al método elegido por el usuario para insertar
	 * nuevas publicaciones. Las dos opciones posibles son: hacer uso de un archivo de
	 * extensión bib o mediante la copia del BibTeX como un string.
	 *
	 * @access	public
	 * @return	void
	 */
	function selectForm()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// muestro el formulario que corresponde al tipo de entrada elegido
		$selOption = JRequest::getWord( 'inputList', 'formstr', 'post' );

		JRequest::setVar( 'view',	'inputbib' );
		JRequest::setVar( 'layout',	$selOption );

		parent::display();
	}

	/**
	 * Recorrer cada entrada del arreglo dado como parámetro y generar otro arreglo en el 
	 * que cada componente representa un BibTeX sin mayor procesamiento (no se separa en campos)
	 *
	 * @access	private
	 * @param	array	$entries	Lista de arreglos asociativos que representa todos los BibTeXs reconocidos a partir de los datos ingresados por el usuario.
	 * @return	array				Lista de BibTeXs, cuyos datos no estan separados por campo.
	 */
	function &_refCruda( &$entries )
	{
		$res = array();
		for($i=0, $j=count($entries); $i<$j; $i++)
		{
			$raw = '@';
			$fila =& $entries[$i];
			// array asociativo, armo la entrada del BibTeX
			foreach( $fila as $campo => $valor) {
				switch( $campo ) {
					case 'bibtexEntryType':
						$raw .= "$valor{";
						break;
					case 'bibtexCitation':
						$raw .= "$valor,\n";
						break;
					default :
						$raw .= "\t$campo = \"$valor\",\n";
				}
				// replace non HTML characters
				$entries[$i][$campo] = $this->replaceSpecials( $valor );;
			}
			$raw .= "}";
			$res[] = $raw;
		}
		
		return $res;
	}
	
	// replace some special LateX characters into HTML characters
	// by Kcho'2010
	// It replaces \~, \', \", \& and {word} only
	function replaceSpecials( $string ){
		/************************************  &			\i			eñe									*/
		$special_characters = array("/\\\&/",	"/\\\i/",	"/\\\~{(.)}/",	"/\\\~(.)/",	
								/*	dos puntos							acentos						*/
									"/\\\"{(.)}/",	"/\\\"(.)/",	"/\\\'{(.)}/",	"/\\\'(.)/",
								/*	llaves				\cualquierletra	acentos en español por si están mal escritos en el Bib*/	
									"/{/",	"/}/",	"/\\\(.)/",	"/á/",	"/é/",	"/í/",	"/ó/",	"/ú/",
								/* grado*/
									"/textdegree/");
		$replacements =		  array("&amp;",	"i",		"&$1tilde;",	"&$1tilde;",
									"&$1uml;",		"&$1uml;",		"&$1acute;",	"&$1acute;",	
									"",		"",		"$1", "&aacute;",	"&eacute;",	"&iacute;",	"&oacute;",	"&uacute;",
									"&ordm;");

		return preg_replace($special_characters , $replacements, $string);
	}
	
	/**
	 * Método que permite almacenar todas las referencias bibliográficas reconocidas haciendo uso del modelo BibTeX
	 *
	 * @access	private
	 * @param	array	$entries1	Lista de arreglos asociativos que representa todos los BibTeXs reconocidos a partir de los datos ingresados por el usuario.
	 * @return	int					Cantidad de referencias bibliográficas insertadas con éxito.
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
		// a continuacion almaceno los datos de una entrada BibTeX completa a la vez
		for( $i=0, $n=count( $entries1 ); $i<$n; $i++)
		{
			$fila =& $entries1[$i];
			// paso a minúsculas las claves del arreglo $entries1[$i]			
			$fila = array_change_key_case( $fila, CASE_LOWER );
			// agrego el estado, la categoría y el orden a los datos del bibtex
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
	 * Este método se utiliza para determinar si un archivo dado tiene extensión bib
	 *
	 * @access	private
	 * @param	string	$fileName	El nombre del archivo.
 	 * @return	boolean				Verdadero en caso de que el archivo sea de extensión bib.
	 */
	function _esBibtex( $fileName )
	{
		// obtengo la extensión del archivo
		$ext = strtolower( JFile::getExt($fileName) );

		return ( $ext == 'bib' );
	}

	/**
	 * Este método hace uso de la librería PARSEENTRIES.php para parsear los datos ingresados
	 * por el usuario a partir de un archivo .bib
	 *
	 * @access	public
	 * @return	void
	 */
	function saveFile()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// obtengo el contenido del archivo
		$file = JRequest::getVar( 'uploaded_file', '', 'files', 'array' );
		// recupero la dirección de retorno
		$return_url = JRequest::getVar( 'return-url', null, 'post', 'base64' );

		if( (!empty($file)) && (!$file['error']) )
		{
			// chequeo que sea un archivo de extensión .bib
			if (!$this->_esBibtex($file['name']))
			{
				// no se trata de un archivo de extension bib
				$msg = JText::_( 'WARNFILETYPE' );
				$this->setRedirect( base64_decode($return_url), $msg, 'notice' );
				return;
			}

			// chequeo que el tamaño del archivo sea menor a 1 MB
			$maxSize = 1000000;
			if ( (int)$file['size'] > $maxSize )
			{
				$msg = JText::_( 'WARNFILETOOLARGE' );
				$this->setRedirect( base64_decode($return_url), $msg, 'notice' );
				return;
			}

			// recupero en $entries1 los datos de los BibTeXs como un arreglo de arreglos asociativos
			$parse = new PARSEENTRIES();
			$parse->expandMacro = TRUE;
			$parse->openBib($file['tmp_name']);
			$parse->extractEntries();
			$parse->closeBib();
			list($preamble, $strings, $entries1, $undefinedStrings) = $parse->returnArrays();

			$raws =& $this->_refCruda( $entries1 );

			for( $i=0, $n=count($raws); $i<$n; $i++ ) {
				$entries1[$i]['detalles'] = $raws[$i];
			}

			$bibtex_ins = $this->_guardarEntradas( $entries1 );

			$msg = JText::sprintf( 'ENT_ALMACENADAS', $bibtex_ins );
			$this->setRedirect( base64_decode($return_url), $msg );
			return;
		}
		else
		{
			$this->setRedirect( base64_decode($return_url) );
			return;
		}
	} // fin de saveFile()

	/**
	 * Este método hace uso de la librería PARSEENTRIES.php para parsear los datos ingresados
	 * por el usuario a partir de un string
	 *
	 * @access	public
	 * @return	void
	 */
	function saveString()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// recupero el string ingresado por el usuario
		$bibtex_data	= JRequest::getVar( 'textstrbib', '', 'post', 'string' );
		// recupero la dirección de retorno
		$return_url		= JRequest::getVar( 'return-url', null, 'post', 'base64' );
		
		if( empty($bibtex_data) ) {
			$msg = JText::_( 'WARNEMPTYTEXTAREA' );
			$this->setRedirect( base64_decode($return_url), $msg, 'notice' );
			return;
		}

		// recupero en $entries1 los datos de los BibTeXs como un arreglo de arreglos asociativos
		$parse = new PARSEENTRIES();
		$parse->expandMacro = TRUE;
		$parse->loadBibtexString($bibtex_data);
		$parse->extractEntries();
		list($preamble, $strings, $entries1, $undefinedStrings) = $parse->returnArrays();

		$raws =& $this->_refCruda( $entries1 );
		for( $i=0, $n=count($raws); $i<$n; $i++ ) {
			$entries1[$i]['detalles'] = $raws[$i];
		}

		$bibtex_ins = $this->_guardarEntradas( $entries1 );

		$msg = JText::sprintf( 'ENT_ALMACENADAS', $bibtex_ins );
		$this->setRedirect( base64_decode($return_url), $msg );
	}

	/**
	 * Actualizar los datos de una referencia bibliográfica dada.
	 *
	 * @access	public
	 * @return	void
	 */
	function update()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$bibtex_data	= JRequest::getVar( 'detalles-bibtex', '', 'post', 'string' );
		$file		= JRequest::getVar( 'filename', '', 'post', 'string' );
		$file_extra	= JRequest::getVar( 'filename_extra', '', 'post', 'string' );
		$taskName	= $this->getTask();

		// redirección, en función de la tarea elegida
		switch ($taskName)
		{
			case 'apply':
				$link = 'index.php?option=com_bibdb&task=edit&cid[]=' . (int)$cid[0];
				break;

			case 'save':
				$link = 'index.php?option=com_bibdb';
				break;
		}

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

		$raws =& $this->_refCruda( $entries1 );
		for( $i=0, $n=count($raws); $i<$n; $i++ ) {
			$entries1[$i]['detalles'] = $raws[$i];
		}

		if( count( $entries1 ) >= 1 )
		{
			// al menos una entrada válida, inserto la primera de ellas, las demás las ignoro
			$entries1 = array_slice( $entries1, 0, 1, TRUE );
			// agrego el path de la publicación que corresponde al BibTeX
			if( !empty($file) ) { $entries1[0]['path'] = $file; }
			if( !empty($file_extra) ) { $entries1[0]['path_extra'] = $file_extra; }

			$bibtex_ins = $this->_guardarEntradas( $entries1 );

			$msg = JText::sprintf( 'ENT_ALMACENADAS', $bibtex_ins );
			$this->setRedirect( $link, $msg );
			return;
		}
		else
		{
			$msg = JText::_( 'WARNSINTAXIS' );
			$this->setRedirect( $link, $msg, 'notice' );
			return;
		}
	}

	/**
	 * Cierra el form de edición y muestra la lista de entradas bibliográficas
	 *
	 * @access	public
	 * @return	void
	 */
	function cancel()
	{
		// chequeo de seguridad, pedidos falsificados
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$this->setRedirect( 'index.php?option=com_bibdb' );
	}
}