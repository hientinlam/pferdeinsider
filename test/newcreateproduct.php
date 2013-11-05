<?php
    error_reporting(E_ALL | E_STRICT);
    $mageFilename = '../app/Mage.php';
    require_once $mageFilename;
    $app = Mage::app('default');
     
    ini_set('display_errors', 1);
 
    
 
    $api = new Mage_Catalog_Model_Product_Api();
     
    $attribute_api = new Mage_Catalog_Model_Product_Attribute_Set_Api();
    $attribute_sets = $attribute_api->items();
     
    $productData = array();
    $productData['website_ids'] = array(1);
    $productData['categories'] = array(5);
 
    $productData['status'] = 1;
    $productData['type_id']='downloadable';
    $productData['producttype'] = 'video';
    $productData['productexpertname'] = '';
    $productData['name'] = 'test12';
    $productData['description'] = 'test description';
    $productData['short_description'] = 'test short description';
    $productData['sku'] = time();
    $productData['price'] = 7878;
    $productData['specialprice'] = 77;
    $productData['qty'] = 6;
    $productData['productcategory'] = 5;
    $productData['downloadableoption'] = 3;
    $productData['productmaterial'] = 7;
    $productData['specialproduct'] = 47;
    $productData['sampleurl'] ='';
    $productData['linkurl'] = '';
     
   
    $productData['weight'] = 23.25;
    $productData['tax_class_id'] =2;
    $productData['page_layout'] ='two_columns_left';
         
    $new_product_id = $api->create('simple',$attribute_sets[0]['set_id'],'ND3',$productData);
     
    //print_r($new_product_id);
     
    $stockItem = Mage::getModel('cataloginventory/stock_item');
    $stockItem->loadByProduct( $new_product_id );
     
    $stockItem->setData('use_config_manage_stock', 1);
    $stockItem->setData('qty', 100);
    $stockItem->setData('min_qty', 0);
    $stockItem->setData('use_config_min_qty', 1);
    $stockItem->setData('min_sale_qty', 0);
    $stockItem->setData('use_config_max_sale_qty', 1);
    $stockItem->setData('max_sale_qty', 0);
    $stockItem->setData('use_config_max_sale_qty', 1);
    $stockItem->setData('is_qty_decimal', 0);
    $stockItem->setData('backorders', 0);
    $stockItem->setData('notify_stock_qty', 0);
    $stockItem->setData('is_in_stock', 1);
    $stockItem->setData('tax_class_id', 0);
     
    $stockItem->save();
    
   
        $filePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
        $linkfile = array();
        $samplefile = array();
        $files ='inde11x.jpg';
        $var = rand(0,99);
        $album_name = $files;
        $fname = $files;
        $_highfilePath = "/highresolution/".$album_name."/" . $fname;
        $_samplefilePath = "/lowresolution/".$album_name."/" . $fname;
            
        $paths = array('highurl' => $_highfilePath, 'sampleurl' => $_samplefilePath);
        $samplefile[] = array(
                'file' => $_samplefilePath,
                'name' => $fname,
              //  'size' => $files['size'][0],
                'status' => 'new'
        );

        $linkfile[] = array(
                'file' => $_highfilePath,
                'name' => $fname,
                //'size' => $files['size'][0],
                'status' => 'new'
        );

        $tmpBasePath = Mage::getBaseDir('media') . DS . 'highresolution' . DS . $album_name;
        $tmpSampleBasePath = Mage::getBaseDir('media') . DS . 'lowresolution' . DS . $album_name;

        $BashPathUrl = $filePath.'highresolution/'.$album_name.'/'.$fname;
        $SamplePathUrl = $filePath.'lowresolution/'.$album_name.'/'.$fname;
        $product = Mage::getModel('catalog/product')->load($new_product_id);
        $product->addImageToMediaGallery($tmpSampleBasePath. DS. $fname, array ('image','small_image','thumbnail'), false, false);
        $product->save();
        
        $linkFileName = Mage::helper('downloadable/file')->moveFileFromTmp(
                Mage_Downloadable_Model_Link::getBaseTmpPath(),
                Mage_Downloadable_Model_Link::getBasePath(),
                $linkfile
        );

        $linkModel = Mage::getModel('downloadable/link')->setData(array(
                'product_id' => $new_product_id,
                'sort_order' => 0,
                'number_of_downloads' => 0, // Unlimited downloads
                'is_shareable' => 2, // Not shareable
                'link_url' => '',
                'link_type' => 'file',
                'link_file' => json_encode($linkfile),
                'sample_url' => $SamplePathUrl,
                'sample_file' => json_encode($samplefile),
                'sample_type' => 'url',
                'use_default_title' => false,
                'title' => 'downloadable link',
                'default_price' => 0,
                'price' => 0,
                'store_id' => 0,
                'website_id' => 1,
        ));

        $linkModel->setLinkFile($linkFileName)->save();
                    echo $new_product_id;
                    
     /*
    $product = Mage::getModel('catalog/product')->load($new_product_id);
     
    $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
    $product->addImageToMediaGallery ('D:\wamp\www\magento\media\import\Bamboo.jpg', array ('image','small_image','thumbnail'), false, false);
    $product->addImageToMediaGallery ('D:\wamp\www\magento\media\import\Bamboo.jpg', array ('image','small_image','thumbnail'), false, false);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
    $product->save();
     
     
    $product = Mage::getModel('catalog/product')->load($new_product_id);
    $optionData =   array(
                        "title" => "Custom Text Field Option Title 1",
                        "type" => "field",
                        "is_require" => 1,
                        "sort_order" => 1,
                        "price" => 0,
                            "price_type" => "fixed",
                            "sku" => "",
                            "max_characters" => 15
                    );
     
    $product->setHasOptions(1);
    $option = Mage::getModel('catalog/product_option')
              ->setProductId($new_product_id)
              ->setStoreId(1)
              ->addData($optionData);
    $option->save();
    $product->addOption($option);
     
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
    $product->save();
     
    $product = Mage::getModel('catalog/product')->load($new_product_id);
    $optionData =   array(
                        "title" => "Custom Text Field Option Title 2",
                        "type" => "field",
                        "is_require" => 1,
                        "sort_order" => 2,
                        "price" => 0,
                            "price_type" => "fixed",
                            "sku" => "",
                            "max_characters" => 25
                    );
     
    $product->setHasOptions(1);
    $option = Mage::getModel('catalog/product_option')
              ->setProductId($new_product_id)
              ->setStoreId(1)
              ->addData($optionData);
    $option->save();
    $product->addOption($option);
     
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
    $product->save();
    */
?>