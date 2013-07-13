<?php
defined('_JEXEC') or die('Restricted access');

$currentFolder = '';
if (isset($this->state->folder) && ($this->state->folder != '')) {
	$currentFolder = $this->state->folder;
}
?><table><tbody><tr><td width="50%">
<form action="<?php echo JURI::base(); ?>index.php?option=com_bibdb&controller=file&amp;task=upload&amp;tmpl=component&amp;<?php echo JUtility::getToken();?>=1&amp;viewback=bibdbmanager:file&amp;folder=<?php echo $currentFolder?>" name="uploadForm" id="uploadForm" method="post" enctype="multipart/form-data">
<!-- Form Subir Archivo -->
	<fieldset>
		<legend><?php echo JText::sprintf( 'LEGEND_SUBIR', $this->uploadmaxsize / 1000000 ); ?></legend>
		<fieldset class="actions">
			<input type="file" id="file-upload" name="Filedata" size="40"/>
			<input type="submit" id="file-upload-submit" value="<?php echo JText::_('VALUE_BTN_UPLOAD'); ?>"/>
			<span id="upload-clear"></span>
		</fieldset>
		<ul class="upload-queue" id="upload-queue">
			<li style="display: none" ></li>
		</ul>
	</fieldset>
	<input type="hidden" name="return-url" value="<?php
		echo base64_encode('index.php?option=com_bibdb&view=uploadfile&tmpl=component&file='.$this->typefile); 
	?>" />
</form>
</td><td width="50%" valign="top">
<form action="<?php echo JURI::base(); ?>index.php?option=com_bibdb&controller=file&amp;task=createfolder&amp;<?php echo JUtility::getToken();?>=1&amp;viewback=bibdbmanager:file&amp;folder=<?php echo $currentFolder?>" name="folderForm" id="folderForm" method="post">
<!-- Form Crear Carpeta -->
	<fieldset id="folderview">
		<legend><?php echo JText::_( 'Folder' ); ?></legend>
		<div class="path">
			<input type="text" class="inputbox" id="foldername" name="foldername" size="40" />
			<input type="hidden" class="update-folder" name="folderbase" id="folderbase" value="<?php echo $currentFolder; ?>" />
			<button type="submit"><?php echo JText::_( 'VALUE_BTN_FOLDER' ); ?></button>
		</div>
	</fieldset>
	<input type="hidden" name="return-url" value="<?php
		echo base64_encode('index.php?option=com_bibdb&view=uploadfile&tmpl=component&file='.$this->typefile); 
	?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</td></tr></tbody></table>
<div style="border-bottom:1px solid #cccccc;margin-bottom: 10px">&nbsp;</div>
<?
echo $this->loadTemplate('up');
if (count($this->folders) > 0 ) {
	echo '<div>';
	for ($i=0,$n=count($this->folders); $i<$n; $i++) {
		$this->setFolder($i);
		echo $this->loadTemplate('folder');
	}
	echo '</div>';
?><div style="border-bottom:1px solid #cccccc;margin-bottom: 10px">&nbsp;</div><?
}

if (count($this->files) > 0 ) {
		echo '<div>';
		for ($i=0,$n=count($this->files); $i<$n; $i++) {
			$this->setFile($i);
			echo $this->loadTemplate('file');
		}
		echo '</div>';
} else { ?>
<div>
	<center style="clear:both;font-size:large;font-weight:bold;color:#b3b3b3;font-family: Helvetica, sans-serif;">
		<?php echo JText::_( 'There are no files' ); ?>
	</center>
</div>
<?php } ?>


