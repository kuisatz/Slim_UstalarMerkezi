<?php

// test commit for branch slim2
require 'vendor/autoload.php';


use \Services\Filter\Helper\FilterFactoryNames as stripChainers;

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
 * @since 18-02-2016
 */
$app->get("/pkFillSingularFirmMachineTools_infoFirmMachineTool/", function () use ($app ) {
 
    $BLL = $app->getBLLManager()->get('infoFirmMachineToolBLL');
 
    $headerParams = $app->request()->headers();
    $sort = null;
    if (isset($_GET['sort'])) {
        $sort = $_GET['sort'];
    }

    $order = null;
    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    }

    $rows = 10;
    if (isset($_GET['rows'])) {
        $rows = $_GET['rows'];
    }

    $page = 1;
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
    }

    $filterRules = null;
    if (isset($_GET['filterRules'])) {
        $filterRules = $_GET['filterRules'];
    }

    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkFillSingularFirmMachineTools_infoFirmMachineTool" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $vLanguageCode = strtolower(trim($_GET['language_code']));
    }

    $resDataGrid = $BLL->fillSingularFirmMachineTools(array('language_code' => $vLanguageCode,
        'page' => $page,
        'rows' => $rows,
        'sort' => $sort,
        'order' => $order,     
        'pk' => $pk,
        'filterRules' => $filterRules));    
 
    $resTotalRowCount = $BLL->fillSingularFirmMachineToolsRtc(array('pk' => $pk,
                                                'language_code' => $vLanguageCode));
 
    $flows = array();
    foreach ($resDataGrid['resultSet'] as $flow) {
        $flows[] = array(
            "id" => $flow["id"],
            "s_date" => $flow["s_date"],
            "c_date" => $flow["c_date"],
            "firm_id" => $flow["firm_id"],
            "firm_name" => $flow["firm_name"],
            "sys_machine_tool_id" => $flow["sys_machine_tool_id"],            
            
            "machine_tool_names" => $flow["machine_tool_names"],
            "machine_tool_name_eng" => $flow["machine_tool_name_eng"],
            "profile_public" => $flow["profile_public"],
            "state_profile_public" => $flow["state_profile_public"],
            "operation_type_id" => $flow["operation_type_id"],
            "operation_name" => $flow["operation_name"],
            "act_parent_id" => $flow["act_parent_id"],
            "language_parent_id" => $flow["language_parent_id"],
            "owner_id" => $flow["owner_id"],
            "owner_username" => $flow["owner_username"],
            "cons_allow_id" => $flow["cons_allow_id"],
            "cons_allow" => $flow["cons_allow"],
            "availability_id" => $flow["availability_id"],
            "state_availability" => $flow["state_availability"],
            
            "deleted" => $flow["deleted"],      
            "state_deleted" => $flow["state_deleted"],
            "active" => $flow["active"],       
            "state_active" => $flow["state_active"],              
            "language_code" => $flow["language_code"],                             
            "language_id" => $flow["language_id"],      
	    "language_name" => $flow["language_name"],
            "language_parent_id" => $flow["language_parent_id"],                
            "op_user_id" => $flow["op_user_id"],  
            "op_user_name" => $flow["op_user_name"],  
            "attributes" => array("notroot" => true, "active" => $flow["active"]),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount['resultSet'][0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));

});

 
/**
 *  * Okan CIRAN
 * @since 23-02-2016
 */
$app->get("/pkFillUsersFirmMachines_infoFirmMachineTool/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmMachineToolBLL');
    
    $headerParams = $app->request()->headers();
    if(!isset($headerParams['X-Public'])) throw new Exception ('rest api "pkGetConsConfirmationProcessDetails_sysOsbConsultants" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
    
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vId = 0;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }
    
    
    $stripper->strip();
    if($stripper->offsetExists('language_code')) $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    if($stripper->offsetExists('id')) $vId = $stripper->offsetGet('id')->getFilterValue();
 
    if (isset($_GET['id'])) {
    $resDataGrid = $BLL->fillUsersFirmMachines(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,
                                                        'id' => $vId,
                                                                ));
    $resTotalRowCount = $BLL->fillUsersFirmMachinesRtc(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,
                                                        'id' => $vId,
                                                                ));
    
    } else {
        $resDataGrid = $BLL->fillUsersFirmMachines(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,                                                        
                                                                ));
        $resTotalRowCount = $BLL->fillUsersFirmMachinesRtc(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,                                                        
                                                                ));
    }
    
   
    $flows = array();
    if (isset($resDataGrid['resultSet'][0]['machine_id'])) {       
        foreach ($resDataGrid['resultSet'] as $flow) {
            $flows[] = array(
                "machine_id" => $flow["machine_id"],
                "manufacturer_name" => $flow["manufacturer_name"],
                "machine_tool_grup_names" => $flow["machine_tool_grup_names"],
                "machine_tool_names" => $flow["machine_tool_names"],
                "model" => $flow["model"],
                "model_year" => $flow["model_year"],
                "attributes" => array("notroot" => true ),
            );
        }
        
    }
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */
    //if (isset($resDataGrid['resultSet']['machine_id'])) {
    //    $app->response()->body(json_encode($flows));
    //} else {
        $app->response()->body(json_encode($resultArray));
   // }
});

 
/**
 *  * Okan CIRAN
 * @since 23-02-2016
 */
