--------------------
message
--------------------
Version: 1.0.0 pl
Since: July 31st, 2012
Author: Joshua Gulledge <jgulledge19@hotmail.com>
License: GNU GPLv2 (or later at your option)

Message manager is a CMP (custom manager page) and snippet for MODX Revolution. The manager allows you to easily create messages that include audio, video, and pdf related documents. 
You can create groups that contain multiple message records inside of them, keeping all your messages nicely organized. It also includes a snippet that you can place on any page to 
display any group of audio and video files.

Features:
	- Create a message to display on your site. Include who is talking, when it was presented, and a description.
	- Upload audio, video, and pdf files for each message.
	- Create groups for each message to go in.
	- Easily display a certain group with the built in snippet call.

Install:
	- Install via the MODX Revolution packagemanagment
	- Install the Flowplayer extra, located at http://modx.com/extras/package/flowplayer.

Usage:
	-Basic options creating a message

	<div id="chapelMedia">
	    
	</div>
	<p>[[!messageSermons]]</p>
	
	This chapelMedia div is the container for the audio and video files. When the audio or video link is clicked on, it creates the object inside this div.
	
	-If you want to display a specific group, just do this:

	<div id="chapelMedia">
	    
	</div>
	<p>[[!messageSermons? &group_id=`1`]]</p>
	
	This chapelMedia div is the container for the audio and video files. When the audio or video link is clicked on, it creates the object inside this div.

Thanks for using message manager!
Josh Gulledge