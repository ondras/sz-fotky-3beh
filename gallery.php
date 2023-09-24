<?php
	session_start();

	include("inc/data.php");
	include("inc/dispatcher.php");
	include("inc/builder.php");

	$data = new Data("sqlite/gallery.sqlite");

	$dispatcher = new Dispatcher($data);
	$builder = new Builder($data, Builder::USER, $dispatcher->isAuthorized());

	$result = $dispatcher->getResult();
	switch ($result) {
		case Dispatcher::INDEX:
			$builder->buildIndex();
		break;

		case Dispatcher::NOTFOUND:
			$builder->buildNotFound();
		break;

		case Dispatcher::AUTH:
			$builder->buildAuth();
		break;

		case Dispatcher::YEAR:
			$id = $dispatcher->getId();
			$builder->buildYear($id);
		break;

		case Dispatcher::ALBUM:
		case Dispatcher::CHAPTER:
			$id = $dispatcher->getId();
			$builder->buildDetail($id);
		break;

		case Dispatcher::PASS:
			$path = substr($_SERVER["REQUEST_URI"], 1);
			header("Content-type: " . mime_content_type($path));
			@readfile($path);
			die();
		break;
	}

	$builder->output(true);
?>
