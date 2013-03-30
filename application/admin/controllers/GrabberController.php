<?php


/**
 * Grabber controller
 *
 */
class Admin_GrabberController extends Zend_Controller_Action
{
	
	public function init()
	{
		$this->view->headTitle('DENKMAL.ORG Admin');
	}
	
	
	/**
	 * Index page
	 */
	public function indexAction() {
		require_once 'Grabber/Calendar/Programmzeitung.php';
		$grabber = new Grabber_Calendar_Programmzeitung();
		$date = Zend_Date::now();
		for ($i=0; $i<Zend_Registry::get('config')->grabber->days; $i++) {
			echo $grabber->grab($date) . '<br/>';
			$date->addDay(1);
		}
		
		
		require_once 'Grabber/Location/Funambolo.php';
		$grabber = new Grabber_Location_Funambolo();
		echo $grabber->grab() . '<br/>';
		
		require_once 'Grabber/Location/Chezsoif.php';
		$grabber = new Grabber_Location_Chezsoif();
		echo $grabber->grab() . '<br/>';
		
		require_once 'Grabber/Location/Tikibar.php';
		$grabber = new Grabber_Location_Tikibar();
		echo $grabber->grab() . '<br/>';
		
		require_once 'Grabber/Location/Hinterhof.php';
		$grabber = new Grabber_Location_Hinterhof();
		echo $grabber->grab() . '<br/>';
		
		require_once 'Grabber/Location/HinterhofDachterrasse.php';
		$grabber = new Grabber_Location_HinterhofDachterrasse();
		echo $grabber->grab() . '<br/>';
		
		require_once 'Grabber/Location/Kaserne.php';
		$grabber = new Grabber_Location_Kaserne();
		echo $grabber->grab() . '<br/>';
		
		require_once 'Grabber/Location/Cafehammer.php';
		$grabber = new Grabber_Location_Cafehammer();
		echo $grabber->grab() . '<br/>';

		require_once 'Grabber/Location/Agora.php';
		$grabber = new Grabber_Location_Agora();
		echo $grabber->grab() . '<br/>';

		require_once 'Grabber/Location/Nordstern.php';
		$grabber = new Grabber_Location_Nordstern();
		echo $grabber->grab() . '<br/>';
	}
	
	/**
	 * Test page
	 */
	public function testAction() {
		$config = Zend_Registry::get('config');
		$config->grabber->debug = true;
		Zend_Registry::set('config', $config);
		
		require_once 'Grabber/Location/Nordstern.php';
		$grabber = new Grabber_Location_Nordstern();
		echo $grabber->grab() . '<br/>';
	}

}
