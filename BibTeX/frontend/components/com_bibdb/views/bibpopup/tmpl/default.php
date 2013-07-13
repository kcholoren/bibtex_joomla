<?php defined('_JEXEC') or die('Restricted access'); ?>

<h1><i><?php echo JText::_( 'LEGEND_DETALLES' ); ?></i></h1>&nbsp;
<!-- las etiquetas <pre> y </pre> permiten insertar texto preformateado -->
<pre><?php echo $this->escape($this->bibtex->detalles); ?></pre>