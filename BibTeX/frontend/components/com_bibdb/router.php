<?php
/**
 * @version		$Id: router.php 14401 2010-12-27 14:42:00Z kcho $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

function bibdbBuildRoute(&$query)
{
	/* examples
	 print_r($query);die();

	Array
	(
			[option] => com_bibdb
			[view] => bibpopup
			[tmpl] => component
			[id] => 4761
			[Itemid] => 7
	)

	Array
	(
			[option] => com_bibdb
			[task] => download
			[id] => 4771
			[Itemid] => 7
	)


	*/
	$segments	= array();


	if(isset($query['tmpl']))
	{
		$segments[] = $query['tmpl'];
		unset( $query['tmpl'] );
	}
	if(isset($query['id']))
	{
		$segments[] = $query['id'];
		unset( $query['id'] );
	};
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		unset( $query['view'] );
	}
	if(isset($query['task']))
	{
		$segments[] = $query['task'];
		unset( $query['task'] );
	}

	// echo "build"; print_r($segments);
	return $segments;

}

function bibdbParseRoute($segments)
{
	/*  examples
	 Array
	(
			[0] => download
			[1] => 4771
	)


	Array (
			[0] => bibpopup
			[1] => component <-- borrado Kcho
			[2] => 4758
	) */

	//   echo "parse"; print_r($segments);//die();
	$vars = array();
	switch($segments[1])
	{
		case 'download':
			$vars['task'] = 'download';
			$id = explode( ':', $segments[0] );
			$vars['id'] = (int) $id[0];
			break;
		case 'downloadextra':
			$vars['task'] = 'downloadextra';
			$id = explode( ':', $segments[0] );
			$vars['id'] = (int) $id[0];
			break;
		case 'bibpopup':
			$vars['view'] = 'bibpopup';
			$vars['tmpl'] = 'component';
			$id = explode( ':', $segments[0] );
			$vars['id'] = (int) $id[0];
			break;
		default: // detect old style routing
			 
			switch($segments[0])
			{
				case 'download':
					$vars['task'] = 'download';
					$id = explode( ':', $segments[1] );
					$vars['id'] = (int) $id[0];
					break;
				case 'downloadextra':
					$vars['task'] = 'downloadextra';
					$id = explode( ':', $segments[1] );
					$vars['id'] = (int) $id[0];
					break;
				case 'bibpopup':
					$vars['view'] = 'bibpopup';
					$vars['tmpl'] = 'component';
					$id = explode( ':', $segments[1] );
					$vars['id'] = (int) $id[0];
					break;
					//                default:
					// 			print_r($vars);
			}

	}
	//  	print_r($vars); //die();
	return $vars;

}
?>