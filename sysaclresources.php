<?php

// test commit for branch slim2
require 'vendor/autoload.php';




/* $app = new \Slim\Slim(array(
  'mode' => 'development',
  'debug' => true,
  'log.enabled' => true,
  )); */


$app = new \Slim\SlimExtended(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    'log.level' => \Slim\Log::INFO,
    'exceptions.rabbitMQ' => true,
    'exceptions.rabbitMQ.logging' => \Slim\SlimExtended::LOG_RABBITMQ_FILE,
    'exceptions.rabbitMQ.queue.name' => \Slim\SlimExtended::EXCEPTIONS_RABBITMQ_QUEUE_NAME
        ));

/**
 * "Cross-origion resource sharing" kontrolüne izin verilmesi için eklenmiştir
 * @author Mustafa Zeynel Dağlı
 * @since 2.10.2015
 */
$res = $app->response();
$res->header('Access-Control-Allow-Origin', '*');
$res->header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");

//$app->add(new \Slim\Middleware\MiddlewareTest());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());

 

/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkFillComboBoxMainResources_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

     $resCombobox = $BLL->fillComboBoxMainResources();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => 'open',
            "checked" => false,
            "attributes" => array("notroot" => true, "active" => $flow["active"], "deleted" => $flow["deleted"]),
        );
    }
    $app->response()->header("Content-Type", "application/json");
    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */
    $app->response()->body(json_encode($flows));
});
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkFillComboBoxFullResources_sysAclResources/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    $resCombobox = $BLL->fillComboBoxFullResources();

    $flows = array();
    foreach ($resCombobox as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            //"text" => strtolower($flow["name"]),
            "text" => $flow["name"],
            "state" => 'closed',
            "checked" => false,
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($flows));
});
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkFillGrid_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
    //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];
   

    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'],
        'search_name' => $_GET['searchname'],
        'pk' => $pk));
    //print_r($resDataGrid);

    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */
    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "name" => $flow["name"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "start_date" => $flow["start_date"],
            "end_date" => $flow["end_date"],
            "parent" => $flow["parent"],
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "root_parent" => $flow["root_parent"],
            "root" => $flow["root"],
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
});
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkInsert_sysAclResources/", function () use ($app ) {
    
    ///////////////////**** validator 
     /**
     * validat chain test
     * @author Mustafa Zeynel Dağlı
     * @since 15/01/2016
     */
    
    $vName = $_GET['name'];
    $vUrl = $_GET['url'];
    $vParent = $_GET['parent'];
    $vIconClass = $_GET['icon_class'];
    $vDescription = $_GET['description']; 
    $vUserId =$_GET['user_ID'];
    
    
    $validater = $app->getServiceManager()->get('validationChainerServiceForZendChainer');    
    $validatorChainUrl = new Zend\Validator\ValidatorChain();
    $validater->offsetSet(array_search($_GET['url'], $_GET), 
            new \Utill\Validation\Chain\ZendValidationChainer($app, 
                                                              $_GET['url'], 
                                                              $validatorChainUrl->attach(
                                                                        new Zend\Validator\StringLength(array('min' => 6,
                                                                                                              'max' => 50)))
                                                                              // ->attach(new Zend\I18n\Validator\Alnum())    
                    ) );
 
     
    $validatorChainName = new Zend\Validator\ValidatorChain();
    $validater->offsetSet('name', 
            new \Utill\Validation\Chain\ZendValidationChainer($app, 
                                                              $vName, 
                                                              $validatorChainName->attach(
                                                                        new Zend\Validator\StringLength(array('min' => 3,
                                                                                                              'max' => 10)))
                                                                              ->attach(new Zend\I18n\Validator\Alnum())    
                    ) );
  
    $validater->offsetSet('parent', 
        new \Utill\Validation\Chain\ZendValidationChainer($app, 
                                                          $vParent, 
                                                          $validatorChainName->attach(
                                                                    new Zend\Validator\StringLength(array('min' => 3,
                                                                                                          'max' => 10)))
                                                                          ->attach(new Zend\I18n\Validator\Alnum())    
                ) );
  
    $validater->validate();
    $messager = $app->getServiceManager()->get('validatorMessager');  
    print_r( $messager->getValidationMessage());
   
 
    /***validator ***/ 
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
 
    //print_r('---'.array_search($_GET['url'], $_GET).'???');
    $stripper->offsetSet(array_search($_GET['url'], $_GET), new \Utill\Strip\Chain\StripChainer($app, $_GET['url'], array(\Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_CUSTOM_ADVANCED,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_JAVASCRIPT_FUNCTIONS,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_ONLY_ALPHABETIC_ALLOWED,        
                                                                                                )));
    $stripper->offsetSet('name', new \Utill\Strip\Chain\StripChainer($app, $_GET['name'], array(
                                                                                              \Services\Filter\FilterServiceNames::FILTER_DEFAULT,  
                                                                                              \Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED,  
                                                                                              \Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_CUSTOM_ADVANCED,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_JAVASCRIPT_FUNCTIONS ,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_LOWER_CASE,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS, 
        
        )));
  /*$stripper->offsetSet(array_search($_GET['icon_class'], $_GET), new \Utill\Strip\Chain\StripChainer($app, $_GET['icon_class'], array(
                                                                                              \Services\Filter\FilterServiceNames::FILTER_DEFAULT,  
                                                                                              \Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED,  
                                                                                              \Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_CUSTOM_ADVANCED,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_JAVASCRIPT_FUNCTIONS ,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_LOWER_CASE,
                                                                                              \Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS,        
    )));*/
    $stripper->offsetSet('parent', new \Utill\Strip\Chain\StripChainer($app, $_GET['parent'], array(
                                                                                        \Services\Filter\FilterServiceNames::FILTER_ONLY_NUMBER_ALLOWED,                                                                                             
    )));
    $stripper->offsetGet('parent');    
    $stripper->offsetSet('user_ID', new \Utill\Strip\Chain\StripChainer($app, $_GET['user_id'], array(
                                                                                          \Services\Filter\FilterServiceNames::FILTER_ONLY_NUMBER_ALLOWED,                                                                                             
    )));
    /*$stripper->offsetSet(array_search($_GET['description'], $_GET), new \Utill\Strip\Chain\StripChainer($app, $_GET['description'], array(
                                                                                          \Services\Filter\FilterServiceNames::FILTER_DEFAULT,  
                                                                                          \Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED,  
                                                                                          \Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_CUSTOM_ADVANCED,
                                                                                          \Services\Filter\FilterServiceNames::FILTER_JAVASCRIPT_FUNCTIONS ,
                                                                                          \Services\Filter\FilterServiceNames::FILTER_LOWER_CASE,
                                                                                          \Services\Filter\FilterServiceNames::FILTER_SQL_RESERVEDWORDS,        
    )));*/
    
       
    $stripper->strip();
    
    $filterMessager = $app->getServiceManager()->get('filterMessager');
    print_r($filterMessager->getFilterMessage());
    
   // $filteredValue = $stripper->offsetGet(array_search($_GET['url'], $_GET))->getFilterValue();
    $vName = urldecode(trim( $stripper->offsetGet(array_search($_GET['name'], $_GET))->getFilterValue()));
    $vParent = trim( $stripper->offsetGet(array_search($_GET['parent'], $_GET))->getFilterValue());
    $vUserId = trim( $stripper->offsetGet('user_ID')->getFilterValue());
   // $vIconClass = urldecode(trim( $stripper->offsetGet(array_search($_GET['icon_class'], $_GET))->getFilterValue()));

   // $vDescription = urldecode(trim( $stripper->offsetGet(array_search($_GET['description'], $_GET))->getFilterValue()));
        
    
   // print_r('--$vParent =-->'.$vParent.'----');
    //print_r($stripChainer->offsetGet('test'));
    
          
    $vIconClass = $_GET['icon_class'];
    $vDescription = $_GET['description']; 
    
    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];
     
      
   
    
    
    
    
    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');  
    




        $resDataInsert = $BLL->insert(array('name' => $vName,
            'icon_class' => $vIconClass,
            'parent' => $vParent,
            'user_id' => $vUserId, 
            'description' => $vDescription,
            'pk' => $vPk));
        // print_r($resDataInsert);    



        $app->response()->header("Content-Type", "application/json");



        /* $app->contentType('application/json');
          $app->halt(302, '{"error":"Something went wrong"}');
          $app->stop(); */

        $app->response()->body(json_encode($resDataInsert));
    
}
);
/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkUpdate_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');

    // Filters are called from service manager
    //$filterHtmlAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HTML_TAGS_ADVANCED);
    //  $filterHexadecimalBase = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED );
    //$filterHexadecimalAdvanced = $app->getServiceManager()->get(\Services\Filter\FilterServiceNames::FILTER_HEXADECIMAL_ADVANCED);

    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->update($_GET['id'], array('name' => $vName,
            'icon_class' => $vIconClass,
            'parent' => $vParent,
            'user_id' => $vUserId,
            'description' => $vDescription,
            'id' =>$_GET['id'],
            'pk' => $vPk));
    //print_r($resDataGrid);    

    $app->response()->header("Content-Type", "application/json");



    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});
