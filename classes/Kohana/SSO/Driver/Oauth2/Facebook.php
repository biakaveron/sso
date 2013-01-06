<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Kohana_SSO_Driver_OAuth2_Facebook extends SSO_Driver_OAuth2 {

	protected $_provider = 'facebook';

	/**
	 * @param   string  $user object (response from provider)
	 * @return  Array
	 */
	protected function _get_user_data($user)
	{
		$user = json_decode($user);

		if ( ! isset($user->nickname) || empty($user->nickname))
		{
			$user->nickname = $user->name;
		}

		return array(
			'service_id'    => $user->id,
			'service_name'  => $user->nickname,
			'realname'      => $user->name,
			'service_type'  => 'oauth2.facebook',
			'email'         => $user->email,
			// @see http://developers.facebook.com/docs/reference/api/#pictures
			'avatar'        => 'https://graph.facebook.com/' . $user->id . '/picture?type=normal',
		);
	}

	protected function _url_verify_credentials()
	{
		return 'https://graph.facebook.com/me';
	}

}
