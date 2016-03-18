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
 * created to be used by DAL MAnager for operation type tools operations
 * @author Okan CIRAN
 * @since 17/03/2016
 */
class InfoFirmKeys extends \DAL\DalSlim {

    /**
     * @author Okan CIRAN
     * @ info_firm_keys tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  17/03/2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $statement = $pdo->prepare(" 
                DELETE FROM info_firm_keys 
                WHERE id = :id");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
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
     * @author Okan CIRAN
     * @ info_firm_keys tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  17/03/2016    
     * @return array
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $statement = $pdo->prepare("
                SELECT 
                    a.id, 
                    a.s_date, 
		    a.firm_id, 
		    fp.firm_name,
		    a.oid, 
		    a.sf_private_key, 
		    a.sf_private_key_value 
                FROM info_firm_keys a 
                INNER JOIN info_firm_profile fp on fp.act_parent_id = a.firm_id AND fp.active=0 AND fp.deleted =0 AND fp.language_parent_id =0  
                ORDER BY fp.firm_name              
                                 ");
            $statement->execute();
            $result = $statement->fetcAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {    
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ info_firm_keys tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  17/03/2016
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $sql = "
                INSERT INTO info_firm_keys(   
                        firm_id
                       )
                VALUES (
                        :firm_id  
                                              )  ";
            $statement = $pdo->prepare($sql);       
            $statement->bindValue(':firm_id', $params['firm_id'], \PDO::PARAM_STR);           
         //   echo debugPDO($sql, $params);
            $result = $statement->execute();
            $insertID = $pdo->lastInsertId('info_firm_keys_id_seq');
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * info_firm_keys tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  17/03/2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function update($params = array()) {
        try {
            } catch (\PDOException $e /* Exception $e */) {            
        }
    }

    /**
     * Datagrid fill function used for testing
     * user interface datagrid fill operation   
     * @author Okan CIRAN
     * @ Gridi doldurmak için info_firm_keys tablosundan kayıtları döndürür !!
     * @version v 1.0  17/03/2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGrid($params = array()) {
        if (isset($params['page']) && $params['page'] != "" && isset($params['rows']) && $params['rows'] != "") {
            $offset = ((intval($params['page']) - 1) * intval($params['rows']));
            $limit = intval($params['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }

        $sortArr = array();
        $orderArr = array();
        if (isset($params['sort']) && $params['sort'] != "") {
            $sort = trim($params['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($params['sort']);
        } else {
            $sort = "fp.firm_name ";
        }

        if (isset($params['order']) && $params['order'] != "") {
            $order = trim($params['order']);
            $orderArr = explode(",", $order);       
            if (count($orderArr) === 1)
                $order = trim($params['order']);
        } else {
            $order = "ASC";
        }


        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                 SELECT 
                    a.id, 
                    a.s_date, 
		    a.firm_id, 
		    fp.firm_name,
		    a.oid, 
		    a.sf_private_key, 
		    a.sf_private_key_value 
                FROM info_firm_keys a 
                INNER JOIN info_firm_profile fp on fp.act_parent_id = a.firm_id AND fp.active=0 AND fp.deleted =0 AND fp.language_parent_id =0                  
                ORDER BY    " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
            //  echo debugPDO($sql, $parameters);
            $statement->bindValue(':language_id', $languageIdValue, \PDO::PARAM_INT);
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
     * @ Gridi doldurmak için info_firm_keys tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  17/03/2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                SELECT 
                    COUNT(a.id) AS COUNT ,    
                FROM info_firm_keys a 
                INNER JOIN info_firm_profile fp on fp.act_parent_id = a.firm_id AND fp.active=0 AND fp.deleted =0 AND fp.language_parent_id =0                  
                
                    ";
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
     *       
     * parametre olarak gelen array deki 'id' li kaydın, info_firm_keys tablosundaki private key ve value değerlerini oluşturur  !!
     * @author Okan CIRAN
     * @version v 1.0  17.03.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function setPrivateKey($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');                    
            $statement = $pdo->prepare("
                UPDATE info_firm_keys
                SET              
                    sf_private_key = armor( pgp_sym_encrypt (firm_id , oid, 'compress-algo=1, cipher-algo=bf'))                     
                WHERE                   
                    id = :id");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $statementValue = $pdo->prepare("
                UPDATE info_users
                SET              
                    sf_private_key_value = substring(sf_private_key,40,length( trim( sf_private_key))-140)                     
                WHERE                     
                    id = :id");
            $statementValue->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $updateValue = $statementValue->execute();
            $affectedRows = $statementValue->rowCount();
            $errorInfo = $statementValue->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);         
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {         
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    
}
