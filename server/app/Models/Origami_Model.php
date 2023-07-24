<?php 
namespace App\Models;

use App\Libraries\origami;

class Origami_Model
{

	private $token_time = "+90 day";
	private $token_remember = "+90 day";

	private $username = 'demo14_test@origami.ms';
	private $password = 'asd44f4511dzx';
	private $account = 'demo14';

	public function __construct()
	{
        $this->origami = new origami();
		$this->origami->set_connection(
			array(
				'username' => $this->username,
				'password' => $this->password,
				'account' => $this->account
			)
		);
	}


	public function normilize_response_data(&$rs, $group_indexes = false)
	{
		$data = array();


		if (@$rs["info"]["total_count"] >= 1) {
			$i = 0;

			foreach ($rs["data"] as $instance_data) {
				$data[$i]["id"] = $instance_data["instance_data"]["_id"];
				$data[$i]["insertTimestamp"] = $instance_data["instance_data"]["insertTimestamp"];


				foreach ($instance_data["instance_data"]["field_groups"] as $field_groups) {
					if (count(@$field_groups["fields_data"])) // LB Custom
						foreach ($field_groups["fields_data"] as $fields_data) {

							$fields = array();
							foreach ($fields_data as $fld) {
								if (isset($fld["value"]))
									$fields[$fld["field_data_name"]] = $fld["value"];
								else
									$fields[$fld["field_data_name"]] = @$fld["default_value"];

							}


							if ($field_groups["field_group_data"]["repeatable_group"] == "1") {
								if (!$group_indexes)
									$data[$i][$field_groups["field_group_data"]["group_data_name"]][] = $fields;
								else
									$data[$i][$field_groups["field_group_data"]["group_data_name"]][$fld["group_index"]] = $fields;
							} else
								$data[$i][$field_groups["field_group_data"]["group_data_name"]] = $fields;

						}

				}

				$i++;
			}

			$rs = $data;
		}

	}

	public function generate_token($data, $remember = false)
	{
		if (!$remember)
			$token_date = date("U", strtotime($this->token_time));
		else
			$token_date = date("U", strtotime($this->token_remember));

		$token_key = array($data, $token_date, rand(99, 999));

		return $this->encrypt->encode(json_encode($token_key, true));
	}

	public function check_token($token)
	{
		$token = json_decode($this->encrypt->decode($token));

		if (count($token) != 3)
			return array("error" => "wrong token");

		$current_time = date("U");

		if ($token[1] < $current_time)
			return array("error" => "token expired");


		return array("ok" => $token[0]);
	}


	public function structure($entity_data_name)
	{

		$data = array();
		$data['entity_data_name'] = $entity_data_name;

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'entity_structure',
				'data' => $data
			)
		);

		return $rs;
	}

	public function create($entity_data_name, $form_data)
	{

		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['form_data'] = (!is_array($form_data)) ? json_decode($form_data, true) : $form_data;

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'create_instance',
				'data' => $data
			)
		);

		return $rs;
	}

	public function select($entity_data_name, $filter = "", $return_fields = "", $return_groups = "", $limit = "", $orederby = "", $specific = false)
	{
		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['filter'] = $filter;
		$data['limit'] = $limit;
		$data['orederby'] = $orederby;
		$data['return_fields'] = $return_fields;
		$data['return_groups'] = $return_groups;
		$data['specific'] = $specific;
		$data['type'] = "2";

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'instance_data',
				'data' => $data
			)
		);

		return $rs;
	}

	public function select_protected($entity_data_name, $filter = "", $return_fields = "", $return_groups = "", $limit = "", $orederby = "")
	{
		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['filter'] = $filter;
		$data['limit'] = $limit;
		$data['orederby'] = $orederby;
		$data['return_fields'] = $return_fields;
		$data['return_groups'] = $return_groups;

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'instance_data_protected',
				'data' => $data
			)
		);

		return $rs;
	}


	public function remove($entity_data_name, $_ids = "")
	{

		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['_ids'] = $_ids;

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'remove_instance',
				'data' => $data
			)
		);

		return $rs;
	}

	public function upload_file($data)
	{

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'upload_file',
				'files_data' => $data
			)
		);


		return $rs;

	}

	public function update($entity_data_name, $filter, $fields)
	{

		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['filter'] = $filter;

		foreach ($fields as $key => $val) {
			if (is_array($val)) {
				$val = json_encode($val);
				$data['field'][] = json_encode([$key, $val]);
			} else
				$data['field'][] = json_encode([$key, $val]);
		}

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'update_instance_fields',
				'data' => $data
			)
		);

		return $rs;
	}

	public function add_new_group($_id, $entity_data_name, $group_data_name, $group_data)
	{

		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['group_data_name'] = $group_data_name;
		$data['group_data'] = $group_data;
		$data['_id'] = $_id;


		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'add_group_repetition',
				'data' => $data
			)
		);

		return $rs;
	}

	public function update_group($entity_data_name, $filter, $fields, $group_index)
	{

		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['filter'] = $filter;

		foreach ($fields as $key => $val) {
			if (is_array($val)) {
				$val = json_encode($val);
				$data['field'][] = '["' . $key . '",' . $val . ',' . $group_index . ']';
			} else
				$data['field'][] = '["' . $key . '","' . $val . '",' . $group_index . ']';
		}

		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'update_instance_fields',
				'data' => $data
			)
		);

		return $rs;
	}

	public function remove_group($_id, $entity_data_name, $group_data_name, $index)
	{
		$data = array();
		$data['entity_data_name'] = $entity_data_name;
		$data['group_data_name'] = $group_data_name;
		$data['_id'] = $_id;
		$data['index'] = $index;



		$rs = $this->origami->query(
			array(
				'module' => 'entities',
				'action' => 'remove_group_repetition',
				'data' => $data
			)
		);

		return $rs;
		/*
			  $data = array();
			  $data['entity_data_name'] = $entity_data_name;
			  $data['group_data_name'] = $group_data_name;
			  $data['_id'] = $_id;
			  $data['index'] = $index;
			  
			  $rs = $this->origami->query(array(
				  'module' => 'entities',
				  'action' => 'remove_group_repetition',
				  'data' => $data 
			  ));
			  
			  return $rs;
			  */
	}
}