<?php

namespace hypeJunction\Moderator;

use Elgg\EntityNotFoundException;
use Elgg\EntityPermissionsException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class ApproveAction {

	/**
	 * Archive a post
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws \Exception
	 */
	public function __invoke(Request $request) {

		return elgg_call(ELGG_IGNORE_ACCESS, function() use ($request) {

			$entity = $request->getEntityParam();

			if (!$entity) {
				throw new EntityNotFoundException();
			}

			$user = elgg_get_logged_in_user_entity();

			$params = [
				'entity' => $entity,
				'user' => $user,
			];

			if (!elgg_trigger_plugin_hook('permissions_check:administer', "$entity->type:$entity->subtype", $params, false)) {
				// No permissions to approve
				throw new EntityPermissionsException();
			}

			$status = $entity->future_published_status ? : 'published';
			$entity->published_status = $status;
			$entity->setVolatileData('is_approved', true);

			elgg_trigger_event('publish', 'object', $entity);

			$link = elgg_view('output/url', [
				'text' => $entity->getDisplayName(),
				'href' => $entity->getURL(),
			]);

			$summary = elgg_echo('post:approved:subject', [$link]);
			$subject = strip_tags($summary);
			$message = elgg_echo('post:approved:message', [
				$entity->getDisplayName(),
				$entity->getURL(),
			]);

			notify_user($entity->owner_guid, $user->guid, $subject, $message, [
				'action' => 'approve',
				'object' => $entity,
				'summary' => $summary,
				'url' => $entity->getURL(),
			]);

			$msg = $request->elgg()->echo('post:approve:success');

			return elgg_ok_response('', $msg);

		});

	}
}