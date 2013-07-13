<?php defined('_JEXEC') or die('Restricted access'); ?>

<div><a href="index.php?option=com_bibdb&amp;view=uploadfile&amp;tmpl=component&amp;folder=<?php 
	$insert =  $this->typefile;
	echo $this->_tmp_folder->path_with_name_relative_no; 
	echo "&amp;file=".$insert;
?>"><?php
	
	echo JHTML::_( 'image.administrator', 'components/com_bibdb/assets/images/icon-folder.png', '','', '', JText::_('Abrir'), 'title="'.JText::_('Abrir').'"'); 
?></a> <a href="index.php?option=com_bibdb&amp;view=uploadfile&amp;tmpl=component&amp;folder=<?php 
	echo $this->_tmp_folder->path_with_name_relative_no;
	echo "&amp;file=".$insert;
?>"><?php 
	echo $this->_tmp_folder->name;
?></a></div>
