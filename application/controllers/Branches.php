<?php
use Restserver \Libraries\REST_Controller ;
Class Branches extends REST_Controller {
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, ContentLength, Accept-Encoding");
        parent::__construct();
        $this->load->model('BranchesModel');
        $this->load->library('form_validation');
    }
    
    public function index_get() {
        return $this->returnData($this->db->get('branches')->result(), false);
    }
    
    public function index_post($id = null) {
        $validation = $this->form_validation;
        $rule = $this->BranchesModel->rules();
        if($id == null){
            array_push($rule,[
                    'field' => 'name',
                    'label' => 'name',
                    'rules' => 'required|alpha'
                ],
                [
                    'field' => 'address',
                    'label' => 'address',
                    'rules' => 'required'
                ],
                [
                    'field' => 'phoneNumber',
                    'label' => 'phoneNumber',
                    'rules' => 'required|is_unique[branches.phoneNumber]|integer'
                ]
            );
        }
        else{
            array_push($rule,
                [
                    'field' => 'phoneNumber',
                    'label' => 'phoneNumber',
                    'rules' => 'required|is_unique[branches.phoneNumber]|integer'
                ]
            );
        }
        $validation->set_rules($rule);
        if (!$validation->run()) {
            return $this->returnData($this->form_validation->error_array(), true);
        }
        $branch = new branchesData();
        $branch->name = $this->post('name');
        $branch->address = $this->post('address');
        $branch->phoneNumber = $this->post('phoneNumber');
        $branch->created_at = $this->post('created_at', 'NOW()', FALSE);
        if($id == null){
            $response = $this->BranchesModel->store($branch);
        }
        else{
            $response = $this->BranchesModel->update($branch,$id);
        }
        return $this->returnData($response['msg'], $response['error']);
    }
            
    public function index_delete($id = null){
        if($id == null){
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->BranchesModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }
    
    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}
Class branchesData{
    public $name;
    public $address;
    public $phoneNumber;
    public $created_at;
}