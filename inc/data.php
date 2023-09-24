<?php
	class Data {
		private $db = null;
		const TABLE = "album";
		const CONFIG = "config";

		function __construct($dbFile) {
			$this->db = new PDO("sqlite:".$dbFile);
		}

		public function getDB() { return $this->db; }

		public function getConfig() {
			$config = array();

			$data = $this->db->query("SELECT * FROM ".self::CONFIG);
			$data->setFetchMode(PDO::FETCH_ASSOC);
			$arr = $data->fetchAll();
			for ($i=0;$i<count($arr);$i++) {
				$row = $arr[$i];
				$config[$row["name"]] = $row["value"];
			}
			return $config;
		}

		public function updateConfig($arr) {
			foreach ($arr as $key=>$value) {
				$n = $this->db->quote($key);
				$v = $this->db->quote($value);
				$q = "UPDATE ".self::CONFIG." SET value=".$v." WHERE name=".$n;
				$this->db->query($q);
			}
		}

		public function lookupAlbum($year, $str) {
			$s = $this->db->quote($str);
			$y = (int) $year;
			$q = "SELECT id FROM ".self::TABLE." WHERE visible=1 AND shortcut=".$s." AND year=".$y;
			$data = $this->db
				->query($q)
				->fetchAll();
			if (count($data)) {
				$row = $data[0];
				$id = $row["id"];
				return $id;
			}
			return null;
		}

		public function lookupChapter($str, $id_album) {
			$s = $this->db->quote($str);
			$id = (int) $id_album;
			$data = $this->db
				->query("SELECT id FROM ".self::TABLE." WHERE id_album=".$id." AND visible=1 AND shortcut=".$s)
				->fetchAll();
			if (count($data)) {
				$row = $data[0];
				$id = $row["id"];
				return $id;
			}
			return null;
		}

		public function lookupDirectory($directory) {
			$s = $this->db->quote($directory);
			$data = $this->db
				->query("SELECT id FROM ".self::TABLE." WHERE directory=".$s)
				->fetchAll();
			if (count($data)) {
				$row = $data[0];
				$id = $row["id"];
				return $id;
			}
			return null;
		}

		public function deleteItem($id) {
			$this->db->query("DELETE FROM ".self::TABLE." WHERE id=".$id);
		}

		public function getMaxOrdering($id_album) {
			$id = $this->db->quote($id_album);
			$result = $this->db->query("SELECT MAX(ordering) AS 'm' FROM ".self::TABLE." WHERE id_album=".$id);
			$result = $result->fetchAll();
			return (int) $result[0]["m"];
		}

		public function getItem($id) {
			$i = (int) $id;
			$data = $this->db->query("SELECT * FROM ".self::TABLE." WHERE id=".$id." LIMIT 1");
			$data->setFetchMode(PDO::FETCH_ASSOC);
			$data = $data->fetchAll();
			if (count($data)) {
				$row = $data[0];
				if (!$row["year"]) {
					$data = $this->db->query("SELECT year FROM ".self::TABLE." WHERE id=".$row["id_album"]);
					foreach ($data as $row2) { $row["year"] = $row2["year"]; }
				}
				return $row;
			}
			return null;
		}

		public function insertItem($values) {
			$result = $this->db->query("INSERT INTO ".self::TABLE." DEFAULT VALUES");
			if (!$result) {
				echo implode(", ", $this->db->errorInfo())."\n";
				return null;
			}
			$id = $this->db->lastInsertId();
			$this->updateItem($id, $values);
			return $id;
		}

		public function updateItem($id, $values) {
			$arr = array();
			foreach ($values as $key=>$value) {
				$arr[] = $key."=".$this->db->quote($value);
			}
			if (array_key_exists("start", $values)) { $arr[] = "year=" . substr($values["start"], 0, 4); }
			$list = implode(", ", $arr);
			$result = $this->db->query("UPDATE ".self::TABLE." SET ".$list." WHERE id=".$id);
			if (!$result) {
				echo implode(", ", $this->db->errorInfo())."\n";
				return false;
			}
			return true;
		}

		public function getYears() {
			$q = "SELECT DISTINCT year FROM album WHERE id_album IS NULL ORDER BY year DESC";
			$data = $this->db->query($q);
			$result = array();
			foreach ($data as $row) {
				$result[] = $row[0];
			}
			return $result;
		}

		public function getAlbums($onlyVisible, $year = false, $limit = false) {
			$collist = array("id", "name", "start", "end", "year", "visible", "shortcut", "photo", "directory", "public");
			$q = $this->select($collist) . "id_album IS NULL";
			if ($year) { $q .= " AND year=".$year; }
			if ($onlyVisible) { $q .= " AND visible=1"; }
//			$q .= " ORDER BY end DESC, start DESC";
			$q .= " ORDER BY start ASC, end ASC, directory ASC";
			if ($limit) { $q .= " LIMIT " . $limit; }
			$data = $this->db->query($q);
			$data->setFetchMode(PDO::FETCH_ASSOC);
			return $data->fetchAll();
		}

		public function getChapters($id_album, $onlyVisible) {
			$collist = array("id", "name", "shortcut", "visible", "ordering", "directory");
			$q = $this->select($collist)."id_album=".$id_album;
			if ($onlyVisible) { $q .= " AND visible=1"; }
			$q .= " ORDER BY ordering ASC";
			$result = $this->db->query($q);
			$result->setFetchMode(PDO::FETCH_ASSOC);
			$result = $result->fetchAll();
			return $result;
		}

		public function moveChapter($id_chapter, $direction) {
			$r = $this->db->query("SELECT id_album, ordering FROM ".self::TABLE." WHERE id=".$id_chapter)->fetchAll();
			if (!count($r)) { return; }
			$id_album = $r[0]["id_album"];
			$order_1 = $r[0]["ordering"];

			$order = ($direction == -1 ? "DESC" : "ASC");
			$operator = ($direction == -1 ? "<" : ">");
			$r = $this->db
					->query("SELECT id, ordering FROM ".self::TABLE."
								WHERE id_album=".$id_album."
								AND ordering ".$operator." ".$order_1."
								ORDER BY ordering ".$order." LIMIT 1")
					->fetchAll();
			if (!count($r)) { return; }

			$id2 = $r[0]["id"];
			$order_2 = $r[0]["ordering"];

			/* swap */
			$this->db->query("UPDATE ".self::TABLE." SET ordering=".$order_1." WHERE id=".$id2);
			$this->db->query("UPDATE ".self::TABLE." SET ordering=".$order_2." WHERE id=".$id_chapter);
		}

		public function getNearAlbums($data, $count, $year) {
			$collist = array("id", "name", "shortcut", "start", "end", "year");
			$total = 2*$count+1;
			$y = (int) $year;

			$q = $this->select($collist);
//			$q .= "visible=1 ORDER BY end DESC, start DESC";
			$q .= "visible=1 AND year=$y ORDER BY start ASC, end ASC, directory ASC";

			$result = $this->db->query($q)->fetchAll();

			$index = -1;
			for ($i=0;$i<count($result);$i++) {
				if ($result[$i]["id"] == $data["id"]) { $index = $i; }
			}
			while (count($result) > $total && $index > $count) {
				array_shift($result);
				$index--;
			}

			while (count($result) > $total) { array_pop($result); }

			return $result;
		}

		private function select($collist) {
			return "SELECT ".implode(", ",$collist)." FROM ".self::TABLE." WHERE ";
		}
	}
?>
