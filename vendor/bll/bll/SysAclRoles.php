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
class SysAclRoles extends \BLL\BLLSlim {

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
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        return $DAL->insert($params);
    }

    /**
     * Data update function
     * @param integer $id
     * @param array $params
     * @return array
     */
    public function update($id = null, $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        return $DAL->update($id, $params);
    }

    /**
     * Data delete function
     * @param integer $id
     * @return array
     */
    public function delete($id = null, $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        return $DAL->delete($id, $params);
    }

    /**
     * get all data
     * @return array
     */
    public function getAll() {
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        return $DAL->getAll();
    }

    /**
     * Function to fill datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGrid($params = array()) {

        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        $resultSet = $DAL->fillGrid($params);
        return $resultSet['resultSet'];
    }

    /**
     * Function to get datagrid row count on user interface layer
     * @param array $params
     * @return array
     */
    public function fillGridRowTotalCount($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        $resultSet = $DAL->fillGridRowTotalCount($params);
        return $resultSet['resultSet'];
    }

    /**
     *  
     * @param array  $params
     * @return array
     */
    public function fillComboBoxMainRoles($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        $resultSet = $DAL->fillComboBoxMainRoles($params);
        return $resultSet['resultSet'];
    }

    /**
     * Function to fill text on user interface layer
     * @param array $params
     * @return array
     */
    public function fillComboBoxFullRoles($params = array()) {

        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        $resultSet = $DAL->fillComboBoxFullRoles($params);
        return $resultSet['resultSet'];
    }

    /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function updateChild($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('sysAclRolesPDO');
        return $DAL->updateChild($params);
    }

}
