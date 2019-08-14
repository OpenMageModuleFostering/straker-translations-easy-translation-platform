<?php
/**
 * Created by PhpStorm.
 * User: WlliamZhao
 * Date: 30/09/15
 * Time: 4:37 PM
 */
class StrakerTranslations_EasyTranslationPlatform_Model_Job extends Mage_Core_Model_Abstract
{
    protected $_attributes = array();

    protected $_translateFilePath = '/var/straker/';

    protected function _construct()
    {
        $this->_init('strakertranslations_easytranslationplatform/job');
    }

    protected function addProductAttributes($productAttributeIds){

        foreach ($productAttributeIds as $productAttributeId) {
            $this->_attributes[] =
                Mage::getModel('strakertranslations_easytranslationplatform/product_attributes')
                    ->setJobId($this->getId())
                    ->setAttributeId((int) $productAttributeId)
                    ->save();
        }
        return $this;
    }


    protected function addProductIds($productIds){

        $writeConnection = $this->getWriteAdapter();

        $query = 'INSERT INTO `'.Mage::getSingleton('core/resource')->getTableName('strakertranslations_easytranslationplatform/job_product').'`  (`product_id`, `job_id`) VALUES ';
        $queryVals = array();
        foreach ($productIds as $productId) {
            $queryVals[] = '(' . (int) $productId . ', ' . $this->getId() . ')';
        }

        $writeConnection->query( $query . implode(',', $queryVals));

        return $this;

    }

    protected function addProductTranslateOriginal($productAttributeId, $productCollection){


        $writeConnection = $this->getWriteAdapter();

        $query = 'INSERT INTO `'.Mage::getSingleton('core/resource')->getTableName('strakertranslations_easytranslationplatform/product_translate').'`  (`job_id`, `product_id`, `attribute_id`, `original`) VALUES ';
        $queryVals = array();
        $productAttributeCode = Mage::getModel('eav/entity_attribute')->load($productAttributeId)->getAttributeCode();
        foreach ($productCollection as $product) {
            $queryVals[] = '(' . $this->getId() . ', ' . $product->getId() . ', ' . $productAttributeId . ', \'' . addslashes($product->getData($productAttributeCode)). '\')';
        }

        $writeConnection->query( $query . implode(',', $queryVals));

        return $this;
    }

    protected function addCategoryAttributes($categoryAttributeIds){

        foreach ($categoryAttributeIds as $categoryAttributeId) {
            $this->_attributes[] =
                Mage::getModel('strakertranslations_easytranslationplatform/category_attributes')
                    ->setJobId($this->getId())
                    ->setAttributeId((int) $categoryAttributeId)
                    ->save();
        }
        return $this;
    }


    protected function addCategoryIds($categoryIds){

        $writeConnection = $this->getWriteAdapter();

        $query = 'INSERT INTO `'.Mage::getSingleton('core/resource')->getTableName('strakertranslations_easytranslationplatform/job_category').'`  (`category_id`, `job_id`) VALUES ';
        $queryVals = array();
        foreach ($categoryIds as $categoryId) {
            $queryVals[] = '(' . (int) $categoryId . ', ' . $this->getId() . ')';
        }

        $writeConnection->query( $query . implode(',', $queryVals));

        return $this;

    }

    protected function addCategoryTranslateOriginal($categoryAttributeId, $categoryCollection){


        $writeConnection = $this->getWriteAdapter();

        $query = 'INSERT INTO `'.Mage::getSingleton('core/resource')->getTableName('strakertranslations_easytranslationplatform/category_translate').'`  (`job_id`, `category_id`, `attribute_id`, `original`) VALUES ';
        $queryVals = array();
        $categoryAttributeCode = Mage::getModel('eav/entity_attribute')->load($categoryAttributeId)->getAttributeCode();
        foreach ($categoryCollection as $category) {
            $queryVals[] = '(' . $this->getId() . ', ' . $category->getId() . ', ' . $categoryAttributeId . ', \'' . addslashes($category->getData($categoryAttributeCode)). '\')';
        }

        $writeConnection->query( $query . implode(',', $queryVals));

        return $this;
    }

