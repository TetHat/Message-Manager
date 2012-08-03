<?php
/**
 * @package doodle
 * @subpackage processors
 */

/* get obj */
if (empty($scriptProperties['id'])) {
    return $modx->error->failure($modx->lexicon('message.feed_err_ns'));
}

$sermon = $modx->getObject('MessageSermons',$scriptProperties['id']);
if (empty($sermon)) {
    return $modx->error->failure($modx->lexicon('message.sermon_err_notfound'));
}
// requried
if (empty($scriptProperties['title']) ) {
    $modx->error->addField('title',$modx->lexicon('message.sermon_err_required'));
}
if ( empty($scriptProperties['description']) ) {
    $modx->error->addField('description',$modx->lexicon('message.sermon_err_required'));
}

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

//require_once MODX_CORE_PATH.'components/message/model/fileuploader.class.php';
/*$option_array = array(//fields array 
        'sermon_id' => 'numeric',  
        'group_id' => 'numeric',
        'sermon_date' => 'date',
        'create_date' => 'set_current_date_time',
        'active' => 'text',
        //'url' => 'text & links',
        'title' => 'text',
        'speaker' => 'text',
        'description' => 'text & links',
        'tags' => 'text', 
        
        'upload_time' => 'set_current_date_time',
        'upload_audio' => 'file',
        'upload_video' => 'file'
    );
/* set fields */
$sermon->fromArray($scriptProperties);

require_once MODX_CORE_PATH.'components/message/model/message/mycontroller.class.php';
$MessageSermon = new myController($modx, array('packageName' => 'message'));

require_once $MessageSermon->config['modelPath'].'fileuploader.class.php';

$uploader = new fileUploader($modx);

