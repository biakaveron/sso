<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_SSO_Driver_OpenID_Google extends SSO_Driver_OpenID {

	public $name = 'OpenID.Google';

	protected function _get_user_data($user)
	{
		$result = parent::_get_user_data($user);

		// Google returns contact/email field only
		$result['service_name']   = current(explode('@', $result['email']));
		if (empty($result['realname']))
		{
			$result['realname'] = trim(Arr::get($result, 'namePerson/first') . ' ' . Arr::get($result, 'namePerson/last'));
		}
		return $result;
	}

}