<?php
/**
 * @package doodle
 * @subpackage processors
 */

/* get obj */
if (empty($scriptProperties['id'])) {
    return $modx->error->failure($modx->lexicon('message.sermon_err_notset'));
}
$sermon = $modx->getObject('MessageSermons',$scriptProperties['id']);
if (empty($sermon)) {
    return $modx->error->failure($modx->lexicon('message.sermon_err_notfound'));
}

/* remove */
if ($sermon->remove() == false) {
    return $modx->error->failure($modx->lexicon('message.sermon_err_remove'));
}

return $modx->error->success('',$sermon);