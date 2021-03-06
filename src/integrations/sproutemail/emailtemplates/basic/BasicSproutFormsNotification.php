<?php

namespace barrelstrength\sproutforms\integrations\sproutemail\emailtemplates\basic;

use barrelstrength\sproutbase\app\email\base\EmailTemplates;
use Craft;

/**
 * Class BasicSproutFormsNotification
 */
class BasicSproutFormsNotification extends EmailTemplates
{
    /**
     * @return string
     */
    public function getName()
    {
        return Craft::t('sprout-base', 'Basic Notification (Sprout Forms)');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return Craft::getAlias('@barrelstrength/sproutforms/templates/_integrations/sproutemail/emailtemplates/basic');
    }
}



