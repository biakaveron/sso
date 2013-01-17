<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * OpenID module required.
 * @link https://github.com/biakaveron/openid
 */
abstract class Kohana_SSO_Driver_OpenID extends SSO_Driver {

	/**
	 * @var OpenID
	 */
	protected $_openid;
	protected $_identity_key = 'auth_openid_id';
	protected $_identity;

	protected function _get_identity($id = NULL)
	{
		// this can be changed in child classes
		if (empty($id))
		{
			throw new Kohana_Exception('OpenID identifier required');
		}
		return $id;
	}

	protected function _get_user_data($user)
	{
		$username = trim(str_replace('http://', '', $this->_identity), '/');
		return array(
			'service_id'    => $username,
			'service_name'  => $username,
			'realname'      => Arr::get($user, 'namePerson/friendly'),
			'service_type'  => $this->name,
			'email'         => Arr::get($user, 'contact/email'),
		);
	}

	public $name = 'OpenID';

	public function init()
	{
		$provider = $this->name == 'OpenID' ? NULL : str_replace('OpenID.', '', $this->name);
		$this->_openid = OpenID::factory($provider);
	}

	public function login()
	{
		$this->_openid = func_get_arg(0);
		if ( ! is_object($this->_openid))
		{
			return FALSE;
		}

		$id = $this->_openid->identity();
		$this->_identity = $this->_get_identity($id);
		if ($user = $this->get_user())
		{
			Cookie::set($this->_identity_key, $this->_identity);
			$this->complete_login();
		}

		return $user;
	}

	public function logout()
	{
		Cookie::delete($this->_identity_key);
	}

	public function get_user()
	{
		if ( ! $this->_identity )
		{
			return FALSE;
		}
		$user_data = $this->_get_user_data($this->_openid->attributes());
		return $this->_auth->orm()->get_user($user_data);
	}
}