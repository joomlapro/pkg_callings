<?php
/**
 * @package     Callings
 * @subpackage  mod_callings
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Add StyleSheet.
JHtml::stylesheet('mod_callings/template.css', false, true, false);
?>
<div class="callings<?php echo $moduleclass_sfx; ?>">
	<ul class="nav nav-tabs" id="myTab">
		<li class="active"><a data-toggle="tab" href="#opened"><?php echo JText::_('MOD_CALLINGS_OPENING'); ?></a></li>
		<li><a data-toggle="tab" href="#closed"><?php echo JText::_('MOD_CALLINGS_CLOSED'); ?></a></li>
		<li><a data-toggle="tab" href="#results"><?php echo JText::_('MOD_CALLINGS_RESULTS'); ?></a></li>
	</ul>
	<div class="tab-content" id="myTabContent">
		<div id="opened" class="tab-pane fade in active">
			<?php foreach ($opening_callings as $item): ?>
				<blockquote>
					<a href="<?php echo $item->link; ?>"><?php echo htmlspecialchars($item->title); ?></a>
				</blockquote>
			<?php endforeach; ?>
		</div>
		<div id="closed" class="tab-pane fade">
			<?php foreach ($closed_callings as $item): ?>
				<blockquote>
					<a href="<?php echo $item->link; ?>"><?php echo htmlspecialchars($item->title); ?></a>
				</blockquote>
			<?php endforeach; ?>
		</div>
		<div id="results" class="tab-pane fade">
			<?php foreach ($results as $item): ?>
				<blockquote>
					<a href="<?php echo $item->link; ?>"><?php echo htmlspecialchars($item->title); ?></a>
				</blockquote>
			<?php endforeach; ?>
		</div>
	</div>
	<a class="btn btn-success btn-small" href="<?php echo JRoute::_(CallingsHelperRoute::getCallingsRoute()); ?>">
		<i class="icon-plus"></i> <?php echo JText::_('MOD_CALLINGS_ALL_CALLINGS'); ?>
	</a>
</div>
