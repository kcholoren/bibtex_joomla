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
			$entries[$i][$campo] = replaceSpecials( $valor );;
		}
		$raw .= "}";
		$res[] = $raw;
	}
	
	return $res;
}


?>