<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
/*	function tableOrdering( order, dir, task ) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit( task );
}*/
</script>
<!--
<form action="<?php //echo JFilterOutput::ampReplace($this->action); ?>" method="post" name="adminForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>-->
	<!-- filtro -->
<!--	<td align="left">
		<?php //echo JText::_( 'LABELFILTER' ); ?>:
		<input type="text" name="search" id="search" value="<?php //echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php //echo JText::_( 'LABELBTNGO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php //echo JText::_( 'LABELBTNRESET' ); ?></button>
	</td>
</tr>
</table>
&nbsp;&nbsp;-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ( $this->params->def( 'mostrar_encabezado', 1 ) ) : ?>
<tr>
	<td width="10" style="text-align:center;" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_('Num'); ?>
	</td>
	<td width="80%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center">
		<?php echo JText::_('LABEL_REF'); ?>
	</td>
	<td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center">
		<?php echo JText::_('pdf'); ?>
	</td>
	<td width="5%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" align="center">
		<?php echo JText::_('bib'); ?>
	</td>
</tr>
<?php endif; ?>
<?php foreach ($this->items as $item) : ?>
<tr class="sectiontableentry<?php echo $item->odd + 1; ?>">
	<td align="right">
		<?php echo $this->pagination->getRowOffset( $item->count ); ?>
	</td>
	<td>
		<!-- información de la referencia -->
		<span class="description"><?php echo $item->extendida; ?></span>
	</td>
	<td>
		<?php echo $item->pdf; ?>
	</td>
	<td align="center">
		<?php echo $item->bib; ?>
	</td>
</tr>
<?php endforeach; ?>
<?php if ( $this->params->def( 'mostrar_paginacion', 0 ) ) : ?>
<tr>
	<td align="center" colspan="4" class="sectiontablefooter<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php echo $this->pagination->getPagesLinks(); ?>
	</td>
</tr>
<?php if ( $this->params->def( 'mostrar_total_pag', 0 ) ) : ?>
<tr>
	<td colspan="4" align="right" class="pagecounter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</td>
</tr>
<?php endif; ?>
<?php endif; ?>
</table>
<!--<input type="hidden" name="filter_order" value="<?php //echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />-->
</form>
