<?php
Pluf::loadFunction ( 'Pluf_HTTP_URL_urlForView' );
Pluf::loadFunction ( 'Pluf_Shortcuts_GetObjectOr404' );
Pluf::loadFunction ( 'Pluf_Shortcuts_GetFormForModel' );

/**
 * لایه نمایش مدیریت کاربران را به صورت پیش فرض ایجاد می‌کند
 *
 * @author maso
 *        
 */
class User_Views_User {
	
	/**
	 * پیش نیازهای حساب کاربری
	 *
	 * @var unknown
	 */
	public $account_precond = array (
			'Pluf_Precondition::loginRequired' 
	);
	
	/**
	 * به روز رسانی و مدیریت اطلاعات خود کاربر
	 *
	 * @param unknown_type $request
	 * @param unknown_type $match
	*/
	public function account($request, $match) {
		if($request->method === 'GET'){
			return new Pluf_HTTP_Response_Json ( $cuser );
		}
		if($request->method === 'POST'){
			// initial page data
			$extra = array ();
			$form = new User_Form_Account ( array_merge ( $request->POST, $request->FILES ), $extra );
			$cuser = $form->save ();
			$request->user->setMessage ( sprintf ( __ ( 'Account data has been updated.' ), ( string ) $cuser ) );
		
			// Return response
			return new Pluf_HTTP_Response_Json ( $cuser );
		}

		throw new Pluf_Exception_NotImplemented ();
	}
	
	
	/**
	 * پیش نیازهای ثبت کاربران
	 *
	 * @var unknown
	 */
	public $signup_precond = array ();
	
	/**
	 * ثبت کاربران
	 *
	 * @param unknown_type $request        	
	 * @param unknown_type $match        	
	 */
	public function signup($request, $match) {
		// initial page data
		$extra = array ();
		$form = new User_Form_User ( array_merge ( $request->POST, $request->FILES ), $extra );
		$cuser = $form->save ();
		$request->user->setMessage ( sprintf ( __ ( 'The user %s has been created.' ), ( string ) $cuser ) );
		
		// Return response
		return new Pluf_HTTP_Response_Json ( $cuser );
	}
	
	/**
	 * پیش نیازهای دسترسی به فهرست کاربران
	 *
	 * @var unknown
	 */
	public $users_precond = array (
			'Pluf_Precondition::staffRequired' 
	);
	
	/**
	 * فهرست تمام کاربران را نمایش می‌دهد
	 *
	 * @param unknown_type $request        	
	 * @param unknown_type $match        	
	 */
	public function users($request, $match) {
		throw new Pluf_Exception_NotImplemented ();
	}
	
	/**
	 * پیش نیازهای فهرست کردن کاربران
	 *
	 * @var unknown
	 */
	public $user_precond = array (
			'Pluf_Precondition::staffRequired' 
	);
	
	/**
	 * مدیریت یک کاربر را در سیستم ایجاد می‌کند
	 *
	 * @param unknown_type $request        	
	 * @param unknown_type $match        	
	 */
	public function user($request, $match) {
		$user_id = $match[1];
		if($user_id === $request->user->id){
			return $this->account($request, $match);
		}
		throw new Pluf_Exception_NotImplemented ();
	}
}
