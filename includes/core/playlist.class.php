<?php
/*
 * Class: PlayList
 * ~~~~~~~~~~~~~~~
 * » Provides and handles a Playlist for Maps.
 *
 * ----------------------------------------------------------------------------------
 * Author:	undef.de
 * Date:	2015-08-30
 * Copyright:	2015 by undef.de
 * ----------------------------------------------------------------------------------
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ----------------------------------------------------------------------------------
 *
 * Dependencies:
 *  - none
 *
 */


/*
#///////////////////////////////////////////////////////////////////////#
#									#
#///////////////////////////////////////////////////////////////////////#
*/

class PlayList {
	private $debug		= false;
	private $playlist	= array();

	/*
	#///////////////////////////////////////////////////////////////////////#
	#									#
	#///////////////////////////////////////////////////////////////////////#
	*/

	public function __construct ($debug) {
		$this->debug = $debug;
	}

//	/*
//	#///////////////////////////////////////////////////////////////////////#
//	#									#
//	#///////////////////////////////////////////////////////////////////////#
//	*/
//
//	public function readMapHistory () {
//		global $aseco;
//
//		// Read MapHistory
//		$query = "
//		SELECT
//			`MapId`,
//			`Date`
//		FROM `%prefix%maphistory`
//		ORDER BY `Date` DESC;
//		";
//
//		$result = $aseco->db->query($query);
//		if ($result) {
//			if ($result->num_rows > 0) {
//				$this->history = array();
//
//				while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
//					$map = $aseco->server->maps->getMapById($row['MapId']);
//					if (isset($map->id) && $map->id > 0) {
//						$this->history[$map->id] = array(
//							'played'	=> $row['Date'],
//							'id'		=> $map->id,
//							'uid'		=> $map->uid,
//						);
//					}
//
//					if (count($this->history) >= $this->settings['max_history_entries']) {
//						break;
//					}
//				}
//
//				// Clean up the MapHistory table
////				$aseco->db->begin_transaction();		// Require PHP >= 5.5.0
//				$aseco->db->query('START TRANSACTION;');
//				$query = "TRUNCATE TABLE `%prefix%maphistory`;";
//
//				$aseco->db->query($query);
//				if ($aseco->db->affected_rows === 0) {
//					$values = array();
//					foreach ($this->history as $item) {
//						$values[] = "(". $item['id'] .", ". $aseco->db->quote($item['played']) .")";
//					}
//					if (count($values) > 0) {
//						$query = "
//						INSERT INTO `%prefix%maphistory` (
//							`MapId`,
//							`Date`
//						)
//						VALUES ". implode(',', $values) .";
//						";
//
//						$result2 = $aseco->db->query($query);
//						if ($aseco->db->affected_rows === -1) {
//							if ($aseco->debug) {
//								trigger_error('[MapHistory] readMapHistory(): Could not clean up table "maphistory": ('. $aseco->db->errmsg() .') for statement ['. $query .']', E_USER_WARNING);
//							}
//							$aseco->db->rollback();
//						}
//						else {
//							$aseco->db->commit();
//						}
//					}
//					else {
//						$aseco->db->commit();
//					}
//				}
//			}
//			$result->free_result();
//		}
//	}
//
//	/*
//	#///////////////////////////////////////////////////////////////////////#
//	#									#
//	#///////////////////////////////////////////////////////////////////////#
//	*/
//
//	public function addMapToHistory ($map) {
//		global $aseco;
//
//		if (isset($map->id) && $map->id > 0 && isset($aseco->server->maps->map_list[$map->uid])) {
//
//			// Update MapHistory in DB
//			$query = "
//			INSERT INTO `%prefix%maphistory` (
//				`MapId`,
//				`Date`
//			)
//			VALUES (
//				". $map->id .",
//				". $aseco->db->quote(date('Y-m-d H:i:s', time())) ."
//			);
//			";
//
//			$aseco->db->query($query);
//			if ($aseco->db->affected_rows === -1) {
//				if ($aseco->debug) {
//					trigger_error('[MapHistory] addMapToHistory(): Could not add Map into table "maphistory": ('. $aseco->db->errmsg() .') for statement ['. $query .']', E_USER_WARNING);
//				}
//				return false;
//			}
//			else {
//				return true;
//			}
//		}
//	}

	/*
	#///////////////////////////////////////////////////////////////////////#
	#									#
	#///////////////////////////////////////////////////////////////////////#
	*/

//	public function addMapToPlaylist ($uid, $login, $skippable = true, $first_position = false) {
	public function addMapToPlaylist ($uid, $login) {
		global $aseco;

//		Test "SetNextMapIdent" instead of "ChooseNextMap"
//		Test "JumpToMapIdent"

		// Check for a Map which has to be "present in the selection" of the dedicated server
		$map = $aseco->server->maps->getMapByUid($uid);
		if ($map->id > 0) {
			// If Playlist is empty, setup the next Map at the dedicated server
			if (count($this->playlist) == 0) {
				try {
					// Set the next Map
					$result = $aseco->client->query('SetNextMapIdent', $map->uid);
					$aseco->dump($result, $uid, $login);
				}
				catch (Exception $exception) {
					$aseco->console('[Playlist] Exception occurred: ['. $exception->getCode() .'] "'. $exception->getMessage() .'" - ChooseNextMap');
				}
			}
		}
	}

	/*
	#///////////////////////////////////////////////////////////////////////#
	#									#
	#///////////////////////////////////////////////////////////////////////#
	*/

	public function isMapInPlaylistById ($id) {
		if (!empty($id)) {
			foreach ($this->playlist as $item) {
				if ($item['id'] == $id) {
					return true;
				}
			}
		}
		return false;
	}

	/*
	#///////////////////////////////////////////////////////////////////////#
	#									#
	#///////////////////////////////////////////////////////////////////////#
	*/

	public function isMapInPlaylistByUid ($uid) {
		if (!empty($uid)) {
			foreach ($this->playlist as $item) {
				if ($item['uid'] == $uid) {
					return true;
				}
			}
		}
		return false;
	}
}

?>