$app->get("/pkFillUsersFirmMachineProperties_infoFirmMachineTool/", function () use ($app ) {

    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmMachineToolBLL');
    
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public'])) {
        throw new Exception('rest api "pkGetConsConfirmationProcessDetails_sysOsbConsultants" end point, X-Public variable not found');
    }
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vMachineId = 0;
    if (isset($_GET['machine_id'])) {
        $stripper->offsetSet('machine_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_id']));
    }    
    
    $stripper->strip();
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('machine_id')) {
        $vMachineId = $stripper->offsetGet('machine_id')->getFilterValue();
    }

    if (isset($_GET['machine_id'])) {
    $resDataGrid = $BLL->fillUsersFirmMachineProperties(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,
                                                        'machine_id' => $vMachineId,
                                                                ));
    } else {
        $resDataGrid = $BLL->fillUsersFirmMachineProperties(array(
                                                        'language_code' => $vLanguageCode,
                                                        'pk' => $pk,                                                        
                                                                ));
    }    

     $flows = array();
    if (isset($resDataGrid['resultSet'][0]['machine_id'])) {      
        foreach ($resDataGrid['resultSet']  as $flow) {
            $flows[] = array(
                "machine_id" => $flow["machine_id"],
                "id" => $flow["id"],
                "property_names" => $flow["property_names"],
                "property_name_eng" => $flow["property_name_eng"],
                "property_value" => $flow["property_value"],
                "unit_id" => $flow["unit_id"],
                "unitcodes" => $flow["unitcodes"],             
                "attributes" => array("notroot" => true ),
            );
        }
        
    }
    $resultArray = array();
    $resultArray['total'] = 2;//$resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    $app->response()->header("Content-Type", "application/json");

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */
   // if (isset($resDataGrid['resultSet']['machine_id'])) {
    //    $app->response()->body(json_encode($flows));
   // } else {
        $app->response()->body(json_encode($resultArray));
  //  }
});



/**x
 *  * Okan CIRAN
 * @since 25-02-2016
 */
$app->get("/pkInsert_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];  
   
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
 
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['profile_public']));
    }
 
    $vOperationTypeId = NULL;
    if (isset($_GET['operation_type_id'])) {
        $stripper->offsetSet('operation_type_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['operation_type_id']));
    }
    
    $vAvailabilityId = 0;
    if (isset($_GET['availability_id'])) {
        $stripper->offsetSet('availability_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['availability_id']));
    }  
    
    $vFirmId = NULL;
    if (isset($_GET['firm_id'])) {
        $stripper->offsetSet('firm_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['firm_id']));
    } 
    $vMachineId = NULL;
    if (isset($_GET['machine_id'])) {
        $stripper->offsetSet('machine_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_id']));
    }
    
    $stripper->strip();
    if($stripper->offsetExists('machine_id')) {
        $vMachineId = $stripper->offsetGet('machine_id')->getFilterValue();
    }
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('firm_id')) {
        $vFirmId = $stripper->offsetGet('firm_id')->getFilterValue();
    }
    if ($stripper->offsetExists('availability_id')) {
        $vAvailabilityId = $stripper->offsetGet('availability_id')->getFilterValue();
    }
    if ($stripper->offsetExists('operation_type_id')) {
        $vOperationTypeId = $stripper->offsetGet('operation_type_id')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }

    $resDataInsert = $BLL->insertTemp(array(  
            'language_code' => $vLanguageCode,
            'profile_public' => $vProfilePublic,        
            'firm_id' => $vFirmId , 
            'machine_id' => $vMachineId , 
            'availability_id' => $vAvailabilityId ,
            'operation_type_id' => $vOperationTypeId ,            
            'pk' => $Pk,        
            ));


    $app->response()->header("Content-Type", "application/json");
 
    $app->response()->body(json_encode($resDataInsert));
}
);

/**x
 *  * Okan CIRAN
 * @since 25-02-2016
 */
$app->get("/pkUpdate_infoFirmProfile/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('infoFirmProfileBLL');
   
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];  
   
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }   
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
        $stripper->offsetSet('profile_public', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['profile_public']));
    } 
    $vOperationTypeId = NULL;
    if (isset($_GET['operation_type_id'])) {
        $stripper->offsetSet('operation_type_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['operation_type_id']));
    }    
    $vAvailabilityId = 0;
    if (isset($_GET['availability_id'])) {
        $stripper->offsetSet('availability_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['availability_id']));
    }      
    $vFirmId = NULL;
    if (isset($_GET['firm_id'])) {
        $stripper->offsetSet('firm_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['firm_id']));
    } 
    $vMachineId = NULL;
    if (isset($_GET['machine_id'])) {
        $stripper->offsetSet('machine_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['machine_id']));
    }
    
    $stripper->strip();
    if($stripper->offsetExists('machine_id')) {
        $vMachineId = $stripper->offsetGet('machine_id')->getFilterValue();
    }
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }
    if ($stripper->offsetExists('firm_id')) {
        $vFirmId = $stripper->offsetGet('firm_id')->getFilterValue();
    }
    if ($stripper->offsetExists('availability_id')) {
        $vAvailabilityId = $stripper->offsetGet('availability_id')->getFilterValue();
    }
    if ($stripper->offsetExists('operation_type_id')) {
        $vOperationTypeId = $stripper->offsetGet('operation_type_id')->getFilterValue();
    }
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    }

    $resDataInsert = $BLL->insertTemp(array(  
            'language_code' => $vLanguageCode,
            'profile_public' => $vProfilePublic,        
            'firm_id' => $vFirmId , 
            'machine_id' => $vMachineId , 
            'availability_id' => $vAvailabilityId ,
            'operation_type_id' => $vOperationTypeId ,            
            'pk' => $Pk,        
            ));


    $app->response()->header("Content-Type", "application/json");
 
    $app->response()->body(json_encode($resDataInsert));
}
); 


$app->run();
