<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_SSO_Token extends ORM {

	protected $_table_name = 'user_tokens';

	protected $_belongs_to = array(
		'user'  => array(
			'model'   => 'auth_data',
			'foreign' => 'user_id'
		),
	);

	public function is_valid()
	{
		return $this->loaded() AND $this->expires > time() AND $this->user_agent == sha1(Request::$user_agent);
	}

	protected function _generate_token_value()
	{
		do
		{
			$token = sha1(uniqid(Text::random('alnum', 32), TRUE));
		}
		while(count(
			DB::select()
				->from($this->table_name())
				->where('token', '=', $token)
				->execute($this->_db)
			) > 0
		);

		return $token;
	}

	public function generate($lifetime)
	{
		$this->expires = time() + $lifetime;
		$this->token = $this->_generate_token_value();
		if ( ! $this->user_agent )
		{
			// this is a new token, so we dont need to save it (yet)
			$this->user_agent = sha1(Request::$user_agent);
		}
		else {
			// save new token value & timestamp
			$this->save();
		}
	}
}