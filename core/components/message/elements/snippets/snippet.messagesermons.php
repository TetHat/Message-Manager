<?php
 /**
 * Message
 * 
 * A message snippet for MODX Revolution, this will show a list of sermons for a group
 * 
 * @package messagemanager
 */
//require_once $modx->getOption('formit.core_path',null,$modx->getOption('core_path').'components/formit/').'model/formit/formit.class.php';
// get the user input (inputName, the input array, default value)
$group_id = $modx->getOption('group', $scriptProperties, 1);
$format = $modx->getOption('format', $scriptProperties, 'n/j/Y');

$skin = $modx->getOption('skin', $scriptProperties, 'default_');
$head = $modx->getOption('headTpl', $scriptProperties, $skin.'MessageHeadTpl' );
$headRow = $modx->getOption('headRowTpl', $scriptProperties, $skin.'MessageHeadRowTpl' );

$message_holder = $modx->getOption('sermonsHolderTpl', $scriptProperties, $skin.'MessageHolderTpl' );
$sermon_row = $modx->getOption('sermonRowTpl', $scriptProperties, $skin.'sermonRowTpl' );
//$slide_pane_link = $modx->getOption('slideLinkTpl', $scriptProperties, $skin.'slideLinkTpl' );
//$html_caption = $modx->getOption('htmlCaptionTpl', $scriptProperties, $skin.'htmlCaptionTpl' );
//$head = $modx->getOption('headTpl', $scriptProperties, $skin.'' );

// add package
$s_path = $modx->getOption('core_path').'components/message/model/';
$modx->addPackage('message', $s_path);
$media_dir = MODX_ASSETS_URL.'components/message/uploads/';


/* 1. Get all of the Group data*/
$sermonGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
$group_data = array();
if ( is_object($sermonGroup) ) {
    $group_data = $sermonGroup->toArray();
} else {
    return '<p>Message Group Not Found</p>';
}

/* 2. Get all of the Sermons */
// get the sermons for the group
$query = $modx->newQuery('MessageSermons');
$query->where(array(
    'group_id' => $group_id, 
    'active' => 'Yes' )
    );
$query->sortby('sermon_date','DESC');

//$oldTarget = $modx->setLogTarget('HTML');
// your code here
//$c->limit(5);
$sermons = $modx->getCollection('MessageSermons',$query);
// $sql = $query->toSQL();

// restore the default logging (to file)
//$modx->setLogTarget($oldTarget);

$sermon_output = '';
$head_output = '';
$count = 0;
foreach( $sermons as $sermon ){
    ++$count;
    // go thourgh each image
    // 
    $sermon_data = $sermon->toArray();
    // set a date format:
    $sermon_data['date'] = date($format,strtotime($sermon_data['sermon_date']));
    
    // make audio and video urls
    // now get audio media file:
    $query = $modx->newQuery('MessageMedia');
    $query->where(array(
        'sermon_id' => $sermon->get('id'), 
        'active' => 'Yes',
        'type' => 'audio' )
        );
    $query->sortby('create_date','DESC');
    $audioFile = $modx->getObject('MessageMedia', $query );
    if ( is_object($audioFile) ) {
        $audio_data = $audioFile->toArray();
        
        $sermon_data['audio_ext'] = $audio_data['file_ext'];
        $sermon_data['audio_name'] = $audio_data['name'];
        $sermon_data['audio_description'] = $audio_data['description'];
        $sermon_data['audio_download'] = $audio_data['allow_download'];
        $sermon_data['audio_url'] = $media_dir.$audio_data['file'];
        
    }
    // now get audio media file:
    $query = $modx->newQuery('MessageMedia');
    $query->where(array(
        'sermon_id' => $sermon->get('id'), 
        'active' => 'Yes',
        'type' => 'video' )
        );
    $query->sortby('create_date','DESC');
    $videoFile = $modx->getObject('MessageMedia', $query );
    if ( is_object($videoFile) ) {
        $video_data = $videoFile->toArray();
        
        $sermon_data['video_ext'] = $video_data['file_ext'];
        $sermon_data['video_name'] = $video_data['name'];
        $sermon_data['video_description'] = $video_data['description'];
        $sermon_data['video_download'] = $video_data['allow_download'];
        $sermon_data['video_url'] = $media_dir.$video_data['file'];
        
    }
    $sermon_data = array_merge($group_data, $sermon_data);
    
    $sermon_output .= $modx->getChunk($sermon_row, $sermon_data);
    
    $head_output .= $modx->getChunk($headRow, $sermon_data);
    
}

/* 3. now load the <head> data */
$group_data['headRows'] = $head_output;
$group_data['sermonRows'] = $sermon_output;
$modx->regClientStartupHTMLBlock($modx->getChunk($head, $group_data));
    
/* 4. now load the <body> data */
$o = $modx->getChunk($message_holder, $group_data);
return $o;
?>