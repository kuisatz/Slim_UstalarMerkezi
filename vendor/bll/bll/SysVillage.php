<?php

/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license  Ğİ 
 */

namespace BLL\BLL;

/**
 * Business Layer class for report Configuration entity
 */

class SysVillage extends \BLL\BLLSlim{
    
     /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
       /**
     * Data insert function
     * @param array | null $params
     * @return array
     */ 
  public function insert($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        return $DAL->insert($params);
    }
    
    /**
     * Data update function
     * @param integer $id
     * @param array | null $params
     * @return array
     */
    public function update($id = null, $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        return $DAL->update($id, $params);
    }
    
    /**
     * Data delete function
     * @param integer $id
     * @return array
     */
    public function delete($id = null) {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        return $DAL->delete($id);
    }

    /**
     * get all data
     * @return array
     */
    public function getAll() {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        return $DAL->getAll();
    }
    
    /**
     * Function to fill datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGrid ($params = array()) {
      //  print_r('123123asdasdasd') ; 
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        $resultSet = $DAL->fillGrid($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function to get datagrid row count on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGridRowTotalCount($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        $resultSet = $DAL->fillGridRowTotalCount($params);  
        return $resultSet['resultSet'];
    }

    /**
     * Function to get datagrid row count on user interface layer
     * @param array | null $params
     * @return array
     */
     public function fillComboBox($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        $resultSet = $DAL->fillComboBox($params);  
        return $resultSet['resultSet'];
     }
    
 /**
     * Function to get datagrid row count on user interface layer
     * @param array | null $params
     * @return array
     */
     public function insertLanguageTemplate($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysVillagePDO');
        $resultSet = $DAL->insertLanguageTemplate($params);  
        return $resultSet['resultSet'];
     }
     
     
     
     
}