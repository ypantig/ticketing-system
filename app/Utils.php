<?php

namespace YP;

class Utils {

  static public function getMimeType( $filename ) {
    $idx = explode( '.', $filename );
    $count_explode = count( $idx );
    $idx = strtolower( $idx[ $count_explode - 1 ] );

    $mimet = array(
      'txt' => 'text/plain',
      'htm' => 'text/html',
      'html' => 'text/html',
      'php' => 'text/html',
      'css' => 'text/css',
      'js' => 'application/javascript',
      'json' => 'application/json',
      'xml' => 'application/xml',
      'swf' => 'application/x-shockwave-flash',
      'flv' => 'video/x-flv',

      // images
      'png' => 'image/png',
      'jpe' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg' => 'image/jpeg',
      'gif' => 'image/gif',
      'bmp' => 'image/bmp',
      'ico' => 'image/vnd.microsoft.icon',
      'tiff' => 'image/tiff',
      'tif' => 'image/tiff',
      'svg' => 'image/svg+xml',
      'svgz' => 'image/svg+xml',

      // archives
      'zip' => 'application/zip',
      'rar' => 'application/x-rar-compressed',
      'exe' => 'application/x-msdownload',
      'msi' => 'application/x-msdownload',
      'cab' => 'application/vnd.ms-cab-compressed',

      // audio/video
      'mp3' => 'audio/mpeg',
      'qt' => 'video/quicktime',
      'mov' => 'video/quicktime',

      // adobe
      'pdf' => 'application/pdf',
      'psd' => 'image/vnd.adobe.photoshop',
      'ai' => 'application/postscript',
      'eps' => 'application/postscript',
      'ps' => 'application/postscript',

      // ms office
      'doc' => 'application/msword',
      'rtf' => 'application/rtf',
      'xls' => 'application/vnd.ms-excel',
      'ppt' => 'application/vnd.ms-powerpoint',
      'docx' => 'application/msword',
      'xlsx' => 'application/vnd.ms-excel',
      'pptx' => 'application/vnd.ms-powerpoint',


      // open office
      'odt' => 'application/vnd.oasis.opendocument.text',
      'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    if ( isset( $mimet[$idx] ) ) {
      return $mimet[$idx];
    } else {
      return 'application/octet-stream';
    }
  }

  public static function getFileImage( $filename ) {
    $idx = explode( '.', $filename );
    $count_explode = count( $idx );
    $idx = strtolower( $idx[ $count_explode - 1 ] );

    $type = [
      'txt' => 'document',
      'htm' => 'document',
      'html' => 'document',
      'php' => 'document',
      'css' => 'document',
      'js' => 'document',
      'json' => 'document',
      'xml' => 'document',
      'swf' => 'document',
      'flv' => 'document',

      // images
      'png' => 'image',
      'jpe' => 'image',
      'jpeg' => 'image',
      'jpg' => 'image',
      'gif' => 'image',
      'bmp' => 'image',
      'ico' => 'image',
      'tiff' => 'image',
      'tif' => 'image',
      'svg' => 'image',
      'svgz' => 'image',

      // archives
      'zip' => 'archive',
      'rar' => 'archive',
      'exe' => 'archive',
      'msi' => 'archive',
      'cab' => 'archive',

      // audio/video
      'mp3' => 'audio',
      'qt' => 'video',
      'mov' => 'video',

      // adobe
      'pdf' => 'document',
      'psd' => 'document',
      'ai' => 'document',
      'eps' => 'document',
      'ps' => 'document',

      // ms office
      'doc' => 'document',
      'rtf' => 'document',
      'xls' => 'document',
      'ppt' => 'document',
      'docx' => 'document',
      'xlsx' => 'document',
      'pptx' => 'document',


      // open office
      'odt' => 'document',
      'ods' => 'document',
    ];

    return $type[ $idx ];

  }

  /**
   * get the abspath url of the file
   *
   * @author Ynah Pantig
   * @package
   * @since 1.0
   * @param $file
   * @return
   */

  static public function getAbspathUrl( $file )
  {

    $server = $_SERVER[ 'HTTP_HOST' ];
    $dataPath = str_replace( $server, '', $file );
    $dataPath = str_replace( 'http://', '', $dataPath );
    $dataPath = str_replace( 'https://', '', $dataPath );

    $file = ABSPATH . $dataPath;

    return $file;

  }/* getAbspathUrl() */


  /**
   * get dist url
   *
   * @author Ynah Pantig
   * @param
   * @return
   */

  public static function distUrl(  )
  {

    return YP_TICKETS_URL . 'dist';

  }


  /**
   * read the assets file
   *
   * @author Ynah Pantig
   * @param
   * @return
   */

  public static function readAssets()
  {

    if ( file_exists( YP_TICKETS_PATH . 'dist/assets.json' ) )
    {
      $assets = file_get_contents( YP_TICKETS_PATH . 'dist/assets.json' );
      return json_decode( $assets );
    }

  }

  /**
   * get assets path
   *
   * @author Ynah Pantig
   * @param $type
   * @return
   */

  public static function getAssetPath( $file )
  {

    $assets = static::readAssets();

    if ( $assets != '' )
    {
      foreach ( $assets as $key => $asset ) {
        if ( strpos( $key, $file ) > -1 ) {
          return trailingslashit( static::distUrl() ) . $asset;
        }
      }
    } else {
      return trailingslashit( static::distUrl() ) . $file;
    }

  }
}
