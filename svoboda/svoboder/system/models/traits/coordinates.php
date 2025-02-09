<?php

declare(strict_types=1);

namespace svoboda\svoboder\models\traits;

/**
 * Coordinates
 *
 * Storage of data in the document from ArangoDB
 *
 * @method int|float distance(float $from_latitude, float $from_longitude, float $to_latitude, float $to_longitude, int $planet) 
 *
 * @package svoboda\svoboder\models\traits
 *
 * @license http://www.wtfpl.net/ Do What The Fuck You Want To Public License
 * @author Arsen Mirzaev Tatyano-Muradovich <arsen@mirzaev.sexy>
 */
trait coordinates
{
	/**
	 * Distance
	 *
	 * Calculate the distance between coordinates using the Vincenty formula
	 *
	 * @see https://en.wikipedia.org/wiki/Great-circle_distance
	 *
	 * @param float $from_latitude From latitude
	 * @param float $from_longitude From longitude
	 * @param float $to_latitude To latitude
	 * @param float $to_longitude To longitude
	 * @param int $planet Radius of the planet
	 *
	 * @return int|float Calculated distance between coordinates (meters)
	 */
	public static function distance(
		float $from_latitude,
		float $from_longitude,
		float $to_latitude,
		float $to_longitude,
		int $planet = 6371000
	): int|float {
		// Initializing the from coordinates
		$from = [
			'latitude' => deg2rad($from_latitude),
			'longitude' => deg2rad($from_longitude)
		];

		// Initializing the to coordinates
		$to = [
			'latitude' => deg2rad($to_latitude),
			'longitude' => deg2rad($to_longitude)
		];

		// Calculating longitude delta
		$delta = $to['longitude'] - $from['longitude'];

		// Calculating (wtf)
		$biba = pow(cos($to['latitude']) * sin($delta), 2) + pow(cos($from['latitude']) * sin($to['latitude']) - sin($from['latitude']) * cos($to['latitude']) * cos($delta), 2);
		$boba = sin($from['latitude']) * sin($to['latitude']) + cos($from['latitude']) * cos($to['latitude']) * cos($delta);
		$angle = atan2(sqrt($biba), $boba);

		// Exit (success)
		return $angle * $planet;
	}
}
