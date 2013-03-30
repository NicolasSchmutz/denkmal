<?php


/**
 * Login and Logout functions
 *
 */
class Auth {

	/**
	 * Login
	 * Log in with the given credentials.
	 *
	 * @param string $user User
	 * @param string $pass Password
	 * @return boolean True on success
	 * @throws Denkmal_Exception On error
	 */
	public static function login($user, $pass) {
		if (empty($user)) {
			throw new Denkmal_Exception('Kein User angegeben');
		}

		if (false != ($authAdapter = self::checkLogin($user, $pass))) {
			// Login successful - write data to session
			$data = $authAdapter->getResultRowObject(array('user', 'role'));
			Zend_Auth::getInstance()->getStorage()->write($data);

			// Set cookie to remember session
			Zend_Session::rememberMe(60 * 60 * 24 * 365 * 10);
			return true;
		}

		throw new Denkmal_Exception('Login fehlgeschlagen');
	}

	/**
	 * Logout
	 *
	 * Logs the current user out.
	 */
	public static function logout() {
		Zend_Auth::getInstance()->clearIdentity();
		// Let session end when browser is closed
		Zend_Session::forgetMe();
	}

	/**
	 * Authenticate against a set of login-credentials
	 *
	 * @param string $user User
	 * @param string $pass Password
	 * @return mixed If the authentification succeeds this returns the created Zend_Auth_Adapter_DbTable, otherwise 'false' is returned.
	 */
	public static function checkLogin($user, $pass) {
		// Setup Zend_Auth adapter for a database table
		$db = Denkmal_Db::get();
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		$authAdapter->setTableName('user');
		$authAdapter->setIdentityColumn('user');
		$authAdapter->setCredentialColumn('pass');
		$authAdapter->setCredentialTreatment('?');

		// Set the input credential values to authenticate against
		$authAdapter->setIdentity($user);
		$authAdapter->setCredential($pass);

		// Do the authentication
		try {
			$result = $authAdapter->authenticate();
			if ($result->isValid()) {
				return $authAdapter;
			}
		} catch (Exception $e) {
			return false;
		}

		return false;
	}
}
