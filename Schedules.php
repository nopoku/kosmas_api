<?php
require_once "kosmas_api.php";

class Schedules extends FM_Table
{
	private static $layout = "API_SCHEDULING";
	private static $fields = array(
			"id" => "_kp_ScheduleID_t",
			"division_id" => "_kf_DivisionID_t",
			"facility_id" => "_kf_FacilityID_t",
			"job_id" => "_kf_JobID_t",
			"sales_order_id" => "_kf_Sales_OrderID_t",
			"crew" => "Crew_Assigned_calc",
			"crew_min" => "Crew_Min_n",
			"crew_max" => "Crew_Max_n",
			"date_start" => "Date_Start_d",
			"date_end" => "Date_Finish_d",
			"description" => "Description_t",
			"cost_display" => "Display_Cost_calc",
			"display" => "ID_Display_t",
			"address" => "Location_Address_t",
			"resource_list" => "Resource_List_t",
			"status" => "Statis_t",
			"time_start" => "Time_Start_ti",
			"time_end" => "Time_Finish_ti",
			"time_duration" => "Time_Duration_calc",
			"timestamp_start" => "Timestamp_Start_calc",
			"timestamp_end" => "Timestamp_End_calc",
			"title" => "Title_t",
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

	public function getCalendarResources($connection, $group){
		$personnel = new Personnel;
		$clients = new Clients;

		$personnel_list = $personnel->find(null, $connection);
		$client_list = $clients->find(null, $connection);

		$resources = [];

		switch ($group) {		
			case 'Client':
				foreach ($client_list as $clientRecord) {
				
					foreach ($personnel_list as $personnelRecord) {
						$r = [];

						$r["id"] = $personnelRecord["id"];
						$r["clientName"] = $clientRecord["name"];
						$r["title"] = $personnelRecord["name"];

						$resources[] = $r;
					}

				};
				break;

			default:
				foreach ($personnel_list as $personnelRecord) {
						
					foreach ($client_list as $clientRecord) {
						$r = [];

						$r["id"] = $clientRecord["id"];
						$r["personnelName"] = $personnelRecord["name"];
						$r["title"] = $clientRecord["name"];

						$resources[] = $r;
					}

				};
				break;
		}
		$resources = json_encode($resources);
		return $resources;
	}

	public function getCalendarEvents($findObject, $connection, $resource){
		$relatedSets = [];
		if ($resource = "cost") {
			$relatedSets["API_Scheduling|JOBS"] = "Jobs";
			$relatedSets["API_Scheduling|FACILITIES"] = "Facilities";
			$relatedSets["API_Scheduling|DIVISIONS"] = "Divisions";
			$relatedSets["API_Scheduling|SALES_ORDERS"] = "Sales_Orders";
		} else {
			$relatedSets["API_Scheduling|HR"] = "Human_Resources";
		}
		
		$eventSearchResult = $this->find($findObject, $connection, $relatedSets);
		
		$events = [];
		foreach ($eventSearchResult as $event) {
			$e = [];

			$e["id"] 			= $event["id"];
			$e["start"] 		= gmdate('Y-m-d\TH:i:s\Z', $event["start_unix"]);
			$e["end"] 			= gmdate('Y-m-d\TH:i:s\Z', $event["end_unix"]);
			$e["startTime"] 	= gmdate('H:i:s', $event["start_unix"]);
			$e["endTime"] 		= gmdate('H:i:s', $event["end_unix"]);
			$e["date"] 			= gmdate('Y-m-d', $event["start_unix"]);

			switch ($resource) {
				case 'Client':
					$e["resourceId"] = $event["personnel_id"];
					break;

				default:
					$e["resourceId"] = $event["client_id"];
					break;
			}

			$e["title"] 		= $event["title"];
			$e["description"] 	= $event["description"];
			$e["service_id"] 	= $event["service_id"];
			$e["serviceName"] 	= $event["serviceName"];
			$e["client_id"] 	= $event["client_id"];
			$e["clientName"] 	= $event["clientName"];
			$e["personnel_id"] 	= $event["personnel_id"];
			$e["personnelName"] = $event["personnelName"];

			if ($event["allDay"] == "1") {
				$e["allDay"] 	= true;
			}

			$events[] = $e;
		}

		$events = json_encode($events);
		return $events;
	}

}
?>