/**
 *  * Okan CIRAN
 * @since 11-01-2016
 */
$app->get("/pkGetAll_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');


    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];
    //print_r($resDataMenu);


    $resDataGrid = $BLL->getAll();
    //print_r($resDataGrid);

    /**
     * BLL fillGridRowTotalCount örneği test edildi
     * datagrid için total row count döndürüyor
     * Okan CIRAN
     */
    $resTotalRowCount = $BLL->fillGridRowTotalCount();

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "name" => $flow["name"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "icon_class" => $flow["icon_class"],
            "create_date" => $flow["create_date"],
            "start_date" => $flow["start_date"],
            "end_date" => $flow["end_date"],
            "parent" => $flow["parent"],
            "deleted" => $flow["deleted"],
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],
            "state_active" => $flow["state_active"],
            "description" => $flow["description"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "root_parent" => $flow["root_parent"],
            "root" => $flow["root"],
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
});

/**
 *  * Okan CIRAN
 * @since 13-01-2016
 */
$app->get("/pkDelete_sysAclResources/", function () use ($app ) {


    $BLL = $app->getBLLManager()->get('sysAclResourcesBLL');


    $headerParams = $app->request()->headers();
    $pk = $headerParams['X-Public'];

    $resDataUpdate = $BLL->delete($_GET['id'], array(
        'user_id' => $_GET['user_id'],
        'pk' => $pk));


    $app->response()->header("Content-Type", "application/json");



    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resDataUpdate));
});

$app->run();
