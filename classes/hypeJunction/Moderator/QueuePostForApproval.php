<?php

namespace hypeJunction\Moderator;

use Elgg\Event;
use hypeJunction\Capabilities\Role;
use hypeJunction\Capabilities\Roles;

class QueuePostForApproval {

	/**
	 * Queue post for approval
	 *
	 * @param Event $event Event
	 * @return false|null
	 */
	public function __invoke(Event $event) {

		$entity = $event->getObject();

		if (!$entity instanceof \ElggObject) {
			return null;
		}

		if ($entity->getVolatileData('is_approved')) {
			return null;
		}

		$params = [
			'entity' => $entity,
			'user' => elgg_get_logged_in_user_entity(),
		];

		if (!elgg_trigger_plugin_hook('uses:moderation', "$entity->type:$entity->subtype", $params, false)) {
			return null;
		}

		if (elgg_trigger_plugin_hook('permissions_check:administer', "$entity->type:$entity->subtype", $params, false)) {
			// Don't require moderation for moderators posts
			return null;
		}

		$entity->future_published_status = $entity->published_status;
		$entity->published_status = H_PENDING;

		/* @todo Notify moderators */

		system_message(elgg_echo('post:pending_approval'));

		return false;
	}
}