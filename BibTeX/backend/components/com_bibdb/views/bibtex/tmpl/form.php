<?php defined('_JEXEC') or die('Restricted access'); 

// importar PARSEENTRIES.php
require_once( JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'PARSEENTRIES.php' );

// recupero los datos de los BibTeXs como un arreglo de arreglos asociativos
$parse = new PARSEENTRIES();
$parse->expandMacro = TRUE;
$parse->loadBibtexString($this->bibtex['detalles']);
$parse->extractEntries();

list($preamble, $strings, $entries1, $undefinedStrings) = $parse->returnArrays();

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal', 'a.modal-button-file');
JHTML::_('behavior.modal', 'a.modal-button-extra'); 
?>

<script language="javascript" type="text/javascript">
	function submitbutton(pressbutton) {
		// recupero el form
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		// valor del área de texto
		strbibtex = document.getElementById("detalles-bibtex").value;
		if( strbibtex == null || strbibtex.length == 0 || /^\s+$/.test(strbibtex) )
		{
			alert( "<?php echo JText::_( 'ALERT_STRBIBTEX', true ); ?>" );
		}
		else if( form.catid.value == "0" )
		{
			alert( "<?php echo JText::_( 'ALERT_CATEGORIA', true ); ?>" );
		} else {
			submitform( pressbutton );
		}
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'LEGEND_DETALLES' ); ?></legend>
		<table class="admintable">

		<!-- caja de texto con los detalles del BibTex -->
		<tr>
			<td width="100" valign="top" align="right" class="key">
				<label for="detalles-bibtex"><?php echo JText::_( 'Datos del BibTeX' ); ?>:</label>
			</td>
			<td valign="top">
				<textarea class="text_area" cols="64" rows="10" name="detalles-bibtex" id="detalles-bibtex"><?php echo $this->escape($this->bibtex['detalles']); ?></textarea>
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

		<!-- radiobuttons para elegir si debe o no publicarse los datos del BibTeX -->
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
		</tr><?php
		//print_r($this);
		//die();
		?>
		<!-- URL fuera del servidor 
		<tr>
			<td valign="middle" align="right" class="key">
				<label for="ordering"><?php echo JText::_( 'Recurso Web' ); ?>:</label>
			</td>
			<td valign="middle">
				<input type="text" class="text_area" name="web" id="web" value="<?php 
					if ( isset($this->lists['url'])) {
						echo $this->lists['url'];				
					}
				?>" size="50" maxlength="255" />
<?php	// added by Kcho
				if ( isset($entries1[0]['doi'])) { ?>
				<td valign="middle" align="left">
				<div class="button2-left" style="display:inline">
					<div class="<?php echo $this->buttondoi->name; ?>">
						<a class="<?php echo $this->buttondoi->modalname; ?>" title="<?php echo $this->buttondoi->text; ?>" href="#" onclick="javascript:document.getElementById('web').value = '<?php echo $entries1[0]['doi'];?>';"><?php echo $this->buttondoi->text; ?></a>
					</div>
				</div>
				</td>
<?php			}	?>				
			</td>
		</tr>
-->
		
		<!-- textarea y botón para enlazar una publicación a los datos del BibTeX -->
		<tr>
			<td valign="middle" align="right" class="key">
				<label for="filename"><?php echo JText::_( 'PATH' ); ?>:</label>
			</td>
			<td valign="middle">
				<input type="text" class="text_area" name="filename" id="filename" value="<?php echo $this->bibtex['path']; ?>" size="50" maxlength="255" />
			</td>
			<td valign="middle" align="left">
				<div class="button2-left" style="display:inline">
					<div class="<?php echo $this->buttonfile->name; ?>">
						<a class="<?php echo $this->buttonfile->modalname; ?>" title="<?php echo $this->buttonfile->text; ?>" href="<?php echo $this->buttonfile->link; ?>" rel="<?php echo $this->buttonfile->options; ?>"><?php echo $this->buttonfile->text; ?></a>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td valign="middle" align="right" class="key">
				<label for="filename_extra"><?php echo JText::_( 'PATH' ); ?>:</label>
			</td>
			<td valign="middle">
				<input type="text" class="text_area" name="filename_extra" id="filename_extra" value="<?php echo $this->bibtex['path_extra']; ?>" size="50" maxlength="255" />
			</td>
			<td valign="middle" align="left">
				<div class="button2-left" style="display:inline">
					<div class="<?php echo $this->buttonfileextra->name; ?>">
						<a class="<?php echo $this->buttonfileextra->modalname; ?>" title="<?php echo $this->buttonfileextra->text; ?>" href="<?php echo $this->buttonfileextra->link; ?>" rel="<?php echo $this->buttonfileextra->options; ?>"><?php echo $this->buttonfileextra->text; ?></a>
					</div>
				</div>
			</td>
		</tr>

		</table>
	</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="option" value="com_bibdb" />
<input type="hidden" name="cid[]" value="<?php echo $this->bibtex['id']; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="inputbib" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
