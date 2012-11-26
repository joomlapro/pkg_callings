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
 * This models supports retrieving lists of results.
 *
 * @package     Callings
 * @subpackage  com_callings
 * @since       3.0
 */
class CallingsModelResults extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   3.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'state', 'a.state',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Get the configuration options.
		$app    = JFactory::getApplication();
		$input  = $app->input;
		$params = JComponentHelper::getParams('com_callings');
		$user   = JFactory::getUser();

		// List state information.
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $limitstart);

		// Load the ordering.
		$orderCol = $input->get('filter_order', 'created');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'created';
		}

		$this->setState('list.ordering', $orderCol);

		// Load the direction.
		$listOrder = $input->get('filter_order_Dir', 'DESC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);

		// Load the parameters.
		$this->setState('params', $params);

		if ((!$user->authorise('core.edit.state', 'com_callings')) &&  (!$user->authorise('core.edit', 'com_callings')))
		{
			// Limit to published for people who can't edit or edit.state.
			$this->setState('filter.state', 1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}

		// Load the layout.
		$this->setState('layout', $input->get('layout'));
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   3.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 *
	 * @since   3.0
	 */
	protected function getListQuery()
	{
		$user   = JFactory::getUser();
		$groups = implode(', ', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time'
				. ', a.state, a.ordering'
				. ', a.created, a.created_by_alias'
			)
		);
		$query->from($db->quoteName('#__atomtech_callings_results') . ' AS a');

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END AS author");
		$query->select("ua.email AS author_email");

		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

		// Filter by state
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('a.state = ' . (int) $state);
		}

		// Add the list ordering clause.
		$orderCol = $this->getState('list.ordering', 'a.created');
		$query->order($db->escape($orderCol) . ' ' . $db->escape($this->getState('list.direction', 'DESC')));

		return $query;
	}
}
