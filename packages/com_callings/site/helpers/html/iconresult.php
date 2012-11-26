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
 * Callings Component HTML Helper.
 *
 * @static
 * @package     Callings
 * @subpackage  com_callings
 * @since       3.0
 */
class JHtmlIconResult
{
	/**
	 * Display an create icon for the result.
	 *
	 * @param   object  $params  The result parameters
	 *
	 * @return  string  The HTML for the result edit icon.
	 *
	 * @since   3.0
	 */
	public static function create($params)
	{
		// Initialiase variables.
		$uri = JURI::getInstance();

		$url = 'index.php?option=com_callings&task=result.add&return=' . base64_encode($uri) . '&c_id=0';

		if ($params->get('show_icons', 1))
		{
			$text = '<i class="icon-plus"></i> ' . JText::_('JNEW') . '&#160;';
		}
		else
		{
			$text = JText::_('JNEW') . '&#160;';
		}

		$button = JHtml::_('link', JRoute::_($url), $text, 'class="btn btn-primary"');

		$output = '<span class="hasTip" title="' . JText::_('COM_CALLINGS_CREATE_RESULT') . '">' . $button . '</span>';

		return $output;
	}

	/**
	 * Display an email icon for the result.
	 *
	 * @param   object  $result   The result in question.
	 * @param   object  $params   The result parameters
	 * @param   array   $attribs  Not used??
	 *
	 * @return  string  The HTML for the result edit icon.
	 *
	 * @since   3.0
	 */
	public static function email($result, $params, $attribs = array())
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';

		// Initialiase variables.
		$uri  = JURI::getInstance();
		$base = $uri->toString(array('scheme', 'host', 'port'));
		$template = JFactory::getApplication()->getTemplate();
		$link = $base . JRoute::_(CallingsHelperRoute::getResultRoute($result->slug), false);
		$url  = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_icons', 1))
		{
			$text = '<i class="icon-envelope"></i> ' . JText::_('JGLOBAL_EMAIL');
		}
		else
		{
			$text = JText::_('JGLOBAL_EMAIL');
		}

		$attribs['title']   = JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";

		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);
		return $output;
	}

	/**
	 * Display an edit icon for the result.
	 *
	 * This icon will not display in a popup window, nor if the result is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   object  $result   The result in question.
	 * @param   object  $params   The result parameters
	 * @param   array   $attribs  Not used??
	 *
	 * @return  string  The HTML for the result edit icon.
	 *
	 * @since   3.0
	 */
	public static function edit($result, $params, $attribs = array())
	{
		// Initialiase variables.
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$uri    = JURI::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($result->state < 0)
		{
			return;
		}

		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the result is checked out by a different user
		if (property_exists($result, 'checked_out') && property_exists($result, 'checked_out_time') && $result->checked_out > 0 && $result->checked_out != $user->get('id'))
		{
			$checkoutUser = JFactory::getUser($result->checked_out);
			$button = JHtml::_('image', 'system/checked_out.png', null, null, true);
			$date = JHtml::_('date', $result->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . JText::sprintf('COM_CALLINGS_CHECKED_OUT_BY', $checkoutUser->name) . ' <br /> ' . $date;

			return '<span class="hasTip" title="' . htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8') . '">' . $button . '</span>';
		}

		$url = 'index.php?option=com_callings&task=result.edit&c_id=' . $result->id . '&return=' . base64_encode($uri);

		if ($result->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date', $result->created);
		$author = $result->created_by_alias ? $result->created_by_alias : $result->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_CALLINGS_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$icon = $result->state ? 'edit' : 'eye-close';
		$text = '<i class="hasTip icon-' . $icon . ' tip" title="' . JText::_('COM_CALLINGS_EDIT_ITEM') . ' :: ' . $overlib . '"></i> ' . JText::_('JGLOBAL_EDIT');

		$output = JHtml::_('link', JRoute::_($url), $text);

		return $output;
	}

	/**
	 * Display an print popup icon for the result.
	 *
	 * @param   object  $result   The result in question.
	 * @param   object  $params   The result parameters
	 * @param   array   $attribs  Not used??
	 *
	 * @return  string  The HTML for the result edit icon.
	 *
	 * @since   3.0
	 */
	public static function print_popup($result, $params, $attribs = array())
	{
		// Initialiase variables.
		$url  = CallingsHelperRoute::getResultRoute($result->slug);
		$url .= '&tmpl=component&print=1&layout=default&page=' . @ $request->limitstart;

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

		// Checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons', 1))
		{
			$text = '<i class="icon-print"></i> ' . JText::_('JGLOBAL_PRINT');
		}
		else
		{
			$text = JText::_('JGLOBAL_PRINT');
		}

		$attribs['title']   = JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		$attribs['rel']     = 'nofollow';

		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}

	/**
	 * Display an print screen icon for the result.
	 *
	 * @param   object  $result   The result in question.
	 * @param   object  $params   The result parameters
	 * @param   array   $attribs  Not used??
	 *
	 * @return  string  The HTML for the result edit icon.
	 *
	 * @since   3.0
	 */
	public static function print_screen($result, $params, $attribs = array())
	{
		// Checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons', 1))
		{
			$text = $text = '<i class="icon-print"></i> ' . JText::_('JGLOBAL_PRINT');
		}
		else
		{
			$text = JText::_('JGLOBAL_PRINT');
		}

		return '<a href="#" onclick="window.print();return false;">' . $text . '</a>';
	}
}
