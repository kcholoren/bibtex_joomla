<?php defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

// print($this->typefile);die();
$insert =  $this->typefile;
$ext = strtolower( substr( strrchr( $this->_tmp_file->path_without_name_relative, "." ), 1 ) );
?>
<div><a href="#" onclick="window.top.document.forms.adminForm.elements.<? 
	echo $insert;
?>.value='<? 
	echo $this->_tmp_file->path_with_name_relative_no;
?>';window.parent.document.getElementById('sbox-window').close();"><?
	echo JHTML::_( 'image.administrator', 'components/com_bibdb/assets/images/icon-file.png', '','', '', JText::_('Insertar'), 'title="'.JText::_('Insertar').'"');
?></a><a href="#" onclick="window.top.document.forms.adminForm.elements.<? echo $insert;?>.value='<?
	echo $this->_tmp_file->path_with_name_relative_no;
?>';window.parent.document.getElementById('sbox-window').close();"><?
	echo $this->_tmp_file->name;
?></a></div>