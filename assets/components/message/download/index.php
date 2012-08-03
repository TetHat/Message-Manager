<?php
/**
 * This file will simply force a download of a given media file
 */

// Set memory limit:
ini_set( 'memory_limit', "256M" );
// set time limit to 10 hours or 36000 seconds
ini_set('max_execution_time', 36000);

// https://bcwebtest01/assets/components/message/download/?media=1

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';

include_once MODX_CORE_PATH . 'model/modx/modx.class.php'; 
$modx= new modX();
/**
 * set debugging/logging options 
$modx->setDebug(E_ALL | E_STRICT);
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
 */

$modx->initialize('web');

// add package
$model_path = MODX_CORE_PATH . 'components/message/model/';
$modx->addPackage('message', $model_path);
$media_dir = MODX_ASSETS_PATH.'components/message/uploads/';

if ( isset($_REQUEST['media']) && is_numeric($_REQUEST['media']) ) {
    $media_id = $_REQUEST['media'];
} else {
    // load 404 page:
    echo 'File Not Found';
    exit();
}
// now get the file:
$query = $modx->newQuery('MessageMedia');
$query->where(array(
    'id' => $media_id, 
    'active' => 'Yes' )
    );
//$oldTarget = $modx->setLogTarget('HTML');
// your code here
//$c->limit(5);
$media = $modx->getObject('MessageMedia', $query);

if ( is_object($media) && $media->get('id') == $media_id ) {
    
} else {
    // load 404 page:
    echo 'File Not Found';
    exit();
}
$display_name = $media->get('title');
$filename = $media_dir . $media->get('file');

# get the type of file
$file_ext = substr($filename, strripos($filename, '.')+1 );

$file_type_array = array(
        # documents
        'doc' =>'application/msword',
        'docx' =>'application/msword',
        //'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'rtf' => 'application/rtf',
        'txt' => 'text/plain',
        'pdf' => 'application/pdf',
        # powerpoint
        'pot' => 'application/mspowerpoint',
        'pps' => 'application/mspowerpoint',
        'ppt' => 'application/mspowerpoint',
        'ppz' => 'application/mspowerpoint',
        # excel
        'csv' => 'application/x-msdownload',
        'xlc' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        # web images
        'gif' => 'image/gif',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png', 
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        # web files
        'css' => 'text/css',
        'htm' => 'text/html',
        'html' => 'text/html',
        'xml' => 'text/xml',
        'js' => 'application/x-javascript',
        # audio
        'au' => 'audio/basic',
        'snd' => 'audio/basic',
        'mid' => 'audio/mid',
        'rmi' => 'audio/mid',
        'mp3' => 'audio/mpeg',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'm3u' => 'audio/x-mpegurl',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'wav' => 'audio/x-wav',
        # video
        'avi' => 'video/x-msvideo',
        'dl' => 'video/dl',
        'fli' => 'video/fli',
        'fli' => 'video/x-fli',
        'flv' => 'video/flv',
        'gl' => 'video/gl',
        'mp2' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mov' => 'video/quicktime',
        'qt' => 'video/quicktime',
        'viv' => 'video/vnd.vivo', 
        'vivo' => 'video/vnd.vivo', 
        'wmv' => 'video/x-ms-wmv',
        'wmx' => 'video/x-ms-wmx',
        'wvx' => 'video/x-ms-wvx',
        'asf' => 'video/x-ms-asf',
        'asx' => 'video/x-ms-asx',
        'movie' => 'video/x-sgi-movie'
    );
$content_type = $file_type_array[$file_ext];
    
################
#   Download a file - http://w-shadow.com/blog/2007/08/12/how-to-force-file-download-with-php/comment-page-1/
################
// NOTE there can not be a space between the start of the file and the <?php this can cause a file corruption notice!
if( is_file($filename) ){
    # send file
    $filesize = filesize($filename);
    if($filesize) {
        if(ini_get('zlib.output_compression')){
            ini_set('zlib.output_compression', 'Off');
        }
        //if( $user->id() == 1 || $user->id() == 678 ){
        if( 1 == 2 ){
            //exit();
            echo '<br />'.$filename.'<br />'.$content_type.'<br />filename="'.str_replace(' ','_',$display_filename).'.'.$file_ext.'"
            <br />' .$filesize.'<br /><br />';
        }
        else { 
            //http://www.jonasjohn.de/snippets/php/headers.htm
            // required for IE, otherwise Content-Disposition may be ignored
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename="'.str_replace(' ','_',$display_name).'.'.$file_ext.'"');
            header("Content-Transfer-Encoding: binary");
            header('Accept-Ranges: bytes');
            // The three lines below basically make the download non-cacheable
            header("Cache-control: private");
            header('Pragma: private');
            $time = gmdate('D, d M Y H:i:s', filectime($filename));
            header('Last-Modified: '.$time.' EST');
            //header("Expires: ".$time." EST");
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // date in the past
            header('Content-Length: '.$filesize);//added
        }
        /* Read It */
        $contents = fread(fopen($filename, "rb"), filesize($filename));
        /* Print It */
        echo $contents;
        exit();
    }
} else {
    echo 'File Not Found: '.__LINE__.' - '.$filename;
    exit();
}
