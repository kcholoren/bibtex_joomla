<?php defined('_JEXEC') or die('Restricted access'); ?>
&nbsp;&nbsp;
<?php
if((is_array($this->items))&&(is_array($this->items[0]))) {
	// creates an array in order to sort the categories according to is 'ordering' field
	for($index=0;$index<sizeof($this->items);$index++){
	  $mapping_array[] =  $this->category[$index]->ordering;
	}
	// sort ascending
	asort($mapping_array);
	// print ordered
	foreach($mapping_array as $val => $num){
		$titulo = $this->escape($this->category[$val]->title);
		print_Category($this->items[$val], $this, $titulo);
	}
} else {
	print_Category($this->items, $this);
}?>
<?php
function print_Category($category_items, $este, $title=false) {
	$sfx = $este->params->get( 'pageclass_sfx' );
	if ($title){
		if ( $este->params->get( 'filtrar_anio' ) != 0 ) {
			$title .= ' ('.$este->params->get( 'filtrar_anio' ).')';
		}
?>
<div class="componentheading<?php echo $sfx; ?>">
		<?php echo $title; ?>
</div><?php }?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php 
if (count($category_items)==0){ ?>
<tr class="sectiontableentry<?php echo 'emptycat'; ?>">
	<?php echo JText::_( 'EMPTY_CATEGORIA' ); ?>
</tr><?php
}else{
	if ( $este->params->def( 'mostrar_encabezado', 1 ) ) : ?>
<tr>
	<td width="20px" style="text-align:center;" class="sectiontableheader<?php echo $sfx; ?>"><?php echo JText::_('Num'); ?></td>
	<td height="20" class="sectiontableheader<?php echo $sfx; ?>" align="center"><?php echo JText::_('LABEL_REF'); ?></td>
	<td width="26px" height="20" class="sectiontableheader<?php echo $sfx; ?>" align="center"><acronym style="cursor:help;" title="BibTeX reference"><?php echo JText::_('bib'); ?></acronym></td>
	<td width="26px" height="20" class="sectiontableheader<?php echo $sfx; ?>" align="center"><acronym style="cursor:help;" title="Paper file"><?php echo JText::_('pdf'); ?></td>
	<td width="26px" height="20" class="sectiontableheader<?php echo $sfx; ?>" align="center"><acronym style="cursor:help;" title="Extra material"><?php echo 'ext'; ?></td>
</tr><?php
	endif;

	foreach ($category_items as $item) : ?>
<tr class="sectiontableentry<?php echo $item->odd + 1; ?>">
	<td align="right"><?php echo count($category_items) - $este->pagination->getRowOffset( $item->count ) + 1; ?></td>
	<td><span class="description"><?php echo $item->extendida; ?></span></td>
	<td align="center"><?php echo $item->bib; ?></td>
	<td><?php echo $item->pdf; ?></td>
	<td><?php echo $item->pdf_extra; ?></td>
	<!-- <img class="no_pdf" src="/images/bibdb/img_trans.gif"  width="1" height="1" alt="File not available" /> -->
</tr>
<?php endforeach; 
}
// la paginacion anda mal porque la muestra en cada categoría, habría que hacer que tenga en cuenta las categorías
// if ( $este->params->def( 'mostrar_paginacion', 0 ) ) : ?>
<tr>
	<td align="center" colspan="4" class="sectiontablefooter<?php //echo $sfx; ?>">
	<?php //echo $este->pagination->getPagesLinks(); ?>
	</td>
</tr>
<?php 
// if ( $este->params->def( 'mostrar_total_pag', 0 ) ) : 
?>
<tr>
	<td colspan="4" align="right" class="pagecounter">
		<?php //echo $este->pagination->getPagesCounter(); ?>
	</td>
</tr>
<?php //endif; ?>
<?php //endif; ?>
</table><br/>
<?php }?>