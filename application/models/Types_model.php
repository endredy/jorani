<?php
/**
 * This Class contains all the business logic and the persistence layer for the types of leave request.
 * @copyright  Copyright (c) 2014-2023 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.0
 */

if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

/**
 * This Class contains all the business logic and the persistence layer for the types of leave request.
 */
class Types_model extends CI_Model {

    /**
     * Default constructor
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function __construct() {
        
    }

    /**
     * Get the list of types or one type
     * @param int $id optional id of a type
     * @return array record of types
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getTypes($id = 0) {
        if ($id === 0) {
            if ($this->config->item('hideZeroLeaveType')){
                $query = $this->db->get_where('types', 'id != 0');
            }else {
                $query = $this->db->get('types');
            }
            return $query->result_array();
        }
        $query = $this->db->get_where('types', array('id' => $id));
        return $query->row_array();
    }
    
    /**
     * Get the list of types or one type
     * @param string $name type name
     * @return array record of a leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getTypeByName($name) {
        $query = $this->db->get_where('types', array('name' => $name));
        return $query->row_array();
    }
    
    /**
     * Get the list of types as an ordered associative array
     * @param int $id
     * @param null $selectedId selected type (optional), if the selected type is forbidden one, then we will show it
     * @return array Associative array of types (id, name)
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getTypesAsArray($id = 0, $selectedId = NULL) {
        $listOfTypes = array();
        $this->db->from('types');
        $this->db->order_by('name');

        //add out office types (esteve)
        if ($this->config->item('hideZeroLeaveType') && $selectedId !== '0') {
            $this->db->where('id != 0');
        }
//        $this->db->join('parameters', 'parameters.entity_id = types.id', 'LEFT', TRUE);
        $this->db->select('types.*'); //, parameters.value as spec
        $rows = $this->db->get()->result_array();
        foreach ($rows as $row) {
            $listOfTypes[$row['id']] = $row['name'];
        }
        return $listOfTypes;
    }
    
    /**
     * Get the name of a given type id
     * @param int $id ID of the type
     * @return string label of the type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function getName($id) {
        $type = $this->getTypes($id);
        return $type['name'];
    }
    
    /**
     * Insert a new leave type. Data are taken from HTML form.
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function setTypes() {
        $deduct = ($this->input->post('deduct_days_off') == 'on')?TRUE:FALSE;
        $data = array(
            'acronym' => $this->input->post('acronym'),
            'name' => $this->input->post('name'),
            'deduct_days_off' => $deduct,
            'color' => $this->input->post('color'),
            'textcolor' => $this->input->post('textcolor'),
            'limit' => $this->input->post('limit'),
            'nodeduction' => $this->input->post('nodeduction'),
            'noapproval' => $this->input->post('noapproval'),
            'approvebyadmin' => $this->input->post('approvebyadmin'),
            'extrainput' => $this->input->post('extrainput') == '' ? NULL : $this->input->post('extrainput')
        );
        return $this->db->insert('types', $data);
    }
    
    /**
     * Delete a leave type from the database
     * @param int $id identifier of the leave type
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function deleteType($id) {
        $this->db->delete('types', array('id' => $id));
    }
    
    /**
     * Update a given leave type in the database.
     * @param int $id identifier of the leave type
     * @param string $name name of the type
     * @param bool $deduct Deduct days off
     * @param string $acronym Acronym of leave type
     * @return int number of affected rows
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function updateTypes($id, $name, $deduct, $acronym, $nodeduction=NULL, $noapproval=NULL, $approvebyadmin=NULL, $extrainput=NULL, $limit=NULL, $color=NULL, $textcolor=NULL) {
        $deduct = ($deduct == 'on')?TRUE:FALSE;
        $data = array(
            'acronym' => $acronym,
            'name' => $name,
            'deduct_days_off' => $deduct,
            'noapproval' => $noapproval == 0 ? NULL : $noapproval,
            'approvebyadmin' => $approvebyadmin == 0 ? NULL : $approvebyadmin,
            'nodeduction' => $nodeduction == 0 ? NULL : $nodeduction,
            'extrainput' => $extrainput == '' ? NULL : $extrainput,
            'limit' => $limit == 0 ? NULL : $limit,
            'color' => $color,
            'textcolor' => $textcolor
        );
        $this->db->where('id', $id);
        return $this->db->update('types', $data);
    }


    public function getTypesWithExtraInput(){
        $this->db->select('id, extrainput');
        $this->db->from('types');
        $this->db->where('extrainput IS NOT NULL');
        return $this->db->get()->row_array();
    }

    /**
     * Count the number of time a leave type is used into the database
     * @param int $id identifier of the leave type record
     * @author Benjamin BALET <benjamin.balet@gmail.com>
     */
    public function usage($id) {
        $this->db->select('COUNT(*)');
        $this->db->from('leaves');
        $this->db->where('type', $id);
        $query = $this->db->get();
        $result = $query->row_array();
        return $result['COUNT(*)'];
    }
}
