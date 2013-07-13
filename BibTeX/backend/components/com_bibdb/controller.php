<?php
/**
 * PROYECTO FINAL DE CARRERA
 *
 * Programador: Pablo E. DALPONTE
 * Fecha: Junio de 2009
 * Utilidad: controlador por defecto para el gestor de BibTeXs
 * licencia: GNU/GPL
 */

// chequeo de seguridad
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class BibdbController extends JController
{

  /**
   * constructor (registrar tareas a métodos)
   *
   * @return void
   */
  function __construct($config = array())
  {
    parent::__construct($config);

    // registro la tarea unpublish al método publish()
    $this->registerTask( 'unpublish', 'publish' );
  }

  /**
   * display() renderiza todos los bibtex en la base de datos
   *
   * @access	public
   * @return	void
   */
  function display()
  {
    JRequest::setVar('view', JRequest::getVar('view', 'all'));

    parent::display();
  }

  /**
   * Método invocado cuando se desea obtener la información detallada de un BibTeX
   *
   * @access	public
   * @return	void
   */
  function edit()
  {
    JRequest::setVar( 'view', 'bibtex' );
    JRequest::setVar( 'layout', 'form' );
    JRequest::setVar( 'hidemainmenu', 1 );
    
    parent::display();
  }

  /**
   * Método para publicar o despublicar una o más entradas BibTeX
   *
   * @access	public
   * @return	void
   */
  function publish()
  {
    // chequeo de seguridad, pedidos falsificados
    JRequest::checkToken() or jexit( 'Invalid Token' );

    $publish = ($this->getTask() == 'publish') ? 1 : 0;
    
    $model = $this->getModel( 'bibtex' );
    $model->publish( $publish );

    $this->setRedirect( 'index.php?option=com_bibdb' );
  }

  /**
   * Eliminar entrada(s) de la tabla #__bibdb_bibtex
   *
   * @access	public
   * @return	void
   */
  function remove()
  {
    // chequeo de seguridad, pedidos falsificados
    JRequest::checkToken() or jexit( 'Invalid Token' );

    $model = $this->getModel('bibtex');
    if (!$model->delete()) {
      $msg = JText::_( 'ERROR_ELIM_BIBTEX' );
    } else {
      $msg = JText::_( 'EXITO_ELIM_BIBTEX' );
    }

    $this->setRedirect( 'index.php?option=com_bibdb', $msg );
  }

  /**
   * Subir el registro un paso dentro de la categoria
   *
   * @access	public
   * @return	void
   */
  function orderup()
  {
    // chequeo de seguridad, pedidos falsificados
    JRequest::checkToken() or jexit( 'Invalid Token' );

    $model = $this->getModel('bibtex');
    $model->move(-1);

    $this->setRedirect( 'index.php?option=com_bibdb' );
  }

  /**
   * Bajar el registro un paso dentro de la categoria
   *
   * @access	public
   * @return	void
   */
  function orderdown()
  {
    // chequeo de seguridad, pedidos falsificados
    JRequest::checkToken() or jexit( 'Invalid Token' );

    $model = $this->getModel('bibtex');
    $model->move(1);

    $this->setRedirect( 'index.php?option=com_bibdb' );
  }

  /**
   * Guardar el nuevo orden de los items categorizados
   *
   * @access	public
   * @return	void
   */
  function saveorder()
  {
  
    // Check for request forgeries
    JRequest::checkToken() or jexit( 'Invalid Token' );

    $cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
    $order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
    JArrayHelper::toInteger($cid);
    JArrayHelper::toInteger($order);

    $model = $this->getModel( 'bibtex' );
    if( $model->saveorder($cid, $order) )
	{
		$msg = JText::_( 'Se ha guardado el nuevo orden.' );
	}
	else
	{
		$msg = JText::_( 'Error al guardar el nuevo orden: ' . $model->getError() );
	}

	$this->setRedirect( 'index.php?option=com_bibdb', $msg );
  }

}