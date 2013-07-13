<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.filesystem.folder' );

function com_install()
{
	// -->
	$folder[0][0]	=	'docs' . DS ;
	$folder[0][1]	= 	JPATH_ROOT . DS .  $folder[0][0];
	
	$message = '';
	$error	 = array();
	foreach ($folder as $key => $value)
	{
		if (!JFolder::exists( $value[1]))
		{
			if (JFolder::create( $value[1], 0777 ))
			{
				@JFile::write($value[1].DS."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");
				$message .= '<p><b><span style="color:#009933">El directorio</span> ' . $value[0] 
						 .' <span style="color:#009933">ha sido creado!</span></b></p>';
				$error[] = 0;
			}	 
			else
			{
				$message .= '<p><b><span style="color:#CC0033">El directorio</span> ' . $value[0]
						 .' <span style="color:#CC0033">no se ha podido crear!</span></b> Por favor intentelo manualmente.</p>';
				$error[] = 1;
			}
		}
		else//Folder exist
		{
			$message .= '<p><b><span style="color:#009933">El directorio</span> ' . $value[0] 
					 .' <span style="color:#009933">ya existe!</span></b></p>';
			$error[] = 0;
		}
	}

	// genero un conjunto de pruebas
	$db		=& JFactory::getDBO();
	$msgSQL = '';

	// inserto algunas categorias
	$query = "INSERT INTO `#__categories` (`id`, `parent_id`, `title`, `alias`, `extension`, `description`, `published`, `checked_out`, `checked_out_time`, `modified_user_id`, `ordering`, `access`, `count`, `params`) VALUES (NULL, '0', 'Default', 'categoria-default', 'com_bibdb', 'BibTeX(s) sin clasificar.', '1', '0', '0000-00-00 00:00:00', NULL, '1', '0', '0', '')";
	$db->setQuery( $query );
	if (!$result = $db->query()) { $msgSQL .= $db->stderr() . '<br />'; }

/*
	$query = "INSERT INTO `#__categories` (`id`, `parent_id`, `title`, `name`, `alias`, `image`, `section`, `image_position`, `description`, `published`, `checked_out`, `checked_out_time`, `editor`, `ordering`, `access`, `count`, `params`) VALUES (NULL, '0', 'Logica', '', 'categoria-logica', '', 'com_bibdb', '', 'alguna descripción adecuada', '1', '0', '0000-00-00 00:00:00', NULL, '2', '0', '0', '')";
	$db->setQuery( $query );
	if (!$result = $db->query()) { $msgSQL .= $db->stderr() . '<br />'; }

	// recupero el id correspondiente a la categoria default insertada mas arriba
	$query = "SELECT id FROM `jos_categories` WHERE `section`='com_bibdb' AND `title`='default'";
	$db->setQuery( $query );
	if (!$catid = $db->loadResult()) { $msgSQL .= $db->stderr() . '<br />'; }

	$query = "INSERT INTO `#__bibdb_bibtex` (`catid`,`bibtexentryType`,`bibtexcitation`,`author`,`title`,`journal`,`volume`,`number`,`pages`,`year`,`url`,`fechaalta`,`path`,`ordering`) VALUES ('"."$catid"."','article','alvarez01minerva','Guillermo A. Alvarez and Elizabeth Borowsky and Susie Go and Theodore H. Romer and Ralph Becker-Szendy and Richard Golding and Arif Merchant and Mirjana Spasojevic and Alistair Veitch and John Wilkes','Minerva: An automated resource provisioning tool for large-scale storage systems','ACM Transactions on Computer Systems','19','4','483--518','2001','citeseer.ist.psu.edu/alvarez01minerva.html','2009-07-18 11:39:00','tesis.pdf','2')";
	$db->setQuery( $query );
	if (!$result = $db->query()) { $msgSQL .= $db->stderr() . '<br />'; }

	$query = "INSERT INTO `#__bibdb_bibtex` (`catid`,`bibtexentryType`,`bibtexcitation`,`author`,`title`,`publisher`,`year`,`annote`,`fechaalta`,`ordering`) VALUES ('"."$catid"."','book','Diller','Antoni Diller','LaTeX Line by Line','John Wiley and Sons','1993','Diller provides a highly readable introduction to LaTeX.','2009-07-18 11:25:00','1')";
	$db->setQuery( $query );
	if (!$result = $db->query()) { $msgSQL .= $db->stderr() . '<br />'; }

	// recupero el id correspondiente a la categoria logica insertada mas arriba
	$query = "SELECT id FROM `jos_categories` WHERE `section`='com_bibdb' AND `title`='logica'";
	$db->setQuery( $query );
	if (!$catid = $db->loadResult()) { $msgSQL .= $db->stderr() . '<br />'; }

	$query = "INSERT INTO `#__bibdb_bibtex` (`catid`,`bibtexentryType`,`bibtexcitation`,`author`,`title`,`booktitle`,`year`,`month`,`url`,`fechaalta`,`path`,`ordering`) VALUES ('"."$catid"."','inproceedings','mahalingam-locating','Mallik Mahalingam and Guillermo A. Alvarez and Magnus Karlsson and Theodore H. Romer','Locating Logical Volumes in Large-Scale Networks','Tenth NASA Goddard Conference on Mass Storage Systems and Technologies','2002','April','citeseer.ist.psu.edu/mahalingam02locating.html','2009-07-17 11:51:00','access control.txt','1')";
	$db->setQuery( $query );
	if (!$result = $db->loadResult()) { $msgSQL .= $db->stderr() . '<br />'; } */

	echo $message;
	echo $msgSQL;
}
?>