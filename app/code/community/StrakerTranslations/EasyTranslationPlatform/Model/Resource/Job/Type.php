<?php
/**
 * Created by PhpStorm.
 * User: WlliamZhao
 * Date: 30/09/15
 * Time: 8:41 PM
 */ 
class StrakerTranslations_EasyTranslationPlatform_Model_Resource_Job_Type extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('strakertranslations_easytranslationplatform/job_type', 'type_id');
    }

}