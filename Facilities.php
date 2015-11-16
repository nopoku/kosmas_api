<?php
require_once "kosmas_api.php";

class Facilities extends FM_Table
{
	private static $layout = "API_FACILITIES";
	private static $fields = array(
			"id" => "_kp_FacilityID_t",
			"company_id" => "_kf_CompanyID_t",
			"address_id" => "_kf_DetailID_PrimaryAddress_t",
			"phone_id" => "_kf_DetailID_PrimaryPhone_t",
			"display" => "Name_Display_calc",
			"name_internal" => "Name_Internal_t",
			"name_official" => "Name_Official_t",
			"scheduled" => "Schedule_n",
			"status" => "Status_t",
		);

	public static function getAsObject($records, $fields, $relatedSets){
		$result = parent::getAsObject($records, $fields, $relatedSets);
		return $result;
	}

	public static function getAsParameter($request, $fields){
		$result = parent::getAsParameter($request, $fields);
		return $result;
	}

	public static function getFields(){
		return self::$fields;
	}

	public function find($parameter, $connection, $relatedSets){
		$parameter = self::getAsParameter($parameter, self::$fields);
		$find = $connection->newFindCommand(self::$layout);
		foreach ($parameter as $field => $value) {
			$find->addFindCriterion($field, $value);
		}
		$result = $find->execute();

		if (Filemaker::isError($result)) {
			echo $result->getMessage();
			return null;
		}

		$records = $result->getRecords();

		$result = $this->getAsObject($records, self::$fields, $relatedSets);
		
		return $result;
	}
}
?>