// set time defaults:
//$time = date('Y-m-d g:h:s');
//$sermon->set('edit_time', $time);
$group_id = $sermon->get('group_id');//$scriptProperties['album_id'];
//echo print_r($_FILES['upload_audio']['tmp_name']);
// upload image:
if( isset($_FILES['upload_audio']['tmp_name']) && strlen($_FILES['upload_audio']['tmp_name']) > 4 ) {
    // validate the file:
    // load the album data NEED THIS!
    $messageGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
    $group_data = array();
    $file_allowed = array();
    if ( is_object($messageGroup) ) {
        $group_data = $messageGroup->toArray();
        // replace the | with a comma
        $audio_allowed = array('mp3', 'wav');
    }
    
    // set the file upload rules - name, file size, allowed extentions
    $uploader->setFileRules('upload_audio', 
            //$group_data['file_size_limit'], 
            $audio_allowed, 
            //$group_data['file_width'], 
            //$group_data['file_height'], 
            $MessageSermon->config['uploadPath'].'tmp/'
        );
    //echo 'checkFile true on line '.__LINE__;
    if ( $uploader->checkFile('upload_audio') ) {
        // passed so continue to upload
        // just the file name
        //echo 'checkFile true on line '.__LINE__;
        $c = $modx->newQuery('MessageMedia');
		$c->where(array(
			'sermon_id' => $sermon->get('id'),
			'type' => 'audio'
		));
		// $audioFile = $modx->getObject('MessageMedia', $c);// returns only 1 record
		$totalRecords = $modx->getCount('MessageMedia',$c);
		$audioFiles =  $modx->getIterator('MessageMedia', $c);// returns all records
        if ( $totalRecords > 0 ) {
        	$count = 1;
        	foreach ($audioFiles as $audioFile) {
				$audio_name = $audioFile->get('file');
				//echo 'ID: '.$audioFile->get('id').' File: '.$audio_name;
				unlink($MessageSermon->config['uploadPath'].$audio_name);// full path...
				if ( $count < $totalRecords ){
					$audioFile->remove();
				}
				++$count;	
			}
        	
        } else {
        	$audioFile = $modx->newObject('MessageMedia');
        }
        $audio_name = 'audio_'.$group_data['id'].'_sermon_'.time();
		
        $file_path = str_replace( 
                $MessageSermon->config['uploadPath'], '', 
                $uploader->moveFile(
                    'upload_audio', 
                    $MessageSermon->config['uploadPath'], 
                    $audio_name
                ) 
            );
			$audio_data = array(
                'sermon_id' => $sermon->get('id'),
                'create_date' => date('Y-m-d H:i:s'),
                'type' => 'audio',
                'file_ext' => $uploader->fileExt('upload_audio'),
                'name' => $sermon->get('title'),
                'description' => 'Audio file',
                'active' => 'Yes',
                'allow_download' => 'Yes',
                'file' => $file_path
            );
            //$audioFile = $modx->newObject('MessageMedia');
            $audioFile->fromArray($audio_data);
            $audioFile->save();
    } else {
        // failed give reason why:
         $modx->error->addField('upload_audio',$modx->lexicon('message.sermon_err_required'));
         return $modx->error->failure();
    }
}
if( isset($_FILES['upload_video']['tmp_name']) && strlen($_FILES['upload_video']['tmp_name']) > 4 ) {
    // validate the file:
    // load the album data NEED THIS!
    $messageGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
    $group_data = array();
    $file_allowed = array();
    if ( is_object($messageGroup) ) {
        $group_data = $messageGroup->toArray();
        // replace the | with a comma
        $video_allowed = array('flv', 'mp4', 'mpeg', 'mov', 'wmv');
    }
    
    // set the file upload rules - name, file size, allowed extentions
    $uploader->setFileRules('upload_video', 
            //$group_data['file_size_limit'], 
            $video_allowed, 
            //$group_data['file_width'], 
            //$group_data['file_height'], 
            $MessageSermon->config['uploadPath'].'tmp/'
        );
    
    if ( $uploader->checkFile('upload_video') ) {
        // passed so continue to upload
        // just the file name
        $c = $modx->newQuery('MessageMedia');
		$c->where(array(
			'sermon_id' => $sermon->get('id'),
			'type' => 'video'
		));
		// $audioFile = $modx->getObject('MessageMedia', $c);// returns only 1 record
		$totalRecords = $modx->getCount('MessageMedia', $c);
		$videoFiles =  $modx->getIterator('MessageMedia', $c);// returns all records
        if ( $totalRecords > 0 ) {
        	$count = 1;
        	foreach ($videoFiles as $videoFile) {
				$video_name = $videoFile->get('file');
				unlink($MessageSermon->config['uploadPath'].$video_name);// full path...
				if ( $count < $totalRecords ){
					$videoFile->remove();
				}
				++$count;	
			}
        	
        } else {
        	$videoFile = $modx->newObject('MessageMedia');
        }
        $video_name = 'video_'.$group_data['id'].'_sermon_'.time();
		
        $file_path = str_replace( 
                $MessageSermon->config['uploadPath'], '', 
                $uploader->moveFile(
                    'upload_video', 
                    $MessageSermon->config['uploadPath'], 
                    $video_name
                ) 
            );
			$video_data = array(
                'sermon_id' => $sermon->get('id'),
                'create_date' => date('Y-m-d H:i:s'),
                'type' => 'video',
                'file_ext' => $uploader->fileExt('upload_video'),
                'name' => $sermon->get('title'),
                'description' => 'Video file',
                'active' => 'Yes',
                'allow_download' => 'Yes',
                'file' => $file_path
            );
            $videoFile->fromArray($video_data);
            $videoFile->save();
    } else {
        // failed give reason why:
         $modx->error->addField('upload_video',$modx->lexicon('message.sermon_err_required'));
         return $modx->error->failure();
    }
}
if( isset($_FILES['upload_pdf']['tmp_name']) && strlen($_FILES['upload_pdf']['tmp_name']) > 4 ) {
    // validate the file:
    // load the album data NEED THIS!
    $messageGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
    $group_data = array();
    $file_allowed = array();
    if ( is_object($messageGroup) ) {
        $group_data = $messageGroup->toArray();
        // replace the | with a comma
        $video_allowed = array('pdf');
    }
    
    // set the file upload rules - name, file size, allowed extentions
    $uploader->setFileRules('upload_pdf', 
            //$group_data['file_size_limit'], 
            $video_allowed, 
            //$group_data['file_width'], 
            //$group_data['file_height'], 
            $MessageSermon->config['uploadPath'].'tmp/'
        );
    
    if ( $uploader->checkFile('upload_pdf') ) {
        // passed so continue to upload
        // just the file name
        $c = $modx->newQuery('MessageMedia');
		$c->where(array(
			'sermon_id' => $sermon->get('id'),
			'type' => 'document'
		));
		// $audioFile = $modx->getObject('MessageMedia', $c);// returns only 1 record
		$totalRecords = $modx->getCount('MessageMedia', $c);
		$pdfFiles =  $modx->getIterator('MessageMedia', $c);// returns all records
        if ( $totalRecords > 0 ) {
        	$count = 1;
        	foreach ($pdfFiles as $pdfFile) {
				$pdf_name = $pdfFile->get('file');
				unlink($MessageSermon->config['uploadPath'].$pdf_name);// full path...
				if ( $count < $totalRecords ){
					$pdfFile->remove();
				}
				++$count;	
			}
        	
        } else {
        	$pdfFile = $modx->newObject('MessageMedia');
        }
        $pdf_name = 'pdf_'.$group_data['id'].'_sermon_'.time();
		
        $file_path = str_replace( 
                $MessageSermon->config['uploadPath'], '', 
                $uploader->moveFile(
                    'upload_pdf', 
                    $MessageSermon->config['uploadPath'], 
                    $pdf_name
                ) 
            );
			$pdf_data = array(
                'sermon_id' => $sermon->get('id'),
                'create_date' => date('Y-m-d H:i:s'),
                'type' => 'document',
                'file_ext' => $uploader->fileExt('upload_pdf'),
                'name' => $sermon->get('title'),
                'description' => 'PDF file',
                'active' => 'Yes',
                'allow_download' => 'Yes',
                'file' => $file_path
            );
            $pdfFile->fromArray($pdf_data);
            $pdfFile->save();
    } else {
        // failed give reason why:
         $modx->error->addField('upload_pdf',$modx->lexicon('message.sermon_err_required'));
         return $modx->error->failure();
    }
}
if( isset($_REQUEST['delete_audio']) ) {
    // validate the file:
    // load the album data NEED THIS!
    $messageGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
    $group_data = array();
    if ( is_object($messageGroup) ) {
        $group_data = $messageGroup->toArray();
    }
    
        $c = $modx->newQuery('MessageMedia');
		$c->where(array(
			'sermon_id' => $sermon->get('id'),
			'type' => 'audio'
		));
		// $audioFile = $modx->getObject('MessageMedia', $c);// returns only 1 record
		$totalRecords = $modx->getCount('MessageMedia', $c);
		$audioFiles =  $modx->getIterator('MessageMedia', $c);// returns all records
        if ( $totalRecords > 0 ) {
        	foreach ($audioFiles as $audioFile) {
				$audio_name = $audioFile->get('file');
				unlink($MessageSermon->config['uploadPath'].$audio_name);// full path...
				$audioFile->remove();	
			}
        	
        } else {
        	return $modx->error->failure($modx->lexicon('message.sermon_err_delete_file'));
        }	
}
if( isset($_REQUEST['delete_video']) ) {
    // validate the file:
    // load the album data NEED THIS!
    $messageGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
    $group_data = array();
    if ( is_object($messageGroup) ) {
        $group_data = $messageGroup->toArray();
    }
    
        $c = $modx->newQuery('MessageMedia');
		$c->where(array(
			'sermon_id' => $sermon->get('id'),
			'type' => 'video'
		));
		// $audioFile = $modx->getObject('MessageMedia', $c);// returns only 1 record
		$totalRecords = $modx->getCount('MessageMedia', $c);
		$videoFiles =  $modx->getIterator('MessageMedia', $c);// returns all records
        if ( $totalRecords > 0 ) {
        	foreach ($videoFiles as $videoFile) {
				$video_name = $videoFile->get('file');
				unlink($MessageSermon->config['uploadPath'].$video_name);// full path...
				$videoFile->remove();	
			}
        	
        } else {
        	return $modx->error->failure($modx->lexicon('message.sermon_err_delete_file'));
        }	
}
if( isset($_REQUEST['delete_pdf']) ) {
    // validate the file:
    // load the album data NEED THIS!
    $messageGroup = $modx->getObject('MessageGroup', array('id' => $group_id));
    $group_data = array();
    if ( is_object($messageGroup) ) {
        $group_data = $messageGroup->toArray();
    }
    
        $c = $modx->newQuery('MessageMedia');
		$c->where(array(
			'sermon_id' => $sermon->get('id'),
			'type' => 'document'
		));
		// $audioFile = $modx->getObject('MessageMedia', $c);// returns only 1 record
		$totalRecords = $modx->getCount('MessageMedia', $c);
		$pdfFiles =  $modx->getIterator('MessageMedia', $c);// returns all records
        if ( $totalRecords > 0 ) {
        	foreach ($pdfFiles as $pdfFile) {
				$pdf_name = $pdfFile->get('file');
				unlink($MessageSermon->config['uploadPath'].$pdf_name);// full path...
				$pdfFile->remove();	
			}
        	
        } else {
        	return $modx->error->failure($modx->lexicon('message.sermon_err_delete_file'));
        }	
}
/* save */
if ($sermon->save() == false) {
    return $modx->error->failure($modx->lexicon('message.group_err_save'));
}

return $modx->error->success('',$sermon);