<?php
function ensure_image_is_available($image_dir_uri, $fname, $img_url) {
  $local_uri = $image_dir_uri . $fname;

  // check if image already exists
  $info = image_get_info($local_uri);
  if ($info === FALSE) {
    // prepare the directory
    if (!file_prepare_directory($image_dir_uri, FILE_CREATE_DIRECTORY)) {
      return array(
        'error' => 'Could not create the directory "' . $image_dir_uri . '".'
      );
    }

    // download the file
    $finfo = system_retrieve_file($img_url, $local_uri, TRUE, FILE_EXISTS_REPLACE);
    if ($finfo === FALSE) {
      return array(
        'error' => 'Could not retrieve the requested file "' . $img_url . '".'
      );
    }

    $img_info = getimagesize($local_uri);

    $entity_type = 'image';
    $entity = entity_create($entity_type, array(
      ''
    ));

    // set meta data
    $ewrapper = entity_metadata_wrapper($entity_type, $entity);
    $ewrapper->field_image_fid->set($finfo->fid);
    $ewrapper->field_image_width->set($img_info[0]);
    $ewrapper->field_image_height->set($img_info[1]);
    $ewrapper->save();

    $info = array(
      'width' => $img_info[0],
      'height' => $img_info[1],
      'extension' => pathinfo($finfo['filename'], PATHINFO_EXTENSION),
      'mime_type' => $finfo['filemime'],
      'file_size' => $finfo['filesize']
    );
  }

  return $info;
}
