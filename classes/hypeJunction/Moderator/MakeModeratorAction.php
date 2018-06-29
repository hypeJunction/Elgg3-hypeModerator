<?php

namespace hypeJunction\Moderator;

use Elgg\EntityNotFoundException;
use Elgg\EntityPermissionsException;
use Elgg\Http\ResponseBuilder;
use Elgg\Request;

class MakeModeratorAction {

	/**
	 * Make a moderator
	 *
	 * @param Request $request Request
	 *
	 * @return void
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
		/* @var $svc \hypeJunction\Capabilities\Roles */

		$role = $target instanceof \ElggGroup ? 'group_moderator' : 'moderator';

		$svc->assign($role, $user, $target);

		$msg = $request->elgg()->echo('moderator:make:success');

		return elgg_ok_response('', $msg);
	}
}