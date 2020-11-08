<?php
App::uses('AppModel', 'Model');

class Portal extends AppModel
{
	const PORTAL_MAIN = 1;
    const PORTAL_VA = 2;
    const PORTAL_ACCOUNT_REVIEWS = 3;

	/*
     * static enum: Model::function()
     * @access static
     */
    public static function portals($value = null) {
        $options = array(
            self::PORTAL_MAIN => __('Main')
        );

        if (AppModule::loaded('VendorAssessments')) {
            $options[self::PORTAL_VA] = __('Online Assessment');
        }

        if (AppModule::loaded('AccountReviews')) {
            $options[self::PORTAL_ACCOUNT_REVIEWS] = __('Account Reviews');
        }

        return parent::enum($value, $options);
    }

    public function getList()
    {
        return self::portals();
    }
}
