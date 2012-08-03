<?php
/**
 * Get a list
 * 
 * @package cmp
 * @subpackage processors
 * This file needs to be customized
 */
/* setup default properties */
$isLimit = !empty($scriptProperties['limit']);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$sort = $modx->getOption('sort',$scriptProperties,'id');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$group_id = $modx->getOption('group_id',$scriptProperties,'1');

$query = $modx->getOption('query',$scriptProperties,'');
/* build query */
$c = $modx->newQuery('MessageSermons');

$count = $modx->getCount('MessageSermons',$c);
$c->where(array(
	'group_id' => $group_id
));
$c->sortby($sort,$dir);
if ($isLimit) {
    $c->limit($limit,$start);
}
$sermons = $modx->getIterator('MessageSermons', $c);

/* iterate */
$list = array();
foreach ($sermons as $sermon ) {
    $sermon_array = $sermon->toArray();
    // make the date readable
    $medias = $sermon->getMany('Media');
	foreach ($medias as $media) {
		$type = $media->get('type');
		switch ($type) {
			case 'audio':
				$sermon_array['audio_path'] = $media->get('file');
				break;
			case 'video':
				$sermon_array['video_path'] = $media->get('file');
				break;
			case 'document':
				$sermon_array['pdf_path'] = $media->get('file');
				break;
			default:
				
				break;
		}
		
	}
    $sermon_array['sermon_date'] = trim(str_replace('00:00:00', '', $sermon_array['sermon_date']));
    $list[] = $sermon_array;
}
return $this->outputArray($list,$count);