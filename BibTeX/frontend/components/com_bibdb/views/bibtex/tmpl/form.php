<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
function submitbutton(pressbutton)
{
	// recupero el form
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// valor del área de texto
	strbibtex = document.getElementById("jformbibtexarea").value;
	if( strbibtex == null || strbibtex.length == 0 || /^\s+$/.test(strbibtex) ) {
		alert( "<?php echo JText::_( 'ALERT_STRBIBTEX', true ); ?>" );
	} else if( form.catid.value == "0" ) {
		alert( "<?php echo JText::_( 'ALERT_CATEGORIA', true ); ?>" );
	} else {
		submitform( pressbutton );
	}
}
</script>

<!-- título de página -->
<?php if ( $this->params->def( 'show_page_title', 1 ) ) : ?>
	<div class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo $this->escape($this->params->get('page_title')); ?>
	</div>
<?php endif; ?>

<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<table cellpadding="4" cellspacing="1" border="0" width="100%">
<!-- área de texto para copiar los datos del BibTeX -->
<tr>
	<td valign="top" width="25%">
		<label for="jformbibtexarea">
			<?php echo JText::_( 'LABELBIBTEXAREA' ); ?>:
		</label>
	</td>
	<td>
		<textarea class="inputbox" cols="85" rows="15" id="jformbibtexarea" name="jformbibtexarea"></textarea>
	</td>
</tr>
<!-- lista desplegable con las categorías -->
<tr>
	<td valign="middle">
		<label for="catid">
			<?php echo JText::_( 'LABELCATEGORY' ); ?>:
		</label>
	</td>
	<td>
		<?php echo $this->lists['catid']; ?>
	</td>
</tr>
<!-- radiobuttons para elegir el estado de publicacion del BibTeX -->
<tr>
	<td valign="middle">
		<label for="published">
			<?php echo JText::_( 'LABELPUBLISHED' ); ?>:
		</label>
	</td>
	<td>
		<?php echo $this->lists['published']; ?>
	</td>
</tr>
<!-- lista desplegable para cambiar el orden del item dentro de la categoría establecida -->
<tr>
	<td valign="middle">
		<label for="ordering">
			<?php echo JText::_( 'LABELORDERING' ); ?>:
		</label>
	</td>
	<td valign="middle">
		<?php echo $this->lists['ordering']; ?>
	</td>
</tr>
<!-- radiobuttons para que el usuario indique si desea o no subir una publicación -->
<tr>
	<td valign="middle">
		<label for="jformadjuntar">
			<?php echo JText::_( 'LABELADJUNTAR' ); ?>:
		</label>
	</td>
	<td valign="middle">
		<?php echo $this->lists['adjuntar']; ?>
	</td>
</tr>
<!-- campo para ingresar el archivo a subir -->
<tr>
	<td valign="middle">
		<label for="jformuploadedfile">
			<?php echo JText::_( 'LABELUPLOADEDFILE' ); ?>:
		</label>
	</td>
	<td valign="middle">
		<input type="file" name="jformuploadedfile" id="jformuploadedfile" size="40"/>
	</td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
</table>

<div>
	<button type="button" onclick="submitbutton('save')">
		<?php echo JText::_('SAVE') ?>
	</button>
	<button type="button" onclick="submitbutton('cancel')">
		<?php echo JText::_('CANCEL') ?>
	</button>
</div>

	<input type="hidden" name="option" value="com_bibdb" />
	<input type="hidden" name="controller" value="bibtex" />
	<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
