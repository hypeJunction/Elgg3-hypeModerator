<?php

namespace hypeJunction\Moderator;

use Elgg\Hook;
use hypeJunction\Capabilities\Role;
use hypeJunction\Capabilities\Roles;

class EntityMenu {

	/**
	 * Setup entity menu
	 *
	 * @param Hook $hook
	 *
	 * @return \ElggMenuItem[]|null
	 */
	public function __invoke(Hook $hook) {

		$entity = $hook->getEntityParam();

		if (!$entity instanceof \ElggObject) {
			return null;
		}

		$params = [
			'entity' => $entity,
			'user' => elgg_get_logged_in_user_entity(),
		];

		if (!elgg_trigger_plugin_hook('permissions_check:administer', "$entity->type:$entity->subtype", $params, false)) {
			// No permissions to approve
			return null;
		}

		$menu = $hook->getValue();

		if ($entity->published_status === H_PENDING) {
			$menu[] = \ElggMenuItem::factory([
				'name' => 'approve',
				'text' => elgg_echo('post:approve'),
				'confirm' => true,
				'href' => elgg_generate_action_url('post/approve', [
					'guid' => $entity->guid,
				]),
				'icon' => 'check',
			]);
		}

		return $menu;
	}
}