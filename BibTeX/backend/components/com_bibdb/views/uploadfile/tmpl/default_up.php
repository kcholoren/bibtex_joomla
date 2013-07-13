<?php defined('_JEXEC') or die('Restricted access'); 
if (isset($this->state->folder) && ($this->state->folder != '')) {
?><div><a style="text-decoration:none" alt=".." href="index.php?option=com_bibdb&amp;view=uploadfile&amp;tmpl=component&amp;folder=<?php echo $this->state->parent; ?>" ><?php
	echo JHTML::_( 'image.administrator', 'components/com_bibdb/assets/images/icon-up.png','','', '', JText::_('Up'), 'title="'.JText::_('Up').'"');
?> ..</a>&nbsp;[<?
	echo $this->state->folder;
?>]</div><?
} // if added by Kcho
?>