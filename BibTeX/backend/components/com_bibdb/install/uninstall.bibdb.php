<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.filesystem.folder' );

function com_uninstall()
{
	// -->
	$folder[0][0]	=	'docs' . DS ;
	$folder[0][1]	= 	JPATH_ROOT . DS .  $folder[0][0];
	
	$message = '';
	$error	 = array();
	foreach ($folder as $key => $value)
	{
		if (JFolder::exists( $value[1]))
		{
			$message .= '<p><b><span style="color:#009933">El directorio</span> ' . $value[0] 
					 .' <span style="color:#009933">todavia existe!</span></b></p>';
		}
	}
	if ($message != '') {
		$message .= '<p>Por favor eliminelo(s) manualmente.</p>';
	}

	/* elimino las entradas en jos_categories correspondientes al componente */
	$db		=& JFactory::getDBO();
	$msgSQL = '';

	$query = "delete from #__categories where `section`='com_bibdb'";
	$db->setQuery( $query );
	if (!$result = $db->query()) { $msgSQL .= $db->stderr() . '<br />'; }

	echo $message;
	echo $msgSQL;
}
?>