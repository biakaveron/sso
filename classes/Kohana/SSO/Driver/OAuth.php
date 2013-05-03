<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * OAuth module required
 * @link https://github.com/kohana/oauth
 */
abstract class Kohana_SSO_Driver_OAuth extends SSO_Driver {

	/**
	 * @var OAuth_Provider
	 */
	protected $_provider;
	/**
	 * @var OAuth_Consumer
	 */
	protected $_consumer;
	/**
	 * @var OAuth_Token_Access
	 */
	protected $_token;
	protected $_token_key = 'auth_oauth_token';

	abstract protected function _get_user_data($user);
	abstract protected function _url_verify_credentials(OAuth_Token_Access $token);

	protected function _verify_credentials(OAuth_Token $token, OAuth_Consumer $consumer)
	{
		$request = OAuth_Request::factory('Credentials', 'GET', $this->_url_verify_credentials($token), array(
			'oauth_consumer_key' => $consumer->key,
			'oauth_token' => $token->token,
		));

		$response = $request->sign($this->_provider->signature, $consumer, $token)
		                    ->execute(array(CURLOPT_FOLLOWLOCATION => TRUE));
		return $this->_get_user_data($response);
	}

	public $name = 'OAuth';

	public function init()
	{
		$this->_consumer = OAuth_Consumer::factory(Kohana::$config->load('oauth.'.$this->_provider));
		$this->_provider = OAuth_Provider::factory($this->_provider);
		if ($token = Cookie::get($this->_token_key))
		{
			$this->_token = unserialize($token);
		}
	}

	public function login()
	{
		$this->_token = func_get_arg(0);
		if ($user = $this->get_user())
		{
			Cookie::set($this->_token_key, serialize($this->_token));
			// successfully logged in
			$this->complete_login();
		}
		return $user;
	}

	public function logout()
	{
		Cookie::delete($this->_token_key);
	}

	public function get_user()
	{
		if ( ! $this->_token )
		{
			return FALSE;
		}
		// get user info from OAuth service
		$user = $this->_verify_credentials($this->_token, $this->_consumer);
		return $this->_auth->orm()->get_user($user);
	}

	/**
	 * получение URL для авторизации
	 */
	public function authorize_url(OAuth_Token_Request $token)
	{
		return $this->_provider->authorize_url($token, $this->_request_params);
	}

	public function callback($callback)
	{
		$this->_consumer->callback($callback);
		return $this;
	}

	/**
	 * Меняет токен запроса на токен доступа
	 *
	 * @param OAuth_Token_Request $token
	 *
	 * @return OAuth_Token_Access
	 */
	public function access_token(OAuth_Token_Request $token)
	{
		return $this->_provider->access_token($this->_consumer, $token);
	}

	/**
	 * Возвращает Request Token
	 *
	 * @return OAuth_Token_Request
	 */
	public function request_token()
	{
		return $this->_provider->request_token($this->_consumer, $this->_request_params);
	}


}