    public function array_to_xml( $data, &$xml_data ) {
        foreach( $data as $key => $value ) {
            if( is_array($value) ) {
                if( is_numeric($key) ){
                    $key = 'item'.$key; //dealing with <0/>..<n/> issues
                }
                $subnode = $xml_data->addChild($key);
                $this->array_to_xml($value, $subnode);
            } else {
                $xml_data->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

    protected function addAttributeTranslateOriginal($attributeData){

        $writeConnection = $this->getWriteAdapter();

        $query = 'INSERT INTO `'.Mage::getSingleton('core/resource')->getTableName('strakertranslations_easytranslationplatform/straker_attribute_translate').'`  (`job_id`, `attribute_id`, `original`) VALUES ';
        $queryVals = array();

        foreach ($attributeData as $attributeId => $translate) {

            $original = array();

            $original['title'] = $translate['label'] ? Mage::getModel('catalog/resource_eav_attribute')->load($attributeId)->getStoreLabel($this->getSourceStore()) : '';


            if ($translate['option'] ){
                $attributeOptioinCollection = Mage::getModel('eav/entity_attribute_option')
                    ->getCollection()
                    ->setStoreFilter($this->getSourceStore())
                    ->setAttributeFilter($attributeId);

                foreach ($attributeOptioinCollection as $attributeOptioin) {
                    $original['option']['id_'.$attributeOptioin->getoptionId()] = $attributeOptioin->getValue();
                }
            }

            $xml = new SimpleXMLElement('<attribute/>');

            $this->array_to_xml($original, $xml);


            $queryVals[] = '(' . $this->getId() . ',  ' . $attributeId . ', \'' . addslashes($xml->asXML()). '\')';



        }

        $writeConnection->query( $query . implode(',', $queryVals));

        return $this;



    }




    protected function addAttributeIds($attributeData){


        $writeConnection = $this->getWriteAdapter();

        $query = 'INSERT INTO `'.Mage::getSingleton('core/resource')->getTableName('strakertranslations_easytranslationplatform/job_attribute').'`  (`attribute_id`, `translate_lable`, `translate_option`, `job_id` ) VALUES ';
        $queryVals = array();

        foreach ($attributeData as $attributeId => $translate) {

            $queryVals[] = '(' . (int) $attributeId . ', '. $translate['label'] .', '. $translate['option'] .', '. $this->getId() . ')';
        }

        $writeConnection->query( $query . implode(',', $queryVals));

        return $this;

    }


    public function addProducts($productAttributeIds,$productIds){

        if (!$this->getId()){
            if (!$this->getStoreId()){
                Mage::throwException('Error: Missing Store Id');
            }
            $this->setSourceStore(Mage::getStoreConfig('straker/general/source',$this->getStoreId()));
            $this->save();
        }

        $this->addProductAttributes($productAttributeIds);

        $countProducts = sizeof($productIds);
        $buffer = 1000;
        $_productIdSet = array();
        for ($i = 0; $i < $countProducts; $i = $i + $buffer){
            $_productIdSet[] = array_slice($productIds,$i,$buffer);
        }

        foreach ($_productIdSet as $_productIds) {
            $this->addProductIds($_productIds);
        }

        foreach ($_productIdSet as $_productIds) {

            $productCollection = Mage::getModel('catalog/product')->getCollection()->setStore($this->getSourceStore());

            foreach ($productAttributeIds as $productAttributeId) {

                $productAttributeCode = Mage::getModel('eav/entity_attribute')->load($productAttributeId)->getAttributeCode();
                $productCollection->addAttributeToSelect($productAttributeCode);
            }

            $productCollection->addFieldToFilter('entity_id', array('in'=> $_productIds));

            foreach ( $productAttributeIds as $productAttributeId){
                $this->addProductTranslateOriginal($productAttributeId, $productCollection);
            }
        }
        return $this;
    }

    public function addCategories($categoryAttributeIds,$categoryIds){

        if (!$this->getId()){
            if (!$this->getStoreId()){
                Mage::throwException('Error: Missing Store Id');
            }
            $this->setSourceStore(Mage::getStoreConfig('straker/general/source',$this->getStoreId()));
            $this->save();
        }

        $this->addCategoryAttributes($categoryAttributeIds);

        $countCategories = sizeof($categoryIds);
        $buffer = 1000;
        $_categoryIdSet = array();
        for ($i = 0; $i < $countCategories; $i = $i + $buffer){
            $_categoryIdSet[] = array_slice($categoryIds,$i,$buffer);
        }

        foreach ($_categoryIdSet as $_categoryIds) {
            $this->addCategoryIds($_categoryIds);
        }

        foreach ($_categoryIdSet as $_categoryIds) {

            $categoryCollection = Mage::getModel('catalog/category')->getCollection()->setStore($this->getSourceStore());

            foreach ($categoryAttributeIds as $categoryAttributeId) {

                $categoryAttributeCode = Mage::getModel('eav/entity_attribute')->load($categoryAttributeId)->getAttributeCode();
                $categoryCollection->addAttributeToSelect($categoryAttributeCode);
            }

            $categoryCollection->addFieldToFilter('entity_id', array('in'=> $_categoryIds));

            foreach ( $categoryAttributeIds as $categoryAttributeId){
                $this->addCategoryTranslateOriginal($categoryAttributeId, $categoryCollection);
            }
        }
        return $this;
    }

    public function addAttributes($attributeData){

        if (!$this->getId()){
            if (!$this->getStoreId()){
                Mage::throwException('Error: Missing Store Id');
            }
            $this->setSourceStore(Mage::getStoreConfig('straker/general/source',$this->getStoreId()));
            $this->save();
        }

        $this->addAttributeIds($attributeData);

        $this->addAttributeTranslateOriginal($attributeData);

        return $this;
    }

    protected function _createProductTranslateFile() {

        $_xml = '<?xml version="1.0" encoding="utf-8"?><root>';

        $productTranslateCollection = Mage::getModel('strakertranslations_easytranslationplatform/product_translate')->getCollection($this->getWriteAdapter());
        $productTranslateCollection->addFieldToFilter('job_id',$this->getId());

        $attributeFrontLabel = array();

        foreach ($productTranslateCollection as $productTranslate ){
            if (!isset($attributeFrontLabel[$productTranslate->getAttributeId()])){
                $attributeFrontLabel[$productTranslate->getAttributeId()] =
                    Mage::getModel('eav/entity_attribute')->load($productTranslate->getAttributeId())->getFrontendLabel();
            }

            $_xml .= '<data name="' .$this->getTypeId(). '_' . $this->getStoreId().'_'. $productTranslate->getAttributeId().'_'. $productTranslate->getProductId().'" ' ;
            $_xml .= 'content_context="' . $attributeFrontLabel[$productTranslate->getAttributeId()] . '" ';
            $_xml .= 'content_context_url="'.Mage::getStoreConfig('web/unsecure/base_link_url',$this->getStoreId()).'catalog/category/view/id/'.$productTranslate->getProductId().'" ';
            $_xml .= 'content_id="'. $productTranslate->getId() .'">';
            $_xml .= '<value><![CDATA['.$productTranslate->getOriginal().']]></value></data>';
        }
        $_xml .='</root>';

        file_put_contents(MAGENTO_ROOT.$this->_translateFilePath.'job'.$this->getId().'.xml',$_xml);
        $this->setSourceFile('job'.$this->getId().'.xml')->save() ;

        return $this;
    }

    protected function _createCategoryTranslateFile() {

        $_xml = '<?xml version="1.0" encoding="utf-8"?><root>';

        $categoryTranslateCollection = Mage::getModel('strakertranslations_easytranslationplatform/category_translate')->getCollection();
        $categoryTranslateCollection->addFieldToFilter('job_id',$this->getId());

        $attributeFrontLabel = array();

        foreach ($categoryTranslateCollection as $categoryTranslate ){
            if (!isset($attributeFrontLabel[$categoryTranslate->getAttributeId()])){
                $attributeFrontLabel[$categoryTranslate->getAttributeId()] =
                    Mage::getModel('eav/entity_attribute')->load($categoryTranslate->getAttributeId())->getFrontendLabel();
            }

            $_xml .= '<data name="' .$this->getTypeId(). '_' . $this->getStoreId().'_'. $categoryTranslate->getAttributeId().'_'. $categoryTranslate->getCategoryId().'" ' ;
            $_xml .= 'content_context="' . $attributeFrontLabel[$categoryTranslate->getAttributeId()] . '" ';
            $_xml .= 'content_context_url="'.Mage::getStoreConfig('web/unsecure/base_link_url',$this->getStoreId()).'catalog/category/view/id/'.$categoryTranslate->getCategoryId().'" ';
            $_xml .= 'content_id="'. $categoryTranslate->getId() .'">';
            $_xml .= '<value><![CDATA['.$categoryTranslate->getOriginal().']]></value></data>';
        }
        $_xml .='</root>';

        file_put_contents(Mage::getBaseDir().$this->_translateFilePath.'job'.$this->getId().'.xml',$_xml);
        $this->setSourceFile('job'.$this->getId().'.xml')->save() ;

        return $this;
    }

    protected function _createAttributeTranslateFile() {

        $_xml = '<?xml version="1.0" encoding="utf-8"?><root>';

        $attributeTranslateCollection = Mage::getModel('strakertranslations_easytranslationplatform/attribute_translate')->getCollection();
        $attributeTranslateCollection->addFieldToFilter('job_id',$this->getId());

        foreach ($attributeTranslateCollection as $attributeTranslate ){

            $dataInJson =  json_encode(simplexml_load_string($attributeTranslate->getOriginal()));
            $data = json_decode($dataInJson,true);

            foreach ($data as $k => $attribute ){
                if ($k == 'title' && $attribute){
                    $_xml .= '<data name="' .$this->getTypeId(). '_' . $this->getStoreId().'_'. $attributeTranslate->getAttributeId().'" ' ;
                    $_xml .= 'content_context="product attribute title" ';
                    $_xml .= 'content_id="'. $attributeTranslate->getId() .'">';
                    $_xml .= '<value><![CDATA['.$attribute.']]></value></data>';
                }

                if ($k = 'option'){
                    foreach ($attribute as $optionId => $optionValue) {
                        $_xml .= '<data name="' .$this->getTypeId(). '_' . $this->getStoreId().'_'. $attributeTranslate->getAttributeId().'" ' ;
                        $_xml .= 'content_context="product attribute option" ';
                        $_xml .= 'option_id="'. $optionId .'" ';
                        $_xml .= 'content_id="'. $attributeTranslate->getId() .'">';
                        $_xml .= '<value><![CDATA['.$optionValue.']]></value></data>';
                    }

                }

            }
        }
        $_xml .='</root>';

        file_put_contents(Mage::getBaseDir().$this->_translateFilePath.'job'.$this->getId().'.xml',$_xml);
        $this->setSourceFile('job'.$this->getId().'.xml')->save() ;

        return $this;
    }

    protected function _summitJob(){

        $request = array();

        if (!$this->getTitle()){
            $store = Mage::getModel('core/store')->load($this->getStoreId());
            $defaultTitle = $store->getFrontendName().'_'.$store->getName().'_'.Mage::getModel('core/date')->timestamp();
            $this->setTitle($defaultTitle);
        }
        $request['title'] = $this->getTitle();
        $request['sl']    = $this->getSl();
        $request['tl']    = $this->getTl();

        $filePath = MAGENTO_ROOT.$this->_translateFilePath.$this->getSourceFile();

        $request['source_file']    = function_exists('curl_file_create') ?  curl_file_create($filePath) :'@'.$filePath;
        $request['callback_uri']    = Mage::getStoreConfig('web/unsecure/base_link_url',$this->getStoreId()) . 'straker/callback';
        $request['token']    = $this->getId();

        $api = $this->_getApi();
        $response = $api->callTranslate($request);
        if($response->job_key) {
            $this->setStatusId(2)
                ->setJobKey($response->job_key)
                ->setTjNumber($response->tj_number)
                ->save();
            $this->setLastStatus(1);
        }
        else{
            $this->setLastStatus(0);
            $message = $response->magentoMessage?$response->magentoMessage:'Unknown Error.';
            $this->setLastMessage($message);
        }
        return $this;

    }

    public function submitProducts($productAttributeIds,$productIds){

        //product
        $this->setTypeId(1);
        $this->addProducts($productAttributeIds, $productIds)
            ->_createProductTranslateFile()
            ->_summitJob();
        return $this;
    }

    public function submitCategories($categoryAttributeIds,$categoryIds){

        //category
        $this->setTypeId(3);
        $this->addCategories($categoryAttributeIds, $categoryIds)
            ->_createCategoryTranslateFile()
            ->_summitJob();
        return $this;
    }

    public function submitAttributes($attributeData){

        //category
        $this->setTypeId(4);
        $this->addAttributes($attributeData)
            ->_createAttributeTranslateFile()
            ->_summitJob();
        return $this;
    }

    public function updateQuote(){

        if ($this->getJobKey()){
            $request = array();
            $request['job_key'] = $this->getJobKey();
            $api = $this->_getApi();
            $response = $api->getTranslation($request);

            if ($response->job) {
                foreach ($response->job as $job){
                    if ($job->token == $this->getId()) {
                        $quote = $job->quotation;
                        if ($quote && $quote != $this->getQuote()) {
                            $this->setQuote($quote)->save();
                            return true;
                        }
                    }
                }
            }
        }
        return false;

    }

    public function updateTranslation(){
        $updateFlag = false;
        if ($this->getJobKey()){
            $request = array();
            $request['job_key'] = $this->getJobKey();
            $api = $this->_getApi();
            $response = $api->getTranslation($request);
            if ($response->job) {
                foreach ($response->job as $job) {

                    if ($job->token == $this->getId()) {
                        $jobStatusId = $this->_getStatusId($job->status);

                        if ($jobStatusId && $job->status <> $this->getStatusName()) {
                            $this->setStatusId($jobStatusId)->save();
                            $updateFlag = true;
                        }

                        if ($job->tj_number && $job->tj_number <> $this->getTjNumber()) {
                            $this->setTjNumber($job->tj_number)->save();
                            $updateFlag = true;
                        }

                        if ($job->workflow && $job->workflow <> $this->getWorkFlow()) {
                            $this->setWorkFlow($job->workflow)->save();
                            $updateFlag = true;
                        }

                        if ($job->quotation && $job->quotation <> $this->getQuote()) {
                            $this->setQuote($job->quotation)->save();
                            $updateFlag = true;
                        }
                    }

                    foreach ($job->translated_file as $file) {

                        if ($file->download_url && !$this->getDownloadUrl()) {
                            $this->setDownloadUrl($file->download_url)->save();
                            $this->_importTranslation();
                            $updateFlag = true;
                        }
                    }
                }
                $this->setLastStatus(1);
            }
            else{
                $this->setLastStatus(0);
                $message = $response->magentoMessage?$this->setLastMessage($response->magentoMessage):'Unknown Error.';
                $this->setLastMessage($message);
            }

        }
        return $updateFlag;
    }

    protected function _getApi(){

        return Mage::getModel('strakertranslations_easytranslationplatform/api',array('store'=>$this->getStoreId()));
    }

    protected function _getStatusId($statusName) {

        return Mage::getModel('strakertranslations_easytranslationplatform/job_status')->load($statusName,'status_name')->getId();
    }

    protected function _importTranslation() {

        $xml = $this->_getApi()->getTranslatedFile($this->getDownloadUrl());

        file_put_contents(Mage::getBaseDir().$this->_translateFilePath.'translated_job'.$this->getId().'.xml',$xml, LOCK_EX);

        $data = simplexml_load_string($xml);

        $_translationValueGroup = array();

        foreach ($data->children() as $_translation) {

            $_entityTranslationId = (string) $_translation->attributes()->content_id;
            if ($this->_getType() == 'attribute'){
                if ($_translation->attributes()->content_context =="product attribute title"){
                    $_translationValueGroup[$_entityTranslationId]['title'] = (string) $_translation->value;
                }

                if ($_translation->attributes()->content_context =="product attribute option"){
                    $_translationValueGroup[$_entityTranslationId]['option']['id_'. $_translation->attributes()->option_id] = (string) $_translation->value;
                }

            }
            else {
                $_translationValueGroup[$_entityTranslationId] = (string)$_translation->value;
            }

        }

        foreach ($_translationValueGroup as $content_id => $_translationValue ){

            $value = $_translationValue;

            if (is_array($_translationValue)){
                $xml = new SimpleXMLElement('<attribute/>');
                $this->array_to_xml($_translationValue,$xml);
                $value = (string) $xml->asXML();
            }


            $_entityTranslation = Mage::getModel('strakertranslations_easytranslationplatform/'.$this->_getType().'_translate')->load($content_id);
            $_entityTranslation->setTranslate($value);
            $_entityTranslation->save();
            $_entityTranslation->clearInstance();
        }

        return $this->setDownloadedVersion(1)->save();

    }

    protected function _getType(){
        return strtolower($this->getTypeName());
    }

    public function updatePayment(){

        if ($this->getJobKey()){
            $request = array();
            $request['job_key'] = $this->getJobKey();
            $api = $this->_getApi();
            $response = $api->getPayment($request);

            if(!empty($response) && $response->status == "Paid"){ ///////waiting for payment api
                $this->setPaymentStatus(1)->save();
                return true;
            }
        }
        return false;
    }

    public function applyTranslation( $entityIds = array()) {

        $collection = Mage::getModel('strakertranslations_easytranslationplatform/'.$this->_getType().'_translate')->getCollection()->addFieldToFilter('job_id',$this->getId());

        if ($entityIds) {
            $collection->addFieldToFilter($this->_getType().'_id', array('in' => $entityIds));
        }

        $updatedIds = array();
        $writeConnection = $this->getWriteAdapter();

        foreach ($collection as $translation) {
            $translation->setStoreId($this->getStoreId())->importTranslation();

            $entityId= call_user_func(array($translation, 'get'.$this->getTypeName().'Id'));

            if (!$updatedIds[$entityId]){
                $updatedIds[$entityId] = true;
                $prefix = Mage::getConfig()->getTablePrefix()->__toString();
                $writeConnection->update($prefix.'straker_job_'.$this->_getType() ,array('version' => 1), $this->_getType()."_id = {$entityId} and job_id ={$this->getId()}"  );
            }
        }

        return true;
    }

    public function isPublished()
    {
        if ($this->getStatusId() == 5) {
            return true;
        } elseif($this->getStatusId() == 4) {
            return $this->updatePublishedStatus()->getStatus() == 5;
        }
        else{
            return false;
        }
    }

    protected  function updatePublishedStatus(){
        $collection = Mage::getModel('strakertranslations_easytranslationplatform/job_'.$this->_getType())->getCollection()->addFieldToFilter('job_id',$this->getId());
        $collection->addFieldToFilter('version',
            array(
                array('neq' => '1'),
                array('null' => true)
            )
        );
        if(!$collection->getFirstItem()->getId()){
            $this->setStatusId(5)->save();
        }
        return $this;
    }

    public function submitSupport(array $data){

        $res = $this->_getApi()->callSupport($data);

        return $res->success;

    }

    public function checkAndCreateFolder(){

        $ioAdapter = new Varien_Io_File();
        try {
            $ioAdapter->checkAndCreateFolder(Mage::getBaseDir('var').DS.'straker');
        }
        catch (Exception $e) {
            Mage::throwException($e->getMessage());
        }

        return $this;

    }

    protected function getWriteAdapter(){
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }
}