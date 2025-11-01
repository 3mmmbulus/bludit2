<?php defined('BLUDIT') or die('Bludit CMS.');
header('Content-Type: application/json');

/*
| Upload an image to a particular page
|
| @_POST['uuid']	string	Page uuid
|
| @return		array
*/

// $_POST
// ----------------------------------------------------------------------------
$uuid = empty($_POST['uuid']) ? false : $_POST['uuid'];
// ----------------------------------------------------------------------------

// Check path traversal on $uuid
if ($uuid) {
	if (Text::stringContains($uuid, DS, false)) {
		$message = 'Path traversal detected.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
}

// Set upload directory
if ($uuid && IMAGE_RESTRICT) {
	$imageDirectory = PATH_UPLOADS_PAGES . $uuid . DS;
	$thumbnailDirectory = $imageDirectory . 'thumbnails' . DS;
	if (!Filesystem::directoryExists($thumbnailDirectory)) {
		Filesystem::mkdir($thumbnailDirectory, true);
	}
} else {
	$imageDirectory = PATH_UPLOADS;
	$thumbnailDirectory = PATH_UPLOADS_THUMBNAILS;
}

$images = array();
foreach ($_FILES['images']['name'] as $uuid => $filename) {
	// Check for errors
	if ($_FILES['images']['error'][$uuid] != 0) {
		$message = $L->g('Maximum load file size allowed:') . ' ' . ini_get('upload_max_filesize');
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}

	// Convert URL characters such as spaces or quotes to characters
	$filename = urldecode($filename);

	// Check path traversal on $filename
	if (Text::stringContains($filename, DS, false)) {
		$message = 'Path traversal detected.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}

	// Check file extension
	$fileExtension = Filesystem::extension($filename);
	$fileExtension = Text::lowercase($fileExtension);
	if (!in_array($fileExtension, $GLOBALS['ALLOWED_IMG_EXTENSION'])) {
		$message = $L->g('File type is not supported. Allowed types:') . ' ' . implode(', ', $GLOBALS['ALLOWED_IMG_EXTENSION']);
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}

	// Check file MIME Type
	$fileMimeType = Filesystem::mimeType($_FILES['images']['tmp_name'][$uuid]);
	if ($fileMimeType !== false) {
		if (!in_array($fileMimeType, $GLOBALS['ALLOWED_IMG_MIMETYPES'])) {
			$message = $L->g('File mime type is not supported. Allowed types:') . ' ' . implode(', ', $GLOBALS['ALLOWED_IMG_MIMETYPES']);
			Log::set($message, LOG_TYPE_ERROR);
			ajaxResponse(1, $message);
		}
	}

	// Enhanced: Validate file content by magic bytes (file signature)
	$fileContent = file_get_contents($_FILES['images']['tmp_name'][$uuid], false, null, 0, 12);
	$isValidImage = false;
	
	// Check for common image file signatures (magic bytes)
	$imageSignatures = array(
		'jpeg' => array("\xFF\xD8\xFF"),                                  // JPEG
		'png'  => array("\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"),            // PNG
		'gif'  => array("GIF87a", "GIF89a"),                             // GIF
		'webp' => array("RIFF", "\x57\x45\x42\x50"),                     // WebP (needs RIFF + WEBP)
		'bmp'  => array("BM"),                                           // BMP
		'svg'  => array("<?xml", "<svg")                                 // SVG
	);
	
	foreach ($imageSignatures as $type => $signatures) {
		foreach ($signatures as $signature) {
			if (strpos($fileContent, $signature) === 0 || 
			    ($type === 'webp' && strpos($fileContent, 'RIFF') === 0 && strpos($fileContent, 'WEBP') !== false) ||
			    ($type === 'svg' && (strpos($fileContent, '<?xml') !== false || strpos($fileContent, '<svg') !== false))) {
				$isValidImage = true;
				break 2;
			}
		}
	}
	
	if (!$isValidImage) {
		$message = 'File content validation failed. The file does not appear to be a valid image.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
	
	// Additional security: Check file size
	$maxFileSize = 10 * 1024 * 1024; // 10MB limit
	if ($_FILES['images']['size'][$uuid] > $maxFileSize) {
		$message = 'File size exceeds maximum allowed size of 10MB.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}

	// Move from PHP tmp file to Bludit tmp directory
	Filesystem::mv($_FILES['images']['tmp_name'][$uuid], PATH_TMP . $filename);

	// Transform the image and generate the thumbnail
	$image = transformImage(PATH_TMP . $filename, $imageDirectory, $thumbnailDirectory);

	if ($image) {
		chmod($image, 0644);
		$filename = Filesystem::filename($image);
		array_push($images, $filename);
	} else {
		$message = 'Error after transformImage() function.';
		Log::set($message, LOG_TYPE_ERROR);
		ajaxResponse(1, $message);
	}
}

ajaxResponse(0, 'Images uploaded.', array(
	'images' => $images
));
