<?php
class StrakerTranslations_EasyTranslationPlatform_Block_Adminhtml_Job_Category extends Mage_Adminhtml_Block_Widget_Container{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('straker/job/category.phtml');
    }

    protected function _prepareLayout()
    {
        $jobId = $this->getRequest()->getParam('job_id');
        $jobStatus = Mage::getModel('strakertranslations_easytranslationplatform/job')->load($jobId)->getStatusId();
        if ( $jobStatus == '4'){
            $this->_addButton('publish', array(
                'label'   => Mage::helper('catalog')->__('Publish Translation'),
                'onclick' => "setLocation('{$this->getUrl('*/*/copyAll',array('job_id'=>$jobId))}');",
                'class'   => 'task'
            ));
        }

        $this->setChild('grid', $this->getLayout()->createBlock('strakertranslations_easytranslationplatform/adminhtml_job_category_grid', 'job_category.grid'));
        $this->getChild('grid')->setStatusId($jobStatus);
        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}