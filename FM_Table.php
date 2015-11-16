<?php

class FM_Table
{
	public static function getAsObject($records, $fields, $relatedSets){
		$result = [];
		foreach ($records as $record) {
			$r = [];
			foreach ($fields as $attribute => $field) {
				$ts = strpos($attribute, "timestamp");
				if ($ts === false) {
					$r[$attribute] = $record->getField($field);
				} else {
					$r[$attribute] = $record->getFieldAsTimestamp($field);
				}
				if (!is_null($relatedSets)) {
					foreach ($relatedSets as $relatedSetName => $class) {
						$relatedRecords = $record->getRelatedSet($relatedSetName);
						if (!Filemaker::isError($relatedSet)) {
							$relatedObjects = $class::getAsObject($relatedRecords, $class::$fields);
							$r[$relatedSetName] = $relatedObjects;
						}
					
					}
				}
			}
			$result[] = $r;
		}
		return $result;
	}

	public static function getAsParameter($request, $fields){
		$result = [];
		foreach ($request as $attribute => $value) {
			$field = $fields[$attribute];
			$result[$field] = $value;
		}
		return $result;
	}

	public static function summarize($records, $field){
		foreach ($records as $record) {
			$value = $record[$field];
			$sum = $sum + $value;
		}
		$sum = ( $sum / 60 / 60 );
		return $sum;
	}
}
?>