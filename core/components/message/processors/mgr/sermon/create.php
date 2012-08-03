<?php
/**
 * @package CMP
 * @subpackage processors
 * This file needs to be customized
 
*/

if (empty($scriptProperties['title']) ) {
    $modx->error->addField('title',$modx->lexicon('message.group_err_required'));
}
if (empty($scriptProperties['speaker']) ) {
    $modx->error->addField('speaker',$modx->lexicon('message.group_err_required'));
}
if (empty($scriptProperties['sermon_date']) ) {
    $modx->error->addField('sermon_date',$modx->lexicon('message.group_err_required'));
}
if (empty($scriptProperties['active']) ) {
    $modx->error->addField('active',$modx->lexicon('message.group_err_required'));
}
if ( empty($scriptProperties['description']) ) {
    $modx->error->addField('description',$modx->lexicon('message.sermon_err_required'));
}

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$sermon = $modx->newObject('MessageSermons');
$sermon->fromArray($scriptProperties);
$id = $scriptProperties['id'];

$sermon->set('group_id', $id);
$time = date('Y-m-d g:h:s');
$sermon->set('create_date', $time);

/* save */ 
if ( $sermon->save() == false) {
    return $modx->error->failure($modx->lexicon('message.sermon_err_save'));
}

return $modx->error->success('',$sermon);