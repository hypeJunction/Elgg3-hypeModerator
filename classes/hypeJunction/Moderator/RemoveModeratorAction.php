<?php

namespace hypeJunction\Moderator;

use Elgg\EntityNotFoundException;
use Elgg\EntityPermissionsException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class RemoveModeratorAction {

	/**
	 * Make a moderator
	 *
	 * @param Request $request Request
	 *
	 * @return ResponseBuilder
	 * @throws EntityNotFoundException
	 * @throws EntityPermissionsException
	 */
	public function __invoke(Request $request) {

		$user = $request->getEntityParam('guid');
		if (!$user instanceof \ElggUser) {
			throw new EntityNotFoundException();
		}

		$target = $request->getEntityParam('target_guid');
		if (!$target) {
			$target = elgg_get_site_entity();
		}

		if (!$target->canEdit()) {
			throw new EntityPermissionsException();
		}

		$svc = elgg()->roles;
		/* @var $svc \hypeJunction\Capabilities\RolesService */

		$role = $target instanceof \ElggGroup ? 'group_moderator' : 'moderator';

		$svc->unassign($role, $user, $target);

		$msg = $request->elgg()->echo('moderator:remove:success');

		return elgg_ok_response('', $msg);
	}
}