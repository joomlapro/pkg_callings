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
 * HTML Calling View class for the Callings component.
 *
 * @package     Callings
 * @subpackage  com_callings
 * @since       3.0
 */
class CallingsViewCallingForm extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $return_page;

	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  The template file to include.
	 *
	 * @return  mixed  False on error, null otherwise.
	 *
	 * @since   3.0
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$app		  = JFactory::getApplication();
		$user        = JFactory::getUser();

		// Get model data.
		$state       = $this->get('State');
		$item        = $this->get('Item');
		$form        = $this->get('Form');
		$return_page = $this->get('ReturnPage');

		if (empty($item->id))
		{
			$authorised = $user->authorise('core.create', 'com_callings');
		}
		else
		{
			$authorised = $item->params->get('access-edit');
		}

		if ($authorised !== true)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut to the parameters.
		$params	= &$state->params;

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->state       = &$state;
		$this->item        = &$item;
		$this->form        = &$form;
		$this->return_page = &$return_page;
		$this->params      = &$params;
		$this->user        = &$user;

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function _prepareDocument()
	{
		// Initialiase variables.
		$app     = JFactory::getApplication();
		$menus   = $app->getMenu();
		$pathway = $app->getPathway();
		$title   = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if (empty($this->item->id))
		{
			$head = JText::_('COM_CALLINGS_CALLINGFORM_SUBMIT_CALLING');
		}
		else
		{
			$head = JText::_('COM_CALLINGS_CALLINGFORM_EDIT_CALLING');
		}

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', $head);
		}

		$title = $this->params->def('page_title', $head);

		if ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$this->document->setTitle($title);

		$pathway->addItem($title, '');

		// Configure the document meta-description.
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		// Configure the document meta-keywords.
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		// Configure the document robots.
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
