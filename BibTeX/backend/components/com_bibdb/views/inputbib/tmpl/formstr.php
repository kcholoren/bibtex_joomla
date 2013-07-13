<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
	function validacion()
	{
		// recupero el form
		var form = document.adminForm;
		// valor del área de texto
		strbibtex = document.getElementById("textstrbib").value;
		if( strbibtex == null || strbibtex.length == 0 || /^\s+$/.test(strbibtex) )
		{
			alert( "<?php echo JText::_( 'ALERT_STRBIBTEX', true ); ?>" );
			return false;
		}
		else if( form.catid.value == "0" )
		{
			alert( "<?php echo JText::_( 'ALERT_CATEGORIA', true ); ?>" );
			return false;
		} else {
			return true;
		}
	}
</script>

<form action="<?php echo JURI::base(); ?>index.php?option=com_bibdb&amp;controller=inputbib&amp;task=savestr" method="post" name="adminForm" id="adminForm" onsubmit="return validacion()">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'LEGEND_BIBTEX_STRING' ); ?></legend>
		<table class="admintable">
			<!-- fila en blanco -->
			<tr><td colspan="2">&nbsp;</td></tr>

			<!-- textarea para pegar el string de BibTeXs -->
			<tr>
				<td width="100" valign="top" class="key">
					<label for="textstrbib"><?php echo JText::_( 'PEGAR_BIBTEX_STRING' ); ?>:</label>
				</td>
				<td valign="middle">
					<textarea class="inputbox" name="textstrbib" id="textstrbib" cols="64" rows="15" ></textarea>
				</td>
			</tr>

			<!-- lista desplegable con las categorías -->
			<tr>
				<td valign="middle" align="right" class="key">
					<label for="catid"><?php echo JText::_( 'Category' ); ?>:</label>
				</td>
				<td valign="middle">
					<?php echo $this->lists['catid']; ?>
				</td>
			</tr>

			<!-- radiobuttons para elegir si deben o no publicarse las entradas -->
			<tr>
				<td valign="middle" align="right" class="key">
					<label for="published"><?php echo JText::_( 'Published' ); ?>:</label>
				</td>
				<td valign="middle">
					<?php echo $this->lists['published']; ?>
				</td>
			</tr>

			<!-- lista desplegable para cambiar el orden del item dentro de la categoría establecida -->
			<tr>
				<td valign="middle" align="right" class="key">
					<label for="ordering"><?php echo JText::_( 'Ordering' ); ?>:</label>
				</td>
				<td valign="middle">
					<?php echo $this->lists['ordering']; ?>
				</td>
			</tr>

			<!-- fila en blanco -->
			<tr><td colspan="2">&nbsp;</td></tr>
			
			<!-- botón de submit -->
			<tr>
				<td valign="middle" colspan="2" align="left">
					<input type="submit" id="str-submit" value="<?php echo JText::_('VALUE_BTN_GUARDAR'); ?>"/>
				</td>
			</tr>

			<!-- fila en blanco -->
			<tr><td colspan="2">&nbsp;</td></tr>
		</table>
	</fieldset>
	<!-- campo oculto con url para redirección -->
	<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_bibdb&controller=inputbib&layout=formstr'); ?>" />
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>

</form>
