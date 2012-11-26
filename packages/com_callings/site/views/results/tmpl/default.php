<?php
/**
 * @package     Callings
 * @subpackage  com_callings
 * @copyright   Copyright (C) 2012 AtomTech, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
?>
<div class="callings-results<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading')): ?>
		<div class="page-header">
			<h1>
				<?php echo $this->escape($this->params->get('page_heading')); ?>
			</h1>
		</div>
	<?php endif; ?>

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><?php echo JText::_('JDATE'); ?></th>
				<th><?php echo JText::_('JGLOBAL_TITLE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $item): ?>
				<tr>
					<td><?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?></td>
					<td><a href="<?php echo JRoute::_(CallingsHelperRoute::getResultRoute($item->slug)); ?>"><?php echo $this->escape($item->title); ?></a></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($this->params->get('show_pagination')): ?>
		<div class="pagination">
			<?php if ($this->params->def('show_pagination_results', 1)): ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif; ?>
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</div>
