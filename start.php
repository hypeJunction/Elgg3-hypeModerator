<?php

use hypeJunction\Capabilities\Role;

require_once __DIR__ . '/autoloader.php';

define('H_PENDING', 'pending');

return function () {

	elgg_register_event_handler('init', 'system', function () {

		elgg()->roles->register('moderator', [], 800);
		elgg()->roles->register('group_moderator', ['moderator'], 800);

		$moderator = elgg()->roles->moderator;
		/* @var $moderator Role */

		$subtypes = get_registered_entity_types('object');

		foreach ($subtypes as $subtype) {
			$moderator->onRead('object', $subtype, Role::ALLOW, Role::OVERRIDE);
			$moderator->onUpdate('object', $subtype, Role::ALLOW, Role::OVERRIDE);
			$moderator->onDelete('object', $subtype, Role::ALLOW, Role::OVERRIDE);
			$moderator->onAdminister('object', $subtype, Role::ALLOW, Role::OVERRIDE);
		}

		elgg_register_plugin_hook_handler('register', 'menu:user_hover', \hypeJunction\Moderator\UserHoverMenu::class);
		elgg_register_plugin_hook_handler('register', 'menu:entity', \hypeJunction\Moderator\EntityMenu::class);

		elgg_register_event_handler('publish', 'object', \hypeJunction\Moderator\QueuePostForApproval::class, 100);

		/** @todo Add reject action */
	});

};
