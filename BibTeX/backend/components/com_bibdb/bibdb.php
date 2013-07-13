<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: punto de acceso a la parte de administración del componente bibdb
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

/*
 * permisos
 */
$acl =& JFactory::getACL();
$acl->_mos_add_acl( 'com_bibdb', 'manage', 'users', 'super administrator');

/*
 * me aseguro que el usuario esté autorizado a ver esta página
 */
$user =& JFactory::getUser();
if (!$user->authorize( 'com_bibdb', 'manage' )) {
	$mainframe->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
}

// incluir el controlador base
require_once( JPATH_COMPONENT.DS.'controller.php' );

// incluir cualquier otro controlador solicitado
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// crear el controlador
$classname	= 'BibdbController'.$controller;
$controller	= new $classname( );

// ejecutar la tarea solicitada
$controller->execute( JRequest::getVar( 'task' ) );

// redirigir la salida
$controller->redirect();