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
 * Callings helper.
 *
 * @package     Callings
 * @subpackage  com_callings
 * @since       3.0
 */
class CallingsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function addSubmenu($vName = 'callings')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_CALLINGS_SUBMENU_CALLINGS'),
			'index.php?option=com_callings&view=callings',
			$vName == 'callings'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_CALLINGS_SUBMENU_RESULTS'),
			'index.php?option=com_callings&view=results',
			$vName == 'results'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject  A JObject containing the allowed actions.
	 *
	 * @since   3.0
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;
		$assetName = 'com_callings';

		$actions = JAccess::getActions($assetName, 'component');

		foreach ($actions as $action)
		{
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
