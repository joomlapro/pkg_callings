<?php
/**
 * @package     Callings
 * @subpackage  mod_callings
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Load dependent classes.
require_once JPATH_SITE . '/components/com_callings/helpers/route.php';

// Include the component models.
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_callings/models', 'CallingsModel');

/**
 * Helper for mod_callings
 *
 * @package     Callings
 * @subpackage  mod_callings
 * @since       3.0
 */
abstract class ModCallingsHelper
{
	/**
	 * Get a list of the calling items.
	 *
	 * @param   JRegistry  &$params  The module options.
	 * @param   int        $status   The register status. Type 1 to opened and 0 to closed.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public static function getCallings(& $params, $status = 1)
	{
		// Initialiase variables.
		$app = JFactory::getApplication();

		// Get an instance of the generic callings model.
		$model = JModelLegacy::getInstance('Callings', 'CallingsModel', array('ignore_request' => true));

		// Set application parameters in model.
		$appParams = JFactory::getApplication()->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params.
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));

		$model->setState('filter.state', 1);
		$model->setState('filter.status', $status);

		$model->setState('list.select', 'a.id, a.title, a.alias, a.date_opening, a.date_closing, a.state, a.access, a.params' .
			', a.created, a.created_by, a.created_by_alias, a.modified, a.modified_by');

		// Access filter.
		$access = !JComponentHelper::getParams('com_callings')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Set ordering.
		$ordering = $params->get('ordering', 'a.created');
		$model->setState('list.ordering', $ordering);

		if (trim($ordering) == 'rand()')
		{
			$model->setState('list.direction', '');
		}
		else
		{
			$model->setState('list.direction', 'DESC');
		}

		// Retrieve Callings.
		$items = $model->getItems();

		foreach ($items as &$item)
		{
			// Compute the calling slug.
			$item->slug = $item->id . ':' . $item->alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the calling.
				$item->link = JRoute::_(CallingsHelperRoute::getCallingRoute($item->slug));
				$item->linkText = JText::_('MOD_CALLINGS_READMORE');
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
				$item->linkText = JText::_('MOD_CALLINGS_READMORE_REGISTER');
			}
		}

		return $items;
	}

	/**
	 * Get a list of the result items.
	 *
	 * @param   JRegistry  &$params  The module options.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public static function getResults(& $params)
	{
		// Initialiase variables.
		$app = JFactory::getApplication();

		// Get an instance of the generic results model.
		$model = JModelLegacy::getInstance('Results', 'CallingsModel', array('ignore_request' => true));

		// Set application parameters in model.
		$appParams = JFactory::getApplication()->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params.
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));

		$model->setState('filter.state', 1);

		$model->setState('list.select', 'a.id, a.title, a.alias, a.state, a.access, a.params' .
			', a.created, a.created_by, a.created_by_alias, a.modified, a.modified_by');

		// Access filter.
		$access = !JComponentHelper::getParams('com_callings')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Set ordering.
		$ordering = $params->get('ordering', 'a.created');
		$model->setState('list.ordering', $ordering);

		if (trim($ordering) == 'rand()')
		{
			$model->setState('list.direction', '');
		}
		else
		{
			$model->setState('list.direction', 'DESC');
		}

		// Retrieve Results.
		$items = $model->getItems();

		foreach ($items as &$item)
		{
			// Compute the result slug.
			$item->slug = $item->id . ':' . $item->alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the result.
				$item->link = JRoute::_(CallingsHelperRoute::getResultRoute($item->slug));
				$item->linkText = JText::_('MOD_CALLINGS_READMORE');
			}
			else
			{
				$item->link = JRoute::_('index.php?option=com_users&view=login');
				$item->linkText = JText::_('MOD_CALLINGS_READMORE_REGISTER');
			}
		}

		return $items;
	}
}
