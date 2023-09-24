<?php
	class Dispatcher {
		const NOTFOUND = 0;
		const AUTH = 1;
		const ALBUM = 2;
		const CHAPTER = 3;
		const INDEX = 4;
		const PASS = 5;
		const YEAR = 6;

		private $result = null;
		private $id = null;
		private $data = null;
		private $authorized = false;

		public function __construct($data) {
			$this->data = $data;
			$this->result = self::NOTFOUND;

			if (array_key_exists("pass", $_POST)) {
				$hash = $this->data->getConfig()["hash-user"];
				$pass = $_POST["pass"];
				if (password_verify($pass, $hash)) {
					$_SESSION["auth"] = 1;
					$this->authorized = true;
				}
			} else {
				$this->authorized = array_key_exists("auth", $_SESSION);
			}

			$parts = $this->getUriParts();
			if (count($parts) == 0) {
				$this->result = ($this->checkAuthForIndex() ? self::INDEX : self::AUTH);
				return;
			}

			$year = $parts[0];

			if (count($parts) == 1) {
				if (is_numeric($year)) {
					$id = (int) $year;
					$this->result = ($this->checkAuthForIndex() ? self::YEAR : self::AUTH);
				} else {
					$this->result = self::INDEX;
				}
				return;
			}

			if ($year == "data") { /* access large image file */
				$path = implode("/", array_slice($parts, 2, -2));
				$id = $this->data->lookupDirectory($path);
				if ($this->checkAuthForId($id)) {
					$this->result = self::PASS;
				} else {
					$this->result = self::NOTFOUND;
				}
				return;
			}

			$shortcut = $parts[1];

			$id = $data->lookupAlbum($year, $shortcut);
			if (!$id) { return; }
			if (!$this->checkAuthForId($id)) {
				$this->result = self::AUTH;
				return;
			}
			if (count($parts) == 2) {
				$this->id = $id;
				$this->result = self::ALBUM;
				return;
			}

			$chapter = $parts[2];
			$id = $data->lookupChapter($chapter, $id);
			if (!$id) { return; }
			if (!$this->checkAuthForId($id)) {
				$this->result = self::AUTH;
				return;
			}

			$this->id = $id;
			$this->result = self::CHAPTER;
		}

		private function getUriParts() {
			if (!isset($_SERVER["REQUEST_URI"])) { return array(); }

			$uri = $_SERVER["REQUEST_URI"];
			$parts = explode("/", $uri);
			$arr = array();
			for ($i=0;$i<count($parts);$i++) {
				$part = $parts[$i];
				if ($part) { $arr[] = $part; }
			}

			return $arr;
		}

		private function checkAuthForIndex() {
			return $this->authorized;
		}

		private function checkAuthForId($id) {
			$item = $this->data->getItem($id);
			if (!$item) { return false; } // not found

			$public = ($item["public"] == "1");

			return ($public || $this->authorized);
		}

		public function getResult() {
			return $this->result;
		}

		public function getId() {
			return $this->id;
		}

		public function isAuthorized() {
			return $this->authorized;
		}
	}
?>
