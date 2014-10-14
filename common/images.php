<?php

/**
 * Ensures that the image at $image_dir_uri . $fname is available. If it is not
 * available, it is made available by downloading the image from $img_url.
 *
 * @param string $image_dir_uri uri of the image directory
 * @param string $fname name of the image file
 * @param string $img_url url of the image
 * @return array finfo including fid
 */
function ensure_image_is_available($image_dir_uri, $fname, $img_url,
    $add_to_db = TRUE) {
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
    $finfo = system_retrieve_file($img_url, $local_uri, FALSE,
        FILE_EXISTS_REPLACE);
    if ($finfo === FALSE) {
      return array(
        'error' => 'Could not retrieve the requested file "' . $img_url . '".'
      );
    }

    $img_info = getimagesize($local_uri);

    if ($add_to_db) {
      $entity_type = 'image';
      $entity = entity_create($entity_type);

      // set meta data
      $ewrapper = entity_metadata_wrapper($entity_type, $entity);
      $ewrapper->field_image_fid->set($finfo->fid);
      $ewrapper->field_image_width->set($img_info[0]);
      $ewrapper->field_image_height->set($img_info[1]);
      $ewrapper->save();
    }

    $info = array(
      'fid' => $finfo->fid,
      'width' => $img_info[0],
      'height' => $img_info[1],
      'extension' => pathinfo($finfo->filename, PATHINFO_EXTENSION),
      'mime_type' => $finfo->filemime,
      'file_size' => $finfo->filesize
    );
  } else {
    $finfo = reset(
        file_load_multiple(array(), array(
          'uri' => $local_uri
        )));
    $info['fid'] = $finfo->fid;
  }

  return $info;
}
