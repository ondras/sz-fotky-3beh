<?php
	session_start();
	include("functions.php");
	chdir("..");

	include("inc/data.php");
	include("inc/builder.php");
	
	$data = new Data("sqlite/gallery.sqlite");
	$builder = new Builder($data, Builder::ADMIN, true);

	$action = "";
	if (isset($_GET["action"])) { $action = $_GET["action"]; }
	if (!array_key_exists("admin-auth", $_SESSION) && $action != "login-finish") { $action = "login"; }
	
	/* pre-action */
	switch ($action) {
		case "chapter-delete":
			deleteChapter($data);
			$id_album = $_GET["id_album"];
			redirect("action=album&id=".$id_album);
		break;
		
		case "album-delete":
			$result = deleteAlbum($data);
			if ($result) {
				$builder->setError($result);
				$action = "";
			} else {
				redirect();
			}
		break;
		
		case "chapter-finish":
			$result = finishChapter($data);
			if ($result) {
				$builder->setError($result);
				$action = "chapter";
			} else {
				$id_album = $_POST["id_album"];
				redirect("action=album&id=".$id_album);
			}
		break;
		
		case "album-finish":
			$result = finishAlbum($data);
			if ($result) {
				$builder->setError($result);
				$action = "album";
			} else {
				$id = $_POST["id"];
				if ($id) {
					$album = $data->getItem($id);
					redirect("year=" . $album["year"]);
				} else {
					redirect();
				}
			}
		break;

		case "config-finish":
			$result = finishConfig($data);
			if ($result) {
				$builder->setError($result);
				$action = "config";
			} else {
				redirect();
			}
		break;
		
		case "logout":
			unset($_SESSION["admin-auth"]);
			redirect();
		break;
		
		case "up":
		case "down":
			$id = (int) $_GET["id"];
			$data->moveChapter($id, ($action == "up" ? -1 : 1));
			redirect("action=album&id=".$_GET["id_album"]);
		break;

		case "login-finish":
			$pass = (array_key_exists("pass", $_POST) ? $_POST["pass"] : "");
			$hash = $data->getConfig()["hash-admin"];
			if (password_verify($pass, $hash)) {
				$_SESSION["admin-auth"] = true;
				redirect();
			} else {
				$action = "login";
				$builder->setError("Bad login");
			}
		break;

		default: /* nothing to do */
		break;
	}
	
	/* action */
	switch ($action) {
		case "config":
			$builder->buildAdminConfig();
		break;
		
		case "logout":
			unset($_SESSION["admin-auth"]);
			redirect();
		break;

		case "login":
			$builder->buildAdminLogin();
		break;
		
		case "album":
			$builder->buildAdminAlbum();
		break;

		case "chapter":
			$builder->buildAdminChapter();
		break;
		
		case "images":
			$builder->buildAdminImages();
		break;

		default: /* list of albums */
			$builder->buildAdminAlbums();
		break;
	}
	
	$builder->output(true);
?>
