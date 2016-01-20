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
 * @author Okan CİRANĞ
 */
class BlLoginLogout extends \DAL\DalSlim {

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
     * @ info_users tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  30.12.2015
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function delete($id = null) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and  column names will be changed for specific use
             */
            //Prepare our UPDATE SQL statement. 
            $statement = $pdo->prepare(" 
                UPDATE info_users
                SET deleted= 1
                WHERE id = :id");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Execute our DELETE statement.
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
     * @ info_users tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  30.12.2015    
     * @return array
     * @throws \PDOException
     */
    public function getAll() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            $statement = $pdo->prepare("
                    SELECT 
                        a.id, 
                        a.profile_public, 
                        a.f_check, 
                        a.s_date, 
                        a.c_date, 
                        a.operation_type_id,
                        op.operation_name, 
                        a.firm_name, 
                        a.web_address,                     
                        a.tax_office, 
                        a.tax_no, 
                        a.sgk_sicil_no,
			a.bagkur_sicil_no,
			a.ownership_status_id,
                        sd4.description AS owner_ship,
			a.foundation_year,
			a.language_id, 
			a.act_parent_id,  
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,                        
                        a.active, 
                        sd3.description AS state_active,  
                        a.deleted,
			sd2.description AS state_deleted, 
                        a.user_id,
                        u.username,                    
                        a.auth_allow_id, 
                        sd.description AS auth_alow ,
                        a.cons_allow_id,
                        sd1.description AS cons_allow,
                        a.language_parent_id
                    FROM info_users a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code  AND op.deleted =0 AND op.active =0
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = a.language_code AND a.auth_allow_id = sd.first_group  AND sd.deleted =0 AND sd.active =0
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_code = a.language_code AND a.cons_allow_id = sd1.first_group  AND sd1.deleted =0 AND sd1.active =0
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_code = a.language_code AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = a.language_code AND sd3.deleted = 0 AND sd3.active = 0
                    LEFT JOIN sys_specific_definitions sd4 ON sd4.main_group = 1 AND sd4.first_group= a.active AND sd4.language_code = a.language_code AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.user_id  
                    ORDER BY a.firm_name   
                          ");

            $statement->execute();
            $result = $statement->fetcAll(\PDO::FETCH_ASSOC);
            /* while ($row = $statement->fetch()) {
              print_r($row);
              } */
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
     * @ info_users tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  30.12.2015
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and column names will be changed for specific use
             */
            $statement = $pdo->prepare("
                INSERT INTO info_users(
                        name, name_eng, language_code, language_parent_id, 
                        user_id, flag_icon_road, country_code3, priority   )
                VALUES (
                        :name,
                        :name_eng, 
                        :language_code,
                        :language_parent_id,
                        :user_id,
                        :flag_icon_road,                       
                        :country_code3,
                        :priority 
                                                ");
            $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement->bindValue(':name_eng', $params['name_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
            $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':flag_icon_road', $params['flag_icon_road'], \PDO::PARAM_STR);
            $statement->bindValue(':country_code3', $params['country_code3'], \PDO::PARAM_STR);
            $statement->bindValue(':priority', $params['priority'], \PDO::PARAM_INT);

            $result = $statement->execute();

            $insertID = $pdo->lastInsertId('info_users_id_seq');

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
     * info_users tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  30.12.2015
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function update($id = null, $params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and  column names will be changed for specific use
             */
            //Prepare our UPDATE SQL statement.            
            $statement = $pdo->prepare("
                UPDATE info_users
                SET              
                    name = :name, 
                    name_eng = :name_eng, 
                    language_code = :language_code,                    
                    language_parent_id = :language_parent_id,
                    user_id = :user_id,
                    flag_icon_road = :flag_icon_road,                       
                    country_code3 = :country_code3,
                    priority = :priority 
                WHERE id = :id");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Bind our :model parameter.
            $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement->bindValue(':name_eng', $params['name_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
            $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':flag_icon_road', $params['flag_icon_road'], \PDO::PARAM_STR);
            $statement->bindValue(':country_code3', $params['country_code3'], \PDO::PARAM_STR);
            $statement->bindValue(':priority', $params['priority'], \PDO::PARAM_INT);

            //Execute our UPDATE statement.
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * 
     * @author Okan CIRAN
     * @ public key e ait bir private key li kullanıcı varsa True değeri döndürür.  !!
     * @version v 1.0  31.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function pkControl($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /*
            $sql = "          
                SELECT * FROM (
                    SELECT id, sf_private_key_value = 
                    Pgp_sym_decrypt( 
                     DEARMOR(CONCAT( '-----BEGIN PGP MESSAGE-----

                    ',:pk,'
                    -----END PGP MESSAGE-----
                    '))
                    , 'Bahram Lotfi Sadigh', 'compress-algo=1, cipher-algo=bf') AS pkey
                    FROM info_users) AS logintable
                WHERE pkey = TRUE                
                                 ";             
             */
            $sql = "              
                    SELECT id,pkey,sf_private_key_value FROM (
                            SELECT id, 	
                                CRYPT(sf_private_key_value,CONCAT('_J9..',REPLACE('".$params['pk']."','*','/'))) = CONCAT('_J9..',REPLACE('".$params['pk']."','*','/')) as pkey,	                                
                                sf_private_key_value
                            FROM info_users) AS logintable
                        WHERE pkey = TRUE

                    ";  
            
            
            $statement = $pdo->prepare($sql);
            //$statement->bindValue(':public_key', $params['pk'], \PDO::PARAM_STR);
            //echo debugPDO($sql, $params);
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
     * 
     * @author Okan CIRAN
     * @ login için info_users tablosundan çekilen kayıtları döndürür   !!
     * bu fonksiyon aktif olarak kullanılmıyor. ihtiyaç a göre aktifleştirilecek. public key algoritması farklı. 
     * @version v 1.0  31.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function pkLoginControl($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "          
                SELECT 
                    a.name, 
                    a.surname, 
                    a.username, 
                    a.auth_email, 
                    a.gender_id, 
                    sd4.description AS gender, 
                    a.active, 
                    a.auth_allow_id, 
                    sd.description AS auth_alow,
                    a.cons_allow_id,
                    sd1.description AS cons_allow,
                    a.language_code AS user_language,  
                    COALESCE(NULLIF(l.language_main_code,''),'en') AS language_main_code,
                    a.sf_private_key_value,
                    COALESCE(NULLIF( 
                    (SELECT CAST(MIN(bz.parent) AS varchar(5))
                            FROM sys_acl_roles az 
                            LEFT JOIN sys_acl_roles bz ON bz.parent = az.id   
                            WHERE az.id= sarmap.role_id),''), CAST(sar.parent AS varchar(5))) AS Menu_type  
                FROM info_users a              
                LEFT JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = COALESCE(NULLIF(l.language_main_code, ''), 'en') AND a.auth_allow_id = sd.first_group 
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND sd1.language_code = COALESCE(NULLIF(l.language_main_code, ''), 'en') AND a.cons_allow_id = sd1.first_group 
                INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = COALESCE(NULLIF(l.language_main_code, ''),'en') AND sd3.deleted = 0 AND sd3.active = 0
                INNER JOIN sys_specific_definitions sd4 ON sd4.main_group = 3 AND sd4.first_group= a.active AND sd4.language_code = COALESCE(NULLIF(l.language_main_code, ''),'en') AND sd4.deleted = 0 AND sd4.active = 0
                INNER JOIN act_users_rrpmap usr ON usr.info_users_id = a.id AND usr.active = 0 AND usr.deleted = 0 
                INNER JOIN sys_acl_rrpmap sarmap ON sarmap.id = usr.rrpmap_id AND sarmap.active=0 AND sarmap.deleted =0 
                INNER JOIN sys_acl_roles sar ON sar.id = sarmap.role_id AND sar.active=0 AND sar.deleted=0 
                WHERE  
                    CRYPT(a.sf_private_key_value,CONCAT('_J9..',REPLACE('".$params['pk']."','*','/'))) = CONCAT('_J9..',REPLACE('".$params['pk']."','*','/'))
 
                    ";

            $statement = $pdo->prepare($sql);            
      //      echo debugPDO($sql, $parameters);
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
     * 
     * @author Okan CIRAN
     * @ info_users tablosundan public key i döndürür   !!
     * @version v 1.0  31.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function getPK($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
      
            /**
             * @version kapatılmıs olan kısımdaki public key algoritması kullanılmıyor.
             */
            /*      $sql = "          
            SELECT                
                REPLACE(REPLACE(ARMOR(pgp_sym_encrypt(a.sf_private_key_value, 'Bahram Lotfi Sadigh', 'compress-algo=1, cipher-algo=bf'))
	,'-----BEGIN PGP MESSAGE-----

',''),'
-----END PGP MESSAGE-----
','') as public_key1     ,

                substring(ARMOR(pgp_sym_encrypt(a.sf_private_key_value, 'Bahram Lotfi Sadigh', 'compress-algo=1, cipher-algo=bf')),30,length( trim( sf_private_key))-62) as public_key2, 
        */      
            ///crypt(:password, gen_salt('bf', 8)); örnek bf komut
                  $sql = "   
                        
                SELECT       
                     REPLACE(TRIM(SUBSTRING(crypt(sf_private_key_value,gen_salt('xdes')),6,20)),'/','*') AS public_key 
                FROM info_users a              
                INNER JOIN act_users_rrpmap usr ON usr.info_users_id = a.id AND usr.active = 0 AND usr.deleted = 0 
		INNER JOIN sys_acl_rrpmap sarmap ON sarmap.id = usr.rrpmap_id AND sarmap.active=0 AND sarmap.deleted =0 
                INNER JOIN sys_acl_roles sar ON sar.id = sarmap.role_id AND sar.active=0 AND sar.deleted=0 
                WHERE a.username = :username 
                    AND a.password = :password   
                    AND a.deleted = 0 
                    AND a.active = 0 
                
                                 ";

            $statement = $pdo->prepare($sql);
            $statement->bindValue(':username', $params['username'], \PDO::PARAM_STR);
            $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);
            echo debugPDO($sql, $parameters);
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
     * 
     * @author Okan CIRAN
     * @ public key e ait bir private key li kullanıcı varsa True değeri döndürür.  !!
     * @version v 1.0  31.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function pkSessionControl($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /*
            $sql = "          
                SELECT * FROM (
                    SELECT id, sf_private_key_value = 
                    Pgp_sym_decrypt( 
                     DEARMOR(CONCAT( '-----BEGIN PGP MESSAGE-----

                    ',:pk,'
                    -----END PGP MESSAGE-----
                    '))
                    , 'Bahram Lotfi Sadigh', 'compress-algo=1, cipher-algo=bf') AS pkey
                    FROM info_users) AS logintable
                WHERE pkey = TRUE                
                                 ";             
             */
            $sql = "    
                    SELECT a.id, a.name, a.data, a.lifetime, a.c_date, a.modified, a.public_key, b.name AS u_name, b.surname AS u_surname, b.username,b.sf_private_key_value
                    FROM act_session a 
                    INNER JOIN info_users b ON CRYPT(b.sf_private_key_value,CONCAT('_J9..',REPLACE(a.public_key,'*','/'))) = CONCAT('_J9..',REPLACE(a.public_key,'*','/'))
                        AND b.active = 0 AND b.deleted = 0
                    WHERE a.public_key = :public_key 
                    ";  
            
            
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':public_key', $params['pk'], \PDO::PARAM_STR);
            echo debugPDO($sql, $params);
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
     * 
     * @author Okan CIRAN
     * @ public key varsa True değeri döndürür.  !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function pkIsThere($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');          
            $sql = "              
                    SELECT a.public_key =  '".$params['pk']."'
                    FROM act_session a                  
                    WHERE a.public_key =   '".$params['pk']."'
                    ";           
            
            $statement = $pdo->prepare($sql);            
            //echo debugPDO($sql, $params);
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
     * 
     * @author Okan CIRAN
     * @ parametre olarak gelen public key in private key inden üretilmiş aktif tüm public key leri döndürür.  !!     
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function pkAllPkGeneratedFromPrivate($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');          
            $sql = "  
                    SELECT ax.id, ax.name,ax.data,ax.lifetime,ax.c_date,ax.public_key FROM act_session ax 
                    WHERE 
                        CRYPT((SELECT b.sf_private_key_value
                                            FROM act_session a 
                                            INNER JOIN info_users b ON CRYPT(b.sf_private_key_value,CONCAT('_J9..',REPLACE(a.public_key,'*','/'))) = CONCAT('_J9..',REPLACE(a.public_key,'*','/'))
                                                AND b.active = 0 AND b.deleted = 0
                                            WHERE a.public_key = '".$params['pk']."'
                        ),CONCAT('_J9..',REPLACE(ax.public_key,'*','/'))) = CONCAT('_J9..',REPLACE(ax.public_key,'*','/'))
 
                    ";           
            
            $statement = $pdo->prepare($sql);            
            //echo debugPDO($sql, $params);
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
