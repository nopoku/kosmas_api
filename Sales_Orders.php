<?php
require_once "kosmas_api.php";

class Sales_Orders extends FM_Table
{
	private static $layout = "API_SALES_ORDERS";
	private static $fields = array(
			"id" => "_kp_Sales_OrderID_t",
			"account_id" => "_kf_AccountID_t",
			"account_sub_id" => "_kf_Account_SubID_t",
			"company_id" => "_kf_CompanyID_t",
			"contact_id" => "_kf_ContactID_t",
			"jobsite_address_id" => "_kf_DetailID_Jobsite_Address_t",
			"division_id" => "_kf_DivisionID_t",
			"entry_id" => "_kf_EntryID_t",
			"facility_id" => "_kf_FacilityID_t",
			"approval" => "Approval_t",
			"approval_timestamp" => "Approval_ts",
			"bond" => "Bond_n",
			"customer_id" => "Customer_ID_t",
			"date" => "Date_d",
			"description" => "Description_t",
			"display" => "DisplayIDAccount_calc",
			"id_display" => "ID_Display_t",
			"locked" => "Locked_n",
			"stage" => "Stage_t",
			"status" => "Status_t",
			"total" => "Summary_Sell_calc",
			"tax" => "Tax_calc",
			"subtotal" => "Summary_PreTax_Subtotal_calc",
			"type" => "Type_t",
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