<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace BLL\BLL;

/**
 * Business Layer class for report Configuration entity
 */
class InfoUsers extends \BLL\BLLSlim{
    
    /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
    
    /**
     * DAta insert function
     * @param array | null $params
     * @return array
     */
    public function insert($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        return $DAL->insert($params);
    }
      /**
     * Check Data function
     * @param array | null $params
     * @return array
     */
    public function haveRecords($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        return $DAL->haveRecords($params);
    }
    
    
    /**
     * Data update function
     * @param integer $id
     * @param array | null $params
     * @return array
     */
    public function update($id = null, $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        return $DAL->update($id, $params);
    }
    
    /**
     * Data delete function
     * @param integer $id
     * @return array
     */
    public function delete($id = null) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        return $DAL->delete($id);
    }

    /**
     * get all data
     * @return array
     */
    public function getAll() {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        return $DAL->getAll();
    }
    
    /**
     * Function to fill datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGrid ($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        $resultSet = $DAL->fillGrid($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function to get datagrid row count on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGridRowTotalCount($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        $resultSet = $DAL->fillGridRowTotalCount($params);  
        return $resultSet['resultSet'];
    }
    
     /**
     * Data delete action function
     * @param integer $id
     * @param array | null $params
     * @return array
     */
    public function deletedAct($id = null, $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoUsersPDO');
        return $DAL->deletedAct($id, $params);
    }
    

}

