<?php

namespace hypeJunction\Moderator;

use Elgg\Hook;
use ElggMenuItem;

class UserHoverMenu {

	/**
	 * @elgg_plugin_hook register menu:user_hover
	 *
	 * @param Hook $hook Hook
	 *
	 * @return ElggMenuItem[]|null
	 */
	public function __invoke(Hook $hook) {

		$menu = $hook->getValue();

		$user = $hook->getEntityParam();
		if (!$user instanceof \ElggUser) {
			return null;
		}

		$svc = elgg()->roles;
		/* @var $svc \hypeJunction\Capabilities\RolesService */

		if (elgg_is_logged_in()) {
			if (!$svc->hasRole('moderator', $user)) {
				$menu[] = ElggMenuItem::factory([
					'name' => 'moderator:make',
					'text' => elgg_echo('moderator:make'),
					'icon' => 'edit',
					'href' => elgg_generate_action_url('moderator/make', [
						'guid' => $user->guid,
					]),
					'confirm' => true,
					'section' => 'admin',
				]);
			} else {
				$menu[] = ElggMenuItem::factory([
					'name' => 'moderator:remove',
					'text' => elgg_echo('moderator:remove'),
					'icon' => 'edit',
					'href' => elgg_generate_action_url('moderator/remove', [
						'guid' => $user->guid,
					]),
					'confirm' => true,
					'section' => 'admin',
				]);
			}
		}

		$container = elgg_get_page_owner_entity();

		if ($container instanceof \ElggGroup && $container->canEdit()) {
			if (!$svc->hasRole('moderator', $user, $container)) {
				$menu[] = ElggMenuItem::factory([
					'name' => 'moderator:group:make',
					'text' => elgg_echo('moderator:group:make'),
					'icon' => 'edit',
					'href' => elgg_generate_action_url('moderator/make', [
						'guid' => $user->guid,
						'target_guid' => $container->guid,
					]),
					'confirm' => true,
					'section' => 'admin',
				]);
			} else {
				$menu[] = ElggMenuItem::factory([
					'name' => 'moderator:group:remove',
					'text' => elgg_echo('moderator:group:remove'),
					'icon' => 'edit',
					'href' => elgg_generate_action_url('moderator/remove', [
						'guid' => $user->guid,
						'target_guid' => $container->guid,
					]),
					'confirm' => true,
					'section' => 'admin',
				]);
			}
		}

		return $menu;
	}
}