<?php
	include("xml.php");

	class Builder {
		private $data = null;
		const DATA = "data/";
		const USER = 0;
		const ADMIN = 1;
		const RSS = 2;

		function __construct($data, $mode, $authorized) {
			$this->data = $data;
			$this->config = $this->data->getConfig();
			$this->authorized = $authorized;

			switch ($mode) {
				case self::USER:
					$this->xml = new XML("user");
					$this->xml->setLanguage($this->getLanguage());
				break;
				case self::ADMIN:
					$this->xml = new XML("admin");
				break;
				case self::RSS:
					$this->xml = new XML("rss", array("version"=>"2.0"));
				break;
			}
		}

		public function output($manual) {
			$this->xml->output($manual);
		}

		public function setError($error) {
			$this->xml->setError($error);
		}

		/* === USER METHODS === */

		public function buildRSS() {
			$channel = $this->xml->addElement("channel");

			$title = $this->xml->addElement("title", false, $channel, $this->config["name"]);
			$desc = $this->xml->addElement("description", false, $channel, $this->config["name"]);
			$link = $this->xml->addElement("link", false, $channel, "http://".$_SERVER["HTTP_HOST"]."/");

			$data = $this->data->getAlbums(true, false, 10);
			foreach ($data as $album) {
				$url = "https://".$_SERVER["HTTP_HOST"]."/" . $album["shortcut"];
				$item = $this->xml->addElement("item", false, $channel);
				$title = $this->xml->addElement("title", false, $item, $album["name"]);
				$link = $this->xml->addElement("link", false, $item, $url);
				$guid = $this->xml->addElement("guid", false, $item, $url);
			}
		}

		public function buildIndex() {
			$this->xml->setXSLT("user/index");

			$data = $this->data->getAlbums(true);
			$years = $this->xml->addElement("years");
			$albums = $this->xml->addElement("albums");

			foreach ($data as $item) {
				$arr = array();

				$arr["href"] = "/" . $item["year"] . "/" . $item["shortcut"];
				$arr["name"] = $item["name"];
				$arr["start"] = $this->formatDate($item["start"]);
				$arr["end"] = $this->formatDate($item["end"]);
				$arr["year"] = $item["year"];

				$photo = $item["photo"];
				if ($photo) {
					$arr["url"] = self::DATA . $item["year"] . "/" . $item["directory"] . "/small/" . $photo;
				} else {
					$arr["url"] = "http://www.icone-gif.com/icone/png/dock-config/System_Question_Mark.png";
				}

				$this->xml->addElement("album", $arr, $albums);
			}

			$allyears = $this->data->getYears();
			foreach ($allyears as $year) {
				$this->xml->addElement("year", false, $years, $year);
			}
		}

		public function buildYear($year) {
			$this->xml->setXSLT("user/year");
			$this->xml->setTitle($this->config["name"] . " " . $year);

			$albums = $this->data->getAlbums(true, $year);

			foreach ($albums as $item) {
				$arr = [];
				$arr["href"] = "/" . $item["year"] . "/" . $item["shortcut"];
				$arr["name"] = $item["name"];
				$arr["start"] = $this->formatDate($item["start"]);
				$arr["end"] = $this->formatDate($item["end"]);
				$arr["year"] = $item["year"];

				$photo = $item["photo"];
				if ($photo) {
					$arr["url"] = self::DATA . $item["year"] . "/" . $item["directory"] . "/small/" . $photo;
				} else {
					$arr["url"] = "http://www.icone-gif.com/icone/png/dock-config/System_Question_Mark.png";
				}

				$this->xml->addElement("album", $arr);
			}
		}

		public function buildDetail($id) {
			$this->xml->setXSLT("user/detail");

			$data = $this->data->getItem($id);
			$year = $data["year"];
			$this->xml->addElement("year", false, false, $year);

			$title = $data["name"];
			if ($data["id_album"]) {
				$album = $this->data->getItem($data["id_album"]);
				$title = $data["name"] . " – " . $album["name"];
			}
			$this->xml->setTitle($title);

			$this->buildThumbnails($data);
			$this->buildAlbums($data, $year);
			$this->buildChapters($data);
		}

		public function buildNotFound() {
			header("HTTP/1.0 404 Not Found");
			$this->xml->setXSLT("user/404");
		}

		public function buildAuth() {
			$this->xml->setXSLT("user/login");

			$arr = array();
			$arr["url"] = $_SERVER["REQUEST_URI"];
			$this->xml->addElement("login", $arr);
		}

		public function buildAdminConfig() {
			$this->xml->setXSLT("admin/config");
			$this->xml->setTitle("Nastavení");

			unset($this->config["hash-admin"]);
			unset($this->config["hash-user"]);
			$this->xml->addElement("config", $this->config);
		}

		public function buildAdminLogin() {
			$this->xml->setXSLT("admin/login");
			$this->xml->setTitle("Přihlášení");
		}

		public function buildAdminImages() {
			$id = (int) $_GET["id"];
			$row = $this->data->getItem($id);
			$dir = self::DATA . $row["year"] . "/" . $row["directory"] . "/small";
			$files = glob($dir."/*.{jpg,webp}", GLOB_BRACE);
			sort($files);
			$images = $this->xml->addElement("images");

			foreach ($files as $file) {
				$attrs = array();
				$attrs["url"] = $file;
				$attrs["name"] = basename($file);
				$this->xml->addElement("image", $attrs, $images);
			}
		}

		public function buildAdminAlbums() {
			$this->xml->setXSLT("admin/albums");
			$this->xml->setTitle("Seznam alb");
			$year = array_key_exists("year", $_GET) ? $_GET["year"] : date("Y");
			$data = $this->data->getAlbums(false, $year);

			$albums = $this->xml->addElement("albums", array("year"=>$year));
			foreach ($data as $item) {
				$this->xml->addElement("album", $item, $albums);
			}
			$data = $this->data->getYears();

			$years = $this->xml->addElement("years", false);
			foreach ($data as $item) {
				$this->xml->addElement("year", null, $years, $item);
			}

		}

		public function buildAdminAlbum() {
			$this->xml->setXSLT("admin/album");

			$id = (int) $_REQUEST["id"];
			if ($id == 0) {
				$this->xml->setTitle("Nové album");
				$data = array("id"=>0);
				$this->xml->addElement("album", $data);
			} else {
				$row = $this->data->getItem($id);
				if (!$row) { return; }
				$row["gpx"] = ($row["gpx"] ? "1" : "0");
				$this->xml->setTitle("Album '" . $row["name"] . "'");
				$album = $this->xml->addElement("album", $row);

				$list = $this->data->getChapters($id, false);
				$chapters = $this->xml->addElement("chapters", false, $album);
				foreach ($list as $item) {
					$this->xml->addElement("chapter", $item, $chapters);
				}
			}
		}

		public function buildAdminChapter() {
			$this->xml->setXSLT("admin/chapter");
			$id = (int) $_REQUEST["id"];
			if ($id == 0) {
				$id_album = (int) $_REQUEST["id_album"];
				$this->xml->setTitle("Nová kapitola");
				$data = array("id"=>0, "id_album"=>$id_album);
				$this->xml->addElement("chapter", $data);
			} else {
				$row = $this->data->getItem($id);
				if (!$row) { return; }
				$row["gpx"] = ($row["gpx"] ? "1" : "0");
				$this->xml->setTitle("Kapitola '" . $row["name"] . "'");
				$this->xml->addElement("chapter", $row);
			}
		}

		/* === PRIVATE METHODS === */

		private function getLanguage() {
			$allowed = array("cs", "en");
			$language = "";
			if (isset($_GET["language"])) {
				$language = $_GET["language"];
				if (in_array($language, $allowed)) {
					setcookie("language", $language, time() + 60*60*24*365);
				}
			} else if (isset($_COOKIE["language"])) {
				$language = $_COOKIE["language"];
			} else if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
				$parts = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
				$first = $parts[0];
				preg_match("/^[a-z]+/i", $first, $r);
				$language = $r[0];
			}

			if (!in_array($language, $allowed)) { $language = "en"; }
			return $language;
		}

		private function buildThumbnails($data) {
			$dirs = [];
			$dirs[] = $data["directory"]; // this directory is present, unless contained in a chapter

			if (!$data["id_album"]) { // albums also contain all chapter dirs
				$chapters = $this->data->getChapters($data["id"], true);
				foreach ($chapters as $chapter) {
					$dirs[] = $chapter["directory"];
					if ($data["directory"] == $chapter["directory"]) { array_shift($dirs); }
				}
			}

			// convert to a list of images
			$list = [];
			foreach ($dirs as $dir) {
				$path = self::DATA . $data["year"] . "/" . $dir . "/small/*.{jpg,webp}";
				$globbed = glob($path, GLOB_BRACE);
				sort($globbed);
				$list = array_merge($list, $globbed);
			}

			// convert to xml elements
			$thumbnails = $this->xml->addElement("thumbnails");
			foreach ($list as $img) {
				$thumbnail = $this->buildThumbnail($img);
				$this->xml->addElement("thumbnail", $thumbnail, $thumbnails);
			}
		}

		private function buildThumbnail($small) {
			$small_file = pathinfo($small, PATHINFO_FILENAME);
			$small_dir = pathinfo($small, PATHINFO_DIRNAME);
			$small_base = $small_dir."/".basename($small_file);
			$big_base = str_replace("/small/", "/big/", $small_base);
			$big_jpg = $big_base . ".jpg";
			$big_webp = $big_base . ".webp";
			$big_url = $big_base . ".url";
			$type = "image";
			$big = false;

			if (file_exists($big_url)) {
				preg_match("/URL=(.*)/", file_get_contents($big_url), $matches);
				$big = $matches[1];
				$type = "youtube";
			} else {
				if (file_exists($big_jpg)) { $big = $big_jpg; }
				if (file_exists($big_webp)) { $big = $big_webp; }
				if ($big) { // image
					$size = getimagesize($big);
					if ($size[0]/$size[1] == 2) {
						$exiftool = "exiftool -s -s -s -projectiontype";
						$output = [];
						exec($exiftool." ".escapeshellarg($big), $output);
						if (in_array("equirectangular", $output)) { $type = "pano"; }
					}
					$big = "/".$big;
				}
			}

			return [
				"url"=>"/".$small,
				"big"=>$big,
				"type"=>$type
			];
		}

		private function buildAlbums($data, $year) {
			$albums = $this->xml->addElement("albums", array("authorized" => $this->authorized));

			$id_album = ($data["id_album"] ? $data["id_album"] : $data["id"]);
			$album = $this->data->getItem($id_album);
			$list = $this->data->getNearAlbums($album, 2, $year);

			foreach ($list as $a) {
				$attrs = array();
				$attrs["href"] = "/" . $a["year"] . "/" . $a["shortcut"];
				$attrs["name"] = $a["name"];
				$attrs["start"] = $this->formatDate($a["start"]);
				$attrs["end"] = $this->formatDate($a["end"]);
				$attrs["selected"] = ($id_album == $a["id"] ? "1" : "0");
				$this->xml->addElement("album", $attrs, $albums);
			}
		}

		private function buildChapters($data) {
			$id_album = ($data["id_album"] ? $data["id_album"] : $data["id"]);
			$album = $this->data->getItem($id_album);
			$list = $this->data->getChapters($id_album, true);
			if (!count($list)) { return; }

			$chapters = $this->xml->addElement("chapters");

			$attrs = array();
			$attrs["href"] = "/" . $album["year"] . "/" . $album["shortcut"];
			$attrs["name"] = "";
			$attrs["selected"] = ($id_album == $data["id"] ? "1" : "0");
			$this->xml->addElement("chapter", $attrs, $chapters);

			foreach ($list as $chapter) {
				$attrs["href"] = "/" . $album["year"] . "/" . $album["shortcut"] . "/" . $chapter["shortcut"];
				$attrs["name"] = $chapter["name"];
				$attrs["selected"] = ($data["id"] == $chapter["id"] ? "1" : "0");
				$this->xml->addElement("chapter", $attrs, $chapters);
			}
		}

		private function formatDate($str) {
			$parts = explode("-", $str);
			$year = (int) $parts[0];
			$month = (int) $parts[1];
			$day = (int) $parts[2];
			return $day.". ".$month.". ".$year;
		}
	}
?>
