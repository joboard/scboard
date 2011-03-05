<?

if (!$_GET['action']) {
	die("no action specified");
}

if (($_GET['action'] == "recognise") && ($_POST['img'])) {
	//echo shell_exec("bash -c ".escapeshellarg("echo -n ".escapeshellarg($_POST['img'])." | base64 -d | convert -compress none png:- pnm:- | gocr -i -"));
	echo shell_exec("bash -c ".escapeshellarg("echo -n ".escapeshellarg($_POST['img'])." | base64 -d | convert -compress none png:- pnm:- | ocrad"));
}

if ($_GET['action'] == "save") {
	if (($_POST['file']) && ($_POST['name']) && ($_POST['class'])) {
		$name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['name']);
		$class = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['class']);
		mkdir("./stored/$class/$name-".time(), 0777, false);
		$pngstrings = explode(" ", $_POST['file']);
		foreach ($pngstrings as $i => $pngfile){
			file_put_contents("./stored/$class/$name-".time()."/$name-".time()."-$i.png", base64_decode($pngfile));
		}
		echo "Datei $name der Klasse $class gespeichert.";
	} else {
		echo "Bitte Dateinamen angeben.";
	}
	
}

if ($_GET['action'] == "list") {
	if ($_POST['class']) {
		$dirEscaped = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['class']);
		if (file_exists("./stored/".$dirEscaped)) {
			$files = array_slice(scandir("./stored/".$dirEscaped), 2);
		} else {
			die("nothing");
		}
	} else {
		$dirs = scandir("./stored/");
		foreach ($dirs as $dir) {
			if (($dir!=".") && ($dir!="..")) {
				foreach (scandir("./stored/".$dir) as $flipchart) {
					if (($flipchart!=".") && ($flipchart!="..")) {
						$files[]=$dir."#".$flipchart;
					}
				}
			}
		}
	}
	if (count($files)==0) {
		echo "nothing";
	} else {
		echo implode(" ", $files);
	}
}

if (($_GET['action']=='getfiles') && ($_POST['class']) && ($_POST['name']) && ($_POST['date']) && ($_POST['width']) && ($_POST['height'])) {
	$class = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['class']);
	$name = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['name']);
	$date = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['date']);
	$width = preg_replace('/[^0-9]/', '', $_POST['width']);
	$height = preg_replace('/[^0-9]/', '', $_POST['height']);
	if (file_exists("./stored/$class/$name-$date")) {
		$images = array_slice(scandir("./stored/$class/$name-$date"), 2);
		foreach ($images as $image) {
			//echo base64_encode(file_get_contents("./stored/$class/$name-$date/$image"))." ";
			echo shell_exec("bash -c \"convert -resize $width".'x'."$height ./stored/$class/$name-$date/$image - | base64 \"")." ";
		}
	} else {
		echo "no ./stored/$class/$name-$date";
	}
}

if ($_GET['action'] == "classes") {
	echo implode(" ",array_slice(scandir("./stored"), 2));
}
?>
