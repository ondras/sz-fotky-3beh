<?php
	function redirect($q = "") {
		$url = "/admin/";
		if ($q) { $url .= "?" . $q; }
		header("Location: ".$url);
	}

	function getError($err) {
		$str = implode(", ", $err);
		return strtoupper($str[0]) . substr($str, 1);
	}

	function checkCommon(&$arr, &$err, $data) {
		if ($_POST["name"] == "") { $err[] = "název musí být vyplněn"; }
		if ($_POST["directory"] == "") { $err[] = "adresář musí být vyplněn"; }
		if ($_POST["shortcut"] == "") { $err[] = "zkratka musí být vyplněna"; }

		$s = $_POST["shortcut"];
		if (array_key_exists("id_album", $_POST)) {
			$id_album = $_POST["id_album"];
			$id = $data->lookupChapter($s, $id_album);
		} else {
			$year = substr($arr["start"], 0, 4);
			$id = $data->lookupAlbum($year, $s);
			$path = "data/".$s;
			if (file_exists($s)) { $err[] = "zkratka nesmí kolidovat s existujícím souborem/adresářem"; }
		}
		if ($id && $id != $_POST["id"]) { $err[] = "zkratka je v tomto roce již použita"; }

		if (array_key_exists("gpx", $_FILES) && $_FILES["gpx"]["error"] != 4) {
			$f = $_FILES["gpx"];
			if ($f["error"] == 0) {
				$arr["gpx"] = file_get_contents($f["tmp_name"]);
			} else {
				$err[] = "chyba při uploadu #".$f["error"];
			}
		}

		if (array_key_exists("gpx-delete", $_POST)) {
			$arr["gpx"] = "";
		}

		$arr["name"] = $_POST["name"];
		$arr["directory"] = $_POST["directory"];
		$arr["shortcut"] = $_POST["shortcut"];
		$arr["visible"] = (array_key_exists("visible", $_POST) ? "1" : "0");
		$arr["public"] = (array_key_exists("public", $_POST) ? "1" : "0");
	}

	function finishConfig($data) {
		$err = array();
		$arr = array();

		if (count($err)) { return getError($err); }

		if ($_POST["pass-admin"] != "") { $arr["hash-admin"] = password_hash($_POST["pass-admin"], PASSWORD_DEFAULT); }
		if ($_POST["pass-user"] != "") { $arr["hash-user"] = password_hash($_POST["pass-user"], PASSWORD_DEFAULT); }

		$list = $data->getConfig();
		foreach ($list as $name=>$value) {
			if (array_key_exists($name, $_POST)) { $arr[$name] = $_POST[$name]; }
		}

		$data->updateConfig($arr);
	}

	function finishChapter($data) {
		$arr = array();
		$err = array();

		checkCommon($arr, $err, $data);
		if (count($err)) { return getError($err); }

		/* insert/update */
		$id = (int) $_POST["id"];
		if ($id == 0) {
			$id_album = (int) $_POST["id_album"];
			$arr["id_album"] = $id_album;
			$o = $data->getMaxOrdering($id_album);
			$arr["ordering"] = $o+1;

			$id = $data->insertItem($arr);
		} else {
			$data->updateItem($id, $arr);
		}
	}

	function finishAlbum($data) {
		$arr = array();
		$err = array();

		if ($_POST["id"] != 0 && $_POST["photo"] == "") { $err[] = "hlavní fotka musí být vyplněna"; }
		$arr["photo"] = $_POST["photo"];
		$arr["start"] = $_POST["start"];
		$arr["end"] = $_POST["end"];

		checkCommon($arr, $err, $data);

		if (count($err)) { return getError($err); }
		/* insert/update */
		$id = (int) $_POST["id"];
		if ($id == 0) {
			$data->insertItem($arr);
		} else {
			$data->updateItem($id, $arr);
		}
	}

	function deleteChapter($data) {
		$id = (int) $_GET["id"];
		$data->deleteItem($id);
	}

	function deleteAlbum($data) {
		$id = (int) $_GET["id"];
		$chapters = $data->getChapters($id, false);
		if (count($chapters)) { return "Nelze smazat album, které obsahuje kapitoly"; }

		$data->deleteItem($id);
	}
?>
