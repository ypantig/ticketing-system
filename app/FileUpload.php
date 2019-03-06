<?php

namespace YP;

class FileUpload {

  static public function uploadFile( $files, $fileField, $postID, $isAjax = false ) {

    $error = $_FILES;

    if ( $_FILES[ $fileField ][ 'name' ] ) {

      /**
       * HANDLE FILE UPLOAD
       */
      //provides access to WP environment
      require_once( $_SERVER[ 'DOCUMENT_ROOT' ] . '/wp-load.php' );

      if ( !$_FILES[ $fileField ]['error'] ) {

        //validate the file
        $new_file_name = strtolower($_FILES[ $fileField ]['tmp_name']);

        //can't be larger than 300 KB
        if($_FILES[ $fileField ]['size'] > (300000)) {
          //wp_die generates a visually appealing message element
          wp_die('Your file size is to large.');
        }
        else {

          if ( $isAjax ) {
            //the file has passed the test
            //These files need to be included as dependencies when on the front end.
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
          }

          // Let WordPress handle the upload.
          // Remember, 'upload' is the name of our file input in our form above.
          $fileID = media_handle_upload( $fileField, $postID );
          $error = $fileID;
          $error .= ' ' . $isAjax;
          // \App\Utils::pre( $fileID );
          if ( is_wp_error( $fileID ) ) {
            $error = 'There was something wrong in uploading the file.';
          } else {
            update_post_meta( $postID, 'yp_ticket_attachment', $fileID );
            $error .= ' there is no error';
          }
        }
      }
      else {
        //set that to be the returned message
        $error = 'There was something wrong in uploading the file: ' . $_FILES[ $fileField ][ 'error'];
      }
    }

    return $error;

  }

}
