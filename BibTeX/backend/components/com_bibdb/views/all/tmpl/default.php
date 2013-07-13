<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.tooltip'); ?>

<form action="index.php" method="post" name="adminForm">
<table>
<tr>
	<td align="left" width="100%">
		<?php echo JText::_( 'LABELFILTER' ); ?>:
		<input type="text" name="search" id="search" size="30" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'LABELBTNGO' ); ?></button>
		<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_catid').value='0';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'LABELBTNRESET' ); ?></button>
	</td>
	<td nowrap="nowrap">
		<?php
			echo $this->lists['catid'];
			echo $this->lists['state'];
		?>
	</td>
</tr>
</table>
<div id="editcell">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="3%">
				<?php echo JText::_( 'NUM' ); ?>
			</th>
			<th width="3%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
			</th>
			<th width="8%">
				<?php echo JHTML::_( 'grid.sort', 'TIPOENT', 'bibtexentrytype', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="50%">
				<?php echo JHTML::_( 'grid.sort', 'TITULO', 'title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="10%">
				<?php echo JHTML::_( 'grid.sort', 'FECHAALTA', 'fechaalta', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="8%">
				<?php echo JHTML::_( 'grid.sort', 'Published', 'published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="10%" nowrap="nowrap">
				<?php echo JHTML::_('grid.sort',  'Order', 'a.ordering', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				<?php echo JHTML::_('grid.order',  $this->items ); ?>
			</th>
			<th width="8%" class="title">
				<?php echo JHTML::_('grid.sort',  'Category', 'category', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$row =& $this->items[$i];
		$checked 	= JHTML::_( 'grid.id', $i, $row->id );
		$published	= JHTML::_( 'grid.published', $row, $i );
		$link 		= JRoute::_( 'index.php?option=com_bibdb&task=edit&cid[]=' . $row->id );
		$ordering = ( $this->lists['order'] == 'a.ordering' );
		$row->cat_link 	= JRoute::_( 'index.php?option=com_categories&section=com_bibdb&task=edit&type=other&cid[]=' . $row->catid );
	?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php echo $row->bibtexentrytype; ?>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Editar BibTeX' ); ?>::<?php echo $this->escape($row->title); ?>">
					<a href="<?php echo $link; ?>">
						<?php // echo $this->escape($row->title); <--- Modified by Kcho because special characters were removed at import process
						echo $row->title;?>
					</a>
				</span>
			</td>
			<td align="center">
				<?php echo JHTML::_('date', $row->fechaalta, JText::_('FORMATO_FECHA'), $this->usersTZ); ?>
			</td>
			<td align="center">
				<?php echo $published; ?>
			</td>
			<td class="order">
				<!-- @ operador de control de errores, delante de una expresión hace que se ignore cualquier error
					 que pudiera llegar a producirse -->
				<span><?php echo $this->pagination->orderUpIcon( $i, ($row->catid == @$this->items[$i-1]->catid), 'orderup', 'Move Up', $ordering ); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, ($row->catid == @$this->items[$i+1]->catid), 'orderdown', 'Move Down', $ordering ); ?></span>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<input type="text" name="order[]" size="5" value="<?php echo $row->ordering;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit Category' );?>::<?php echo $this->escape($row->category); ?>">
				<a href="<?php echo $row->cat_link; ?>" >
				<?php echo $this->escape($row->category); ?></a></span>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_bibdb" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
