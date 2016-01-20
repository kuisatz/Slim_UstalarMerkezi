<?php

/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace DAL\PDO;

/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @
 * @author Okan CIRAN
 */
class SysAclRrpMap extends \DAL\DalSlim {

    /**
     * basic delete from database  example for PDO prepared
     * statements, table names are irrelevant and should be changed on specific 
     * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [affectedRowsCount] => 1
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage
     * @author Okan CIRAN
     * @ sys_acl_rrpmap tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  13-01-2016
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function delete($id = null, $params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();         
            $statement = $pdo->prepare(" 
                UPDATE sys_acl_rrpmap
                SET  deleted= 1 , 
                    user_id =  " . intval($params['user_id']) . " 
                WHERE id = :id");      
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);         
            $update = $statement->execute();
            $afterRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic select from database  example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific 
     * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [resultSet] => Array
      (
      [0] => Array
      (
      [id] => 1
      [name] => zeyn dag
      [international_code] => 12
      [active] => 1
      )

      [1] => Array
      (
      [id] => 4
      [name] => zeyn dag
      [international_code] => 12
      [active] => 1
      )

      [2] => Array
      (
      [id] => 5
      [name] => zeyn dag new
      [international_code] => 25
      [active] => 1
      )

      [3] => Array
      (
      [id] => 3
      [name] => zeyn zeyn oldu şimdik
      [international_code] => 12
      [active] => 1
      )

      )

      )
     * usage 
     * @author Okan CIRAN
     * @ sys_acl_rrpmap tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  13-01-2016    
     * @return array
     * @throws \PDOException
     */
    public function getAll() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory'); 
            $statement = $pdo->prepare("
            SELECT 
                    a.id, 
                    concat( rr.name,' - ',  rs.name,' - ' ,  rp.name , ' map ') as map_adi,
		    a.role_id, 
		    rr.name as role_name,
		    a.resource_id, 
		    rs.name as resource_name,
		    a.privilege_id,
                    rp.name as privilege_name,		 
		    a.c_date as create_date,
		    a.start_date,
		    a.end_date,		                  
                    a.deleted, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active,  
                    a.description,                                     
                    a.user_id,
                    u.username                                                            
            FROM sys_acl_rrpmap  a
            INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
            INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0  
            INNER JOIN info_users u ON u.id = a.user_id 
            INNER JOIN sys_acl_roles rr ON rr.id = a.role_id AND rr.deleted = 0 AND rr.active = 0 
            INNER JOIN sys_acl_resources rs ON rs.id = a.resource_id AND rs.deleted = 0 AND rs.active = 0 
            INNER JOIN sys_acl_privilege rp ON rp.id = a.privilege_id AND rp.deleted = 0 AND rp.active = 0             
            WHERE a.deleted =0 
            ORDER BY map_adi                
                                 ");
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);     
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }    
    
    /**
     * basic have records control  
     * * returned result set example;
     * for success result  
     * usage     
     * @author Okan CIRAN
     * @ sys_acl_rrpmap tablosunda role_id, resource_id ve privilege_id aynı kayıtta daha önce oluşturulmuş mu? 
     * @version v 1.0 15.01.2016
     * @return array
     * @throws \PDOException
     */
    public function haveRecords($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $addSql = "";
            if (isset($params['id'])) {
                $addSql = " AND id != " . intval($params['id']) . " ";
            }
            $sql = "             
            SELECT  CONCAT( value , ' map ') , value AS name ,
            value = value AS control , 
            CONCAT(value , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message                             
            FROM (
                SELECT  
                    CONCAT( rr.name,' - ',  rs.name,' - ' ,  rp.name  ) AS value                                               
                FROM sys_acl_rrpmap a
                INNER JOIN sys_acl_roles rr ON rr.id = a.role_id AND rr.deleted = 0 AND rr.active = 0 
                INNER JOIN sys_acl_resources rs ON rs.id = a.resource_id AND rs.deleted = 0 AND rs.active = 0 
                INNER JOIN sys_acl_privilege rp ON rp.id = a.privilege_id AND rp.deleted = 0 AND rp.active = 0  
                WHERE a.deleted =0  
                    AND a.role_id =  " . $params['role_id'] . "
                    AND a.resource_id = " . $params['resource_id'] . "
                    AND a.privilege_id = " . $params['privilege_id'] . "
                       " . $addSql . "   
                ) as ssd
                                   ";
            $statement = $pdo->prepare($sql);        
         //   echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);             
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic insert database example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific 
     * * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [lastInsertId] => 5
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage     
     * @author Okan CIRAN
     * @ sys_acl_rrpmap tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  13-01-2016
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $kontrol = $this->haveRecords($params); 
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) {          
                $sql = "
                INSERT INTO sys_acl_rrpmap(
                       role_id, resource_id, privilege_id,  user_id, description )
                VALUES (
                        :role_id,
                        :resource_id,                       
                        :privilege_id,  
                        :user_id,
                        :description                      
                                             )   ";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':role_id', $params['role_id'], \PDO::PARAM_INT);
                $statement->bindValue(':resource_id', $params['resource_id'], \PDO::PARAM_INT);
                $statement->bindValue(':privilege_id', $params['privilege_id'], \PDO::PARAM_INT);
                $statement->bindValue(':description', $params['description'], \PDO::PARAM_STR);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
                // echo debugPDO($sql, $params);
                $result = $statement->execute();
                $insertID = $pdo->lastInsertId('sys_acl_rrpmap_id_seq');
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
            } else {  
                $errorInfo = '23505'; 
                $pdo->commit();
                $result= $kontrol;                            
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic update database example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific
     * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [affectedRowsCount] => 1
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage  
     * @author Okan CIRAN
     * sys_acl_rrpmap tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  13-01-2016
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function update($id = null, $params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
             $kontrol = $this->haveRecords($params); 
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) {

                /**
                 * table names and  column names will be changed for specific use
                 */
                //Prepare our UPDATE SQL statement.            
                $sql = "
                UPDATE sys_acl_rrpmap
                SET 
                    role_id = :role_id,
                    resource_id = :resource_id,                       
                    privilege_id =:privilege_id,  
                    user_id = :user_id,
                    description= :description  
                WHERE id = :id ";
                //  echo debugPDO($sql, $params);

                $statement = $pdo->prepare($sql);
                //Bind our value to the parameter :id.         
                $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
                //Bind our :model parameter.                  
                $statement->bindValue(':role_id', $params['role_id'], \PDO::PARAM_INT);
                $statement->bindValue(':resource_id', $params['resource_id'], \PDO::PARAM_INT);
                $statement->bindValue(':privilege_id', $params['privilege_id'], \PDO::PARAM_INT);
                $statement->bindValue(':description', $params['description'], \PDO::PARAM_STR);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);

                //  echo debugPDO($sql, $params);
                //Execute our UPDATE statement.
                $update = $statement->execute();
                $affectedRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
            } else {   
                $errorInfo = '23505';
                $pdo->commit();
                $result= $kontrol;            
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * Datagrid fill function used for testing
     * user interface datagrid fill operation   
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_acl_rrpmap tablosundan kayıtları döndürür !!
     * @version v 1.0  13-01-2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGrid($args = array()) {
        if (isset($args['page']) && $args['page'] != "" && isset($args['rows']) && $args['rows'] != "") {
            $offset = ((intval($args['page']) - 1) * intval($args['rows']));
            $limit = intval($args['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
        
        $sortArr = array();
        $orderArr = array();
        if (isset($args['sort']) && $args['sort'] != "") {
            $sort = trim($args['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($args['sort']);
        } else {         
            $sort = "name";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            //print_r($orderArr);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {
            //$order = "desc";
            $order = "ASC";
        }
        
         $whereNameSQL = '';
        if (isset($args['search_name']) && $args['search_name'] != "") {
            $whereNameSQL = " AND name LIKE '%" . $args['search_name'] . "%' ";
            //    print_r('2<<<<< sql e gelen ='.$args['search_name'].'>>>>>>>>>>2');
        } 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
            SELECT 
                id, 
                name,
                role_id, 
                role_name,
                resource_id, 
                resource_name,
                privilege_id,
                privilege_name,		 
                create_date,
                start_date,
                end_date,		                  
                deleted, 
                state_deleted,                 
                active, 
                state_active,  
                description,                                     
                user_id,
                username  
            FROM (   
                    SELECT 
                       a.id, 
                       CONCAT( rr.name,' - ',  rs.name,' - ' ,  rp.name , ' map ') AS name,
                       a.role_id, 
                       rr.name AS role_name,
                       a.resource_id, 
                       rs.name AS resource_name,
                       a.privilege_id,
                       rp.name AS privilege_name,		 
                       a.c_date AS create_date,
                       a.start_date,
                       a.end_date,		                  
                       a.deleted, 
                       sd.description AS state_deleted,                 
                       a.active, 
                       sd1.description AS state_active,  
                       a.description,                                     
                       a.user_id,
                       u.username                                                            
                    FROM sys_acl_rrpmap  a
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0  
                    INNER JOIN info_users u ON u.id = a.user_id 
                    INNER JOIN sys_acl_roles rr ON rr.id = a.role_id AND rr.deleted = 0 AND rr.active = 0 
                    INNER JOIN sys_acl_resources rs ON rs.id = a.resource_id AND rs.deleted = 0 AND rs.active = 0 
                    INNER JOIN sys_acl_privilege rp ON rp.id = a.privilege_id AND rp.deleted = 0 AND rp.active = 0      
                    WHERE a.deleted =0 
               ) as asdd 
                " . $whereNameSQL . "
               ORDER BY    " . $sort . " "
                       . "" . $order . " "
                       . "LIMIT " . $pdo->quote($limit) . " "
                       . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            /**
             * For debug purposes PDO statement sql
             * uses 'Panique' library located in vendor directory
             */
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
            //  echo debugPDO($sql, $parameters);

            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();

            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * user interface datagrid fill operation get row count for widget
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_acl_rrpmap tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  13-01-2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            
            $whereNameSQL = '';
            $whereNameSQL1 = '';
            $whereNameSQL2 = '';
            if (isset($params['search_name']) && $params['search_name'] != "") {
                $whereNameSQL = " WHERE CONCAT( rr.name,' - ',  rs.name,' - ' ,  rp.name , ' map ') LIKE '%" . $params['search_name'] . "%' ";
                $whereNameSQL1 = " AND CONCAT( rrx.name,' - ',  rsx.name,' - ' ,  rpx.name , ' map ') LIKE '%" . $params['search_name'] . "%' ";
                $whereNameSQL2 = " AND CONCAT( rry.name,' - ',  rsy.name,' - ' ,  rpy.name , ' map ') LIKE '%" . $params['search_name'] . "%' ";        
            }            
            
            $sql = "
               SELECT 
                    COUNT(a.id) AS COUNT ,
                    (SELECT COUNT(a1x.id) FROM sys_acl_rrpmap a1x  
                    INNER JOIN sys_specific_definitions sdx ON sdx.main_group = 15 AND sdx.first_group= a1x.deleted AND sdx.language_code = 'tr' AND sdx.deleted = 0 AND sdx.active = 0
		    INNER JOIN sys_specific_definitions sd1x ON sd1x.main_group = 16 AND sd1x.first_group= a1x.active AND sd1x.language_code = 'tr' AND sd1x.deleted = 0 AND sd1x.active = 0  
		    INNER JOIN info_users ux ON ux.id = a1x.user_id 
		    INNER JOIN sys_acl_roles rrx ON rrx.id = a1x.role_id AND rrx.deleted = 0 AND rrx.active = 0 
		    INNER JOIN sys_acl_resources rsx ON rsx.id = a1x.resource_id AND rsx.deleted = 0 AND rsx.active = 0 
		    INNER JOIN sys_acl_privilege rpx ON rpx.id = a1x.privilege_id AND rpx.deleted = 0 AND rpx.active = 0 
                    WHERE a1x.deleted =0  
                     " . $whereNameSQL1 . ") AS undeleted_count, 
                    (SELECT COUNT(a2y.id) FROM sys_acl_rrpmap a2y
                    INNER JOIN sys_specific_definitions sdy ON sdy.main_group = 15 AND sdy.first_group= a2y.deleted AND sdy.language_code = 'tr' AND sdy.deleted = 0 AND sdy.active = 0
		    INNER JOIN sys_specific_definitions sd1y ON sd1y.main_group = 16 AND sd1y.first_group= a2y.active AND sd1y.language_code = 'tr' AND sd1y.deleted = 0 AND sd1y.active = 0  
		    INNER JOIN info_users uy ON uy.id = a2y.user_id 
		    INNER JOIN sys_acl_roles rry ON rry.id = a2y.role_id AND rry.deleted = 0 AND rry.active = 0 
		    INNER JOIN sys_acl_resources rsy ON rsy.id = a2y.resource_id AND rsy.deleted = 0 AND rsy.active = 0 
		    INNER JOIN sys_acl_privilege rpy ON rpy.id = a2y.privilege_id AND rpy.deleted = 0 AND rpy.active = 0 			
                    WHERE a2y.deleted =1  
                     " . $whereNameSQL2 . " ) AS deleted_count                        
                FROM sys_acl_rrpmap a
		INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0  
		INNER JOIN info_users u ON u.id = a.user_id 
		INNER JOIN sys_acl_roles rr ON rr.id = a.role_id AND rr.deleted = 0 AND rr.active = 0 
		INNER JOIN sys_acl_resources rs ON rs.id = a.resource_id AND rs.deleted = 0 AND rs.active = 0 
		INNER JOIN sys_acl_privilege rp ON rp.id = a.privilege_id AND rp.deleted = 0 AND rp.active = 0 
                 " . $whereNameSQL . "    ";
            $statement = $pdo->prepare($sql);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * Combobox fill function used for testing
     * user interface combobox fill operation   
     * @author Okan CIRAN
     * @ tree, combobox doldurmak için sys_acl_rrpmap tablosundan tüm kayıtları ya da ilgili parametreye göre kayıtları döndürür !!
     * @version v 1.0  13-01-2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillRrpMap($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');           
            $whereSQL = '';
            $roleId = 0;
            if (isset($_GET['role_id']) && $_GET['role_id'] != "") {
                $whereSQL = ' AND a.role_id = ' . $_GET['role_id'];
            }

            if (isset($_GET['resource_id']) && $_GET['resource_id'] != "") {
                $whereSQL = $whereSQL . ' AND a.resource_id = ' . $_GET['resource_id'];
            }

            if (isset($_GET['privilege_id']) && $_GET['privilege_id'] != "") {
                $whereSQL = $whereSQL . ' AND a.privilege_id = ' . $_GET['privilege_id'];
            }

            $whereNameSQL = '';
            if (isset($_GET['name']) && $_GET['name'] != "") {
                $whereNameSQL = ' WHERE name LIKE "%' . $_GET['name'] . '%" ';
            }

            $statement = $pdo->prepare("
                SELECT id, name , active ,state_type 
                FROM (
                    SELECT 
                        a.id, 
                        CONCAT( rr.name,' - ',  rs.name,' - ' ,  rp.name , ' map ') AS name,		               
                        a.active ,
                        a.active as state_type
                    FROM sys_acl_rrpmap a         
                    INNER JOIN sys_acl_roles rr ON rr.id = a.role_id AND rr.deleted = 0 AND rr.active = 0 
                    INNER JOIN sys_acl_resources rs ON rs.id = a.resource_id AND rs.deleted = 0 AND rs.active = 0 
                    INNER JOIN sys_acl_privilege rp ON rp.id = a.privilege_id AND rp.deleted = 0 AND rp.active = 0             
                    WHERE a.deleted =0 "
                        . $whereSQL . "
                ) AS xxy "
                    . $whereNameSQL . "
                ORDER BY name           
                                 ");
         //   echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);          
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

}
