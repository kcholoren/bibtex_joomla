<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="<?php echo JURI::base(); ?>index.php?option=com_bibdb&amp;controller=inputbib&amp;task=selectForm" method="post" name="adminForm" id="adminForm">

<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'Ingresar BibTeX(s)' ); ?></legend>
		<table class="admintable">
			<!-- fila en blanco -->
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<!-- label -->
				<td width="100" class="key">
					<label for="select-input-options"><?php echo JText::_( 'Metodo de entrada' ); ?>:</label>
				</td>
				<!-- lista desplegable -->
				<td>
					<?php echo $this->lists['entrada']; ?>
				</td>
			</tr>
			<tr>
				<td valign="middle" colspan="2" align="left">
					<input type="submit" id="select-input-submit" value="<?php echo JText::_('Continuar'); ?>"/>
				</td>
			</tr>
			<!-- fila en blanco -->
			<tr><td colspan="2">&nbsp;</td></tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>

</form>
