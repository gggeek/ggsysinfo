<?php
/**
 * Test class for PHP Application
 *
 * @package PhpSecInfo
 * @author Piwik
 */

/**
 * require the PhpSecInfo_Test_Application class
 */
require_once(PHPSECINFO_BASE_DIR.'/Test/Test_Core.php');

/**
 * Test class for PHP application
 *
 * Checks PHP version
 *
 * @package PhpSecInfo
 * @author Piwik
 */
class PhpSecInfo_Test_Core_Php_Version extends PhpSecInfo_Test_Core
{
	var $test_name = "php_version";

	var $recommended_value = null;

	function _retrieveCurrentValue() {
		$this->current_value = PHP_VERSION;

		$url = 'http://php.net/releases/?serialize=1&version=7';
		try {
		    $latestVersion = eZHTTPTool::getDataByURL( $url );
			$versionInfo = unserialize( $latestVersion );
			$this->recommended_value = $versionInfo['version'];
		} catch(Exception $e) {
			$this->recommended_value = '';
		}
	}

	function _execTest() {
		if (version_compare($this->current_value, '5.2.1') < 0) {
			return PHPSECINFO_TEST_RESULT_WARN;
		}

		if (empty($this->recommended_value)) {
			return PHPSECINFO_TEST_RESULT_ERROR;
		}

		if (version_compare($this->current_value, $this->recommended_value) >= 0 ) {
			return PHPSECINFO_TEST_RESULT_OK;
		}

		return PHPSECINFO_TEST_RESULT_NOTICE;
	}

	function _setMessages() {
		parent::_setMessages();

		$this->setMessageForResult(PHPSECINFO_TEST_RESULT_OK, 'en', "You are running PHP ".$this->current_value.
			($this->current_value == $this->recommended_value
			? " (the latest version)."
			: ".  The latest version is ".$this->recommended_value."."));
		$this->setMessageForResult(PHPSECINFO_TEST_RESULT_NOTICE, 'en', "You are running PHP ".$this->current_value.".  The latest version of PHP is ".$this->recommended_value.".");
		$this->setMessageForResult(PHPSECINFO_TEST_RESULT_WARN, 'en', "You are running PHP ".$this->current_value." which is really old. We recommend running the latest (stable) version of PHP which includes numerous bug fixes and security fixes.");
		$this->setMessageForResult(PHPSECINFO_TEST_RESULT_ERROR, 'en', "Unable to determine the latest version of PHP available.");
	}

    function getMoreInfoURL() {
        return 'http://www.php.net';
    }
}
