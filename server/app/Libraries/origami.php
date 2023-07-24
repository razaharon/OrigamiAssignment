<?php

namespace App\Libraries;
class origami
{

	private $username, $password, $account;

	public function set_connection($details = array())
	{

		if (!@$details["username"] || !@$details["password"] || !@$details["account"])
			return array("error" => "missing details");

		$this->username = $details["username"];
		$this->password = $details["password"];
		$this->account = $details["account"];

	}



	public function query($details = array())
	{
		if (!@$details["module"] || !@$details["action"] || (!@$details["data"] && !@$details["files_data"]))
			return array("error" => "missing details");

		$module = $details["module"];
		$action = $details["action"];
		$data = @$details["data"];
		$files_data = @$details["files_data"];

		return $this->execute($module, $action, $data, $files_data);
	}

	private function execute($module, $action, $data = null, $files_data = null)
	{
		$url = 'https://' . $this->account . '.origami.ms/' . $module . '/api/' . $action . "/format/json?";

		$general_details = array(
			'username' => $this->username,
			'password' => $this->password
		);

		$ch = curl_init($url);

		if ($files_data) {
			if ((version_compare(PHP_VERSION, '5.5') >= 0)) {

				foreach ($files_data as &$f) {
					$tmp = new CURLFile(str_replace('@', '', $f));
					$f = $tmp;
				}

				curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
			}

			$postdata = array_merge($general_details, $files_data);

		} else {
			$postdata = http_build_query(
				array_merge($general_details, $data)
			);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$result = curl_exec($ch);
		curl_close($ch);


		if ($rs = json_decode($result, true))
			return $rs;
		else {
			echo $result;
			die("Server Error");
		}
	}


}