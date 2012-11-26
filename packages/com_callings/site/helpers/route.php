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
 * Callings Component Route Helper
 *
 * @static
 * @package     Callings
 * @subpackage  com_callings
 * @since       3.0
 */
abstract class CallingsHelperRoute
{
	protected static $lookup;

	/**
	 * Method to get a route configuration for the callings view.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getCallingsRoute()
	{
		// Create the link
		$link = 'index.php?option=com_callings&view=callings';

		if ($item = self::_findItem(array('callings' => array(0))))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get a route configuration for the calling view.
	 *
	 * @param   int  $id  The route of the calling.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getCallingRoute($id)
	{
		// Initialiase variables.
		$needles = array(
			'calling' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_callings&view=calling&id=' . $id;

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem(array('callings' => array(0))))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get a route configuration for the result view.
	 *
	 * @param   int  $id  The route of the result.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getResultRoute($id)
	{
		// Initialiase variables.
		$needles = array(
			'result' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_callings&view=result&id=' . $id;

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem(array('results' => array(0))))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		return $link;
	}

	/**
	 * Method to get a route configuration for the form view.
	 *
	 * @param   int     $id      The id of the calling.
	 * @param   string  $return  The return page variable.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getCallingFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id)
		{
			$link = 'index.php?option=com_callings&task=calling.edit&w_id=' . $id;
		}
		else
		{
			$link = 'index.php?option=com_callings&task=calling.add&w_id=0';
		}

		if ($return)
		{
			$link .= '&return=' . $return;
		}

		return $link;
	}

	/**
	 * Method to get a route configuration for the form view.
	 *
	 * @param   int     $id      The id of the result.
	 * @param   string  $return  The return page variable.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public static function getResultFormRoute($id, $return = null)
	{
		// Create the link.
		if ($id)
		{
			$link = 'index.php?option=com_callings&task=result.edit&w_id=' . $id;
		}
		else
		{
			$link = 'index.php?option=com_callings&task=result.add&w_id=0';
		}

		if ($return)
		{
			$link .= '&return=' . $return;
		}

		return $link;
	}

	/**
	 * Method to find the item.
	 *
	 * @param   array  $needles  The needles to find.
	 *
	 * @return  null
	 *
	 * @since   3.0
	 */
	protected static function _findItem($needles = null)
	{
		// Initialiase variables.
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component = JComponentHelper::getComponent('com_callings');
			$items     = $menus->getItems('component_id', $component->id);

			if ($items)
			{
				foreach ($items as $item)
				{
					if (isset($item->query) && isset($item->query['view']))
					{
						$view = $item->query['view'];

						if (!isset(self::$lookup[$view]))
						{
							self::$lookup[$view] = array();
						}

						if (isset($item->query['id']))
						{
							self::$lookup[$view][$item->query['id']] = $item->id;
						}
						else
						{
							self::$lookup[$view][] = $item->id;
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$view][(int) $id]))
						{
							return self::$lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();

			if ($active && $active->component == 'com_callings')
			{
				return $active->id;
			}
		}

		return null;
	}
}
