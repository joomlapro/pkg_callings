<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Search.callings_results
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('JPATH_BASE') or die;

require_once JPATH_SITE . '/components/com_callings/helpers/route.php';

/**
 * Callings Results Search plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Search.callings_results
 * @since       3.0
 */
class PlgSearchCallings_Results extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @access  protected
	 * @since   3.0
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Callings result Search Areas method.
	 *
	 * @return  array An array of search areas
	 *
	 * @since   3.0
	 */
	public function onContentSearchAreas()
	{
		static $areas = array(
			'callings_results' => 'PLG_SEARCH_CALLINGS_RESULTS_CALLINGS_RESULTS'
		);

		return $areas;
	}

	/**
	 * Callings result Search method.
	 *
	 * The sql must return the following fields that are used in a common display
	 * routine: href, title, section, created, text, browsernav.
	 *
	 * @param   string  $text      Target search string.
	 * @param   string  $phrase    mathcing option, exact|any|all.
	 * @param   string  $ordering  ordering option, newest|oldest|popular|alpha.
	 * @param   mixed   $areas     An array if the search it to be restricted to areas, null if search all.
	 *
	 * @return  string
	 *
	 * @since   3.0
	 */
	public function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		// Initialiase variables.
		$db     = JFactory::getDbo();
		$app    = JFactory::getApplication();
		$user   = JFactory::getUser();
		$groups = implode(', ', $user->getAuthorisedViewLevels());

		$searchText = $text;

		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		$sContent  = $this->params->get('search_content', 1);
		$sArchived = $this->params->get('search_archived', 1);
		$limit     = $this->params->def('search_limit', 50);
		$state = array();

		if ($sContent)
		{
			$state[] = 1;
		}
		if ($sArchived)
		{
			$state[] = 2;
		}

		$text = trim($text);

		if ($text == '')
		{
			return array();
		}

		$section = JText::_('PLG_SEARCH_CALLINGS_RESULTS');
		$wheres	= array();

		switch ($phrase)
		{
			case 'exact':
				$text      = $db->Quote('%' . $db->escape($text, true) . '%', false);
				$wheres2   = array();
				$wheres2[] = 'a.description LIKE ' . $text;
				$wheres2[] = 'a.title LIKE ' . $text;
				$where     = '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words  = explode(' ', $text);
				$wheres = array();

				foreach ($words as $word)
				{
					$word      = $db->Quote('%' . $db->escape($word, true) . '%', false);
					$wheres2   = array();
					$wheres2[] = 'a.description LIKE ' . $word;
					$wheres2[] = 'a.title LIKE ' . $word;
					$wheres[]  = implode(' OR ', $wheres2);
				}

				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		switch ($ordering)
		{
			case 'oldest':
				$order = 'a.created ASC';
				break;

			case 'popular':
				$order = 'a.hits DESC';
				break;

			case 'alpha':
				$order = 'a.title ASC';
				break;

			case 'newest':
			default:
				$order = 'a.created DESC';
		}

		$return = array();

		if (!empty($state))
		{
			$query = $db->getQuery(true);

			// Sqlsrv changes.
			$case_when = ' CASE WHEN ';
			$case_when .= $query->charLength('a.alias', '!=', '0');
			$case_when .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$case_when .= ' ELSE ';
			$case_when .= $a_id . ' END as slug';

			$query->select('a.title AS title, a.description AS text, a.created AS created, '
				. $case_when . ', '
				. $db->Quote($section) . ' AS section, \'2\' AS browsernav');
			$query->from($db->quoteName('#__atomtech_callings_results') . ' AS a');
			$query->where('(' . $where . ')' . ' AND a.state in (' . implode(', ', $state) . ')');
			$query->order($order);

			// Filter by language.
			if ($app->isSite() && $app->getLanguageFilter())
			{
				$tag = JFactory::getLanguage()->getTag();
				$query->where('a.language in (' . $db->Quote($tag) . ', ' . $db->Quote('*') . ')');
			}

			// Set the query and load the result.
			$db->setQuery($query, 0, $limit);
			$rows = $db->loadObjectList();

			$return = array();

			if ($rows)
			{
				foreach ($rows as $key => $row)
				{
					$rows[$key]->href = CallingsHelperRoute::getResultRoute($row->slug);
				}

				foreach ($rows as $key => $result)
				{
					if (searchHelper::checkNoHTML($result, $searchText, array('text', 'title')))
					{
						$return[] = $result;
					}
				}
			}
		}

		return $return;
	}
}
