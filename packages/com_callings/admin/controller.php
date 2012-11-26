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
 * Callings Component Controller
 *
 * @package     Callings
 * @subpackage  com_callings
 * @since       3.0
 */
class CallingsController extends JControllerLegacy
{
	/**
	 * @var     string  The default view.
	 * @since   3.0
	 */
	protected $default_view = 'callings';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   3.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Include dependancies.
		require_once JPATH_COMPONENT . '/helpers/callings.php';

		$view   = $this->input->get('view', 'callings');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if (($view == 'calling' && $layout == 'edit' && !$this->checkEditId('com_callings.edit.calling', $id))
			|| ($view == 'result' && $layout == 'edit' && !$this->checkEditId('com_callings.edit.result', $id)))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_callings&view=callings', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
