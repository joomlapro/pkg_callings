<?php
/**
 * @package     Callings
 * @subpackage  com_callings
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Build the route for the com_callings component
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @since   3.0
 */
function callingsBuildRoute(& $query)
{
	$segments = array();

	// Get a menu item based on Itemid or currently active
	$app      = JFactory::getApplication();
	$menu     = $app->getMenu();
	$params   = JComponentHelper::getParams('com_callings');
	$advanced = $params->get('sef_advanced_link', 1);

	// We need a menu item. Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
	}

	$mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
	$mId   = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

	if (isset($query['view']))
	{
		$view = $query['view'];

		if (empty($query['Itemid']))
		{
			$segments[] = $query['view'];
		}

		// We need to keep the view for forms since they never have their own menu item
		if ($view != 'callingform' and $view != 'resultform')
		{
			unset($query['view']);
		}
	}

	// Are we dealing with an calling that is attached to a menu item?
	if (isset($query['view']) && ($mView == $query['view']) and (isset($query['id'])) and ($mId == (int) $query['id']))
	{
		unset($query['view']);
		unset($query['id']);

		return $segments;
	}

	if (isset($view) and $view == 'calling')
	{
		if ($mId != (int) $query['id'] || $mView != $view)
		{
			if ($view == 'calling')
			{
				if ($advanced)
				{
					list($tmp, $id) = explode(':', $query['id'], 2);
				}
				else
				{
					$id = $query['id'];
				}

				$segments[] = $id;
			}
		}

		unset($query['id']);
	}

	if (isset($view) and $view == 'result')
	{
		if ($mId != (int) $query['id'] || $mView != $view)
		{
			if ($view == 'result')
			{
				if ($advanced)
				{
					list($tmp, $id) = explode(':', $query['id'], 2);
				}
				else
				{
					$id = $query['id'];
				}

				$segments[] = $id;
			}
		}

		unset($query['id']);
	}

	if (isset($query['layout']))
	{
		if (!empty($query['Itemid']) && isset($menuItem->query['layout']))
		{
			if ($query['layout'] == $menuItem->query['layout'])
			{
				unset($query['layout']);
			}
		}
		else
		{
			if ($query['layout'] == 'default')
			{
				unset($query['layout']);
			}
		}
	}

	return $segments;
}

/**
 * Parse the segments of a URL.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   3.0
 */
function callingsParseRoute($segments)
{
	$vars = array();

	// Get the active menu item.
	$app      = JFactory::getApplication();
	$menu     = $app->getMenu();
	$item     = $menu->getActive();
	$params   = JComponentHelper::getParams('com_callings');
	$advanced = $params->get('sef_advanced_link', 1);

	// Count route segments
	$count = count($segments);

	// Standard routing for callings.
	if (!isset($item))
	{
		$vars['view'] = $segments[0];
		$vars['id']   = $segments[$count - 1];

		return $vars;
	}

	$found = 0;

	foreach ($segments as $segment)
	{
		$segment = $advanced ? str_replace(':', '-', $segment) : $segment;

		if ($found == 0)
		{
			switch ($item->query['view'])
			{
				case 'callings':
					if ($advanced)
					{
						$db = JFactory::getDBO();
						$query = 'SELECT id FROM #__callings WHERE alias = ' . $db->Quote($segment);
						$db->setQuery($query);
						$nid = $db->loadResult();
					}
					else
					{
						$nid = $segment;
					}

					$vars['id']   = $nid;
					$vars['view'] = 'calling';

					break;

				case 'results':
					if ($advanced)
					{
						$db = JFactory::getDBO();
						$query = 'SELECT id FROM #__callings_results WHERE alias = ' . $db->Quote($segment);
						$db->setQuery($query);
						$nid = $db->loadResult();
					}
					else
					{
						$nid = $segment;
					}

					$vars['id']   = $nid;
					$vars['view'] = 'result';

					break;
			}
		}

		$found = 0;
	}

	return $vars;
}
