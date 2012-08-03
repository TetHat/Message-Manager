<?php
/**
 * ChurchEvents
 *
 * Copyright 2010-11 by Josh Gulledge <jgulledge19@hotmail.com>
 *
 * This file is part of Quip, a simple commenting component for MODx Revolution.
 *
 * Quip is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Quip is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Quip; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package churchevents
 */
/**
 * Resolves setup-options settings by setting email options.
 *
 * @package churchevents
 * @subpackage build
 */
$success= false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        if ( isset($options['createSample']) && $options['createSample'] ) {
            $modx->log(xPDO::LOG_LEVEL_INFO,'Loading sample data ');
            $modx =& $object->xpdo;
            // add package
            $s_path = $modx->getOption('core_path').'components/message/model/';
            $modx->addPackage('message', $s_path);
            // create a default slideshow:
            $c = $modx->newQuery('MessageGroup');
            $c->limit(1,0);
            $count = $modx->getCount('MessageGroup',$c);
            if ( $count == 0 ) {
                $album = $modx->newObject('MessageGroup');
                $album->fromArray(array(
                    'name' => 'Fall 2011', 
                    'description' => 'This is the default message'
                    ));
                if ($album->save() == false) {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,'ERROR adding default data to table: MessageGroup ');
                } else {
                    $modx->log(xPDO::LOG_LEVEL_INFO,'Added default data to table: MessageGroup ');
                }
            } else {
                $album = $modx->getObject('MessageGroup', $c );
            }
            // add default pictures
            $now = date('Y-m-d 00:00:00');
            $panes = array(
                    array(
                        'sermon_date' => $now,
                        'title' => 'First Message',
                        'description' => 'MODX Rules!',
                        'speaker' => 'Speaker',
                        'audio_path' => 'test1.mp3',
                        'video_path' => 'test1.mp4',
                        'pdf_path' => 'test1.pdf',
                        'active' => 'Yes'
                    ),
                    array(
                        'sermon_date' => $now,
                        'title' => 'Second Message',
                        'description' => 'MODX Rules!',
                        'speaker' => 'Speaker',
                        'audio_path' => 'test2.mp3',
                        'video_path' => 'test2.mp4',
                        'pdf_path' => 'test2.pdf',
                        'active' => 'Yes'
                    ),
                    array(
                        'sermon_date' => $now,
                        'title' => 'Third Message',
                        'description' => 'MODX Rules!',
                        'speaker' => 'Speaker',
                        'audio_path' => 'test3.mp3',
                        'video_path' => 'test3.mp4',
                        'pdf_path' => 'test3.pdf',
                        'active' => 'Yes'
                    ),
                );
            foreach ( $panes as $pane ) {
                $slide = $modx->newObject('MessageSermons');
                $pane['group_id'] = $album->get('id'); 
                $slide->fromArray($pane);
                /* save */
                if ($slide->save() == false) {
                    $modx->log(xPDO::LOG_LEVEL_ERROR,'ERROR adding default data to table: MessageSermons ');
                } else {
                    $modx->log(xPDO::LOG_LEVEL_INFO, 'Added default data to table: MessageSermons ');
                }
            }
        }
        break;
    case xPDOTransport::ACTION_UPGRADE:
        $success= true;
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $success= true;
        break;
}
return $success;