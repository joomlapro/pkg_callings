<?php
/**
 * @package     Callings
 * @subpackage  mod_callings
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Include the callings functions only once.
require_once __DIR__ . '/helper.php';

// Get the callings and results.
$opening_callings = ModCallingsHelper::getCallings($params);
$closed_callings = ModCallingsHelper::getCallings($params, 0);
$results = ModCallingsHelper::getResults($params);

// Initialise variables.
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_callings', $params->get('layout', 'default'));
