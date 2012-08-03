/* make a local <select>/combo box */
Cmp.combo.SermonStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        store: ['Yes','No']
        ,baseParams: { action: '' ,combo: true }
        ,editable: false
    });
    Cmp.combo.SermonStatus.superclass.constructor.call(this,config);
};
Ext.extend(Cmp.combo.SermonStatus,MODx.combo.ComboBox);
Ext.reg('sermonstatus-combo', Cmp.combo.SermonStatus);

// the album filter
Cmp.combo.SermonGroup = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        displayField: 'name'
        ,valueField: 'id'
        ,value: cmpAlbumId
        ,fields: ['id', 'name']
        ,url: Cmp.config.connectorUrl
        ,baseParams: { action: 'mgr/group/getList',combo: true }
        ,editable: false
    });
    Cmp.combo.SermonGroup.superclass.constructor.call(this,config);
};
Ext.extend(Cmp.combo.SermonGroup,MODx.combo.ComboBox);
Ext.reg('sermon-combo-group', Cmp.combo.SermonGroup);

// the slide filter
Cmp.combo.SermonFilter = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        //displayField: 'name'
        //,valueField: 'id'
        //,fields: ['id', 'name']
        value: 'current'
        ,store: ['current', 'future', 'archive', 'tbd']
        //,url: Testapp.config.connectorUrl
        ,baseParams: { action: '' ,combo: true }
        //,mode: 'local'
        ,editable: false
    });
    Cmp.combo.SermonFilter.superclass.constructor.call(this,config);
};
//Ext.extend(MODx.combo.SermonStatus, MODx.combo.ComboBox);
Ext.extend(Cmp.combo.SermonFilter,MODx.combo.ComboBox);
Ext.reg('Sermonstatus-combo-filter', Cmp.combo.SermonFilter);


/* YOU will need to edit this file with proper names, follow the cases(upper/lower) */
Cmp.grid.sermon = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'cmp-grid-sermon'
        ,url: Cmp.config.connectorUrl
        ,baseParams: { 
            action: 'mgr/sermon/getList'
            ,group_id: cmpAlbumId }
        ,save_action: 'mgr/sermon/updateFromGrid'
        ,fields: ['id', 'group_id', 'create_date','sermon_id', 'sermon_date', 'title', 'speaker', 'description', 'tags', 'active', 'upload_audio', 'upload_video', 'audio_path', 'video_path', 'pdf_path', 'upload_time', 'file_size', 'type', 'file_ext' ]
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'description'
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 10
        },{
            header: _('message.sermon_date')
            ,dataIndex: 'sermon_date'
            ,sortable: true
            ,width: 20
            ,renderer : Ext.util.Format.dateRenderer('Y-m-d')
            ,editor: { xtype: 'datefield' }
        },{
            header: _('message.sermon_title')
            ,dataIndex: 'title'
            ,sortable: true
            ,width: 45
            ,editor: { xtype: 'textfield' }
        },{
            header: _('message.sermon_speaker')
            ,dataIndex: 'speaker'
            ,sortable: true
            ,width: 45
            ,editor: { xtype: 'textfield' }
        },{
            header: _('message.sermon_description')
            ,dataIndex: 'description'
            ,sortable: false
            ,width: 65 
            ,editor: { xtype: 'textarea' }
        },/*{
            header: _('message.sermon_file_path')
            //,tpl: this.templates.thumb
            /*,renderer: function(value, fcell) {
                return '<img src="'+Cmp.config.uploadUrl+value+'" height="100" />';
            }* /
            ,dataIndex: 'image_path'
            ,sortable: false
            ,width: 65 
            //,editor: { xtype: 'displayfield' }
        },*/{
            header: _('message.label_active')
            ,dataIndex: 'active'
            ,sortable: false
            ,width: 20
            ,editor: { xtype: 'sermonstatus-combo', renderer: 'value' }// 'textfield' } 
        }]
        ,tbar: [{
            xtype: 'sermon-combo-group'
            ,name: 'group_id'
            ,id: 'sermon-combo-group'
            //,emptyText: _('batcher.filter_by_template')
            ,width: 250
            ,allowBlank: false
            ,listeners: {
                'select': {fn:this.filterGroup,scope:this}
            }
        },/*{
            xtype: 'slidestatus-combo-filter'
            ,name: 'sort_type'
            ,id: 'slideshow-status-filter'
            //,emptyText: _('batcher.filter_by_template')
            ,width: 100
            ,allowBlank: false
            ,listeners: {
                'select': {fn:this.filterSlides,scope:this}
            }
        },/*{
            xtype: 'textfield'
            ,id: 'cmp-slide-search-filter'
            ,emptyText: _('message.sermon_search')
            ,listeners: {
                'change': {fn:this.search,scope:this}
                ,'render': {fn: function(cmp) {
                    new Ext.KeyMap(cmp.getEl(), {
                        key: Ext.EventObject.ENTER
                        ,fn: function() {
                            this.fireEvent('change',this);
                            this.blur();
                            return true;
                        }
                        ,scope: cmp
                    });
                },scope:this}
            }
        },*/{
            text: _('message.sermon_create')
            /*,renderer: function(value, cell) {
                url = '?a='+MODx.request.a+'&action=addslide'+'&album_id='+this.menu.record.slideshow_album_id;
                return '<a href="'+url+'">' + _('message.sermon_create') + '</a>';
            }*/
            ,handler: this.createSermon
        }]
    });
    Cmp.grid.sermon.superclass.constructor.call(this,config);
};
var audio_path = '';
var video_path = '';
var pdf_path = '';
Ext.extend(Cmp.grid.sermon,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function() {
        var m = [{
            text: _('message.sermon_update')
            ,handler: this.updateSermon
        },'-',{
            text: _('message.sermon_remove')
            ,handler: this.removeSermon
        }];
        this.addContextMenuItem(m);
        
        return true;
    }
    ,filterGroup: function(cb,nv,ov) {
        cmpAlbumId = cb.getValue();
        this.getStore().setBaseParam('group_id',cmpAlbumId);
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    /* ,filterSlides: function(cb,nv,ov) {
        this.getStore().setBaseParam('sort_type',cb.getValue());
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
   ,addSermon: function(btn,e) {
        location.href = '?a='+MODx.request.a+'&action=addsermon'+'&group_id='+cmpAlbumId;//+this.menu.record.slideshow_album_id;
    }*/
    ,updateSermon: function(btn,e) {
    	if(this.menu.record.audio_path !== "") {
    		audio_path = Cmp.config.uploadUrl+this.menu.record.audio_path;
    		audio_object = '<object><param name="src" value="http://bcwebtest01' + audio_path + '"><param name="controller" value="true"><embed controller="true" height="65px" autoplay="true" type="audio/mpeg" src="http://bcwebtest01' + audio_path + '" /></object>';
    	}
    	else {
    		audio_object = 'There is no current audio file. Upload one via the file uploader below.';
    	}
    	if(this.menu.record.video_path !== "") {
    		video_path = Cmp.config.uploadUrl+this.menu.record.video_path;
    		video_object = '<object><param name="src" value="http://bcwebtest01' + video_path + '"><param name="controller" value="true"><embed controller="true" type="audio/mpeg" src="http://bcwebtest01' + video_path + '" /></object>';
    	}
    	else {
    		video_object = 'There is no current video file. Upload one via the file uploader below.';
    	}
    	if(this.menu.record.pdf_path !== "") {
    		pdf_path = Cmp.config.uploadUrl+this.menu.record.pdf_path;
    		pdf_object = '<a target="_blank" href="http://bcwebtest01' + pdf_path + '">' + this.menu.record.pdf_path + '</a>';
    	}
    	else {
    		pdf_object = 'There is no current pdf file. Upload one via the file uploader below.';
    	}
        /*if (!this.menu.record || !this.menu.record.id  || !this.menu.record.group_id) {
            return false;
        }
        location.href = '?a='+MODx.request.a+'&action=editsermon'+'&group_id='+this.menu.record.group_id+
            '&sermon_id='+this.menu.record.id;
        */
        if (!this.updateSermonWindow) {
            this.updateSermonWindow = MODx.load({
                xtype: 'cmp-window-sermon-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
        this.menu.record.upload_audio = '';
        this.menu.record.upload_video = '';
        this.menu.record.upload_pdf = '';
        this.menu.record.delete_audio = '';
        this.menu.record.delete_video = '';
        this.menu.record.delete_pdf = '';
        this.updateSermonWindow.setValues(this.menu.record);
        this.updateSermonWindow.show(e.target);
        Ext.fly('audio_file').dom.innerHTML = audio_object;
        Ext.fly('video_file').dom.innerHTML = video_object;
        Ext.fly('pdf_file').dom.innerHTML = pdf_object;
    }
    ,createSermon: function(btn,e) {
        if (!this.createSermonWindow) {
            this.createSermonWindow = MODx.load({
                xtype: 'cmp-window-sermon-create'
                //,record: this.menu.record
                ,blankValues: true
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        }
        var defaultData = {
            active: 'Yes'
            ,id: cmpAlbumId
            ,upload_audio: ''
            ,upload_video: ''
        };
        //console.log(defaultData);
        this.createSermonWindow.setValues(defaultData);
        this.createSermonWindow.show(e.target);
    }
    ,removeSermon: function() {
        MODx.msg.confirm({
            title: _('message.sermon_remove')
            ,text: _('message.sermon_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/sermon/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('cmp-grid-sermon',Cmp.grid.sermon);

Cmp.window.UpdateSermon = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('message.sermon_update')
        ,url: Cmp.config.connectorUrl
        ,baseParams: {
            action: 'mgr/sermon/update'
        }
        ,fileUpload:true
        ,fields: [{
        	xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,border: true
            ,deferredRender: false
            ,forceLayout: true
            ,defaults: {
                autoHeight: true
                ,layout: 'form'
                ,deferredRender: false
                ,forceLayout: true
            }
	        ,items: [{
	            title: 'Fields'
	            ,layout: 'form'
	            ,cls: 'modx-panel'
	            ,items:[{ 
		            html: _('message.sermon_update')+'<br />'
		        },{
		            xtype: 'hidden'
		            ,name: 'id'
		        },{
		            xtype: 'hidden'
		            ,name: 'group_id'
		        },{
		            xtype: 'textfield'
		            ,fieldLabel: _('message.sermon_title')
		            ,name: 'title'
		            ,width: 300
		        },{
		            xtype: 'textfield'
		            ,fieldLabel: _('message.sermon_speaker')
		            ,name: 'speaker'
		            ,width: 100
		        },{
		            xtype: 'textarea'
		            ,fieldLabel: _('message.sermon_description')
		            ,name: 'description'
		            ,width: 300
		        },{
		            xtype: 'textfield'
		            ,fieldLabel: _('message.sermon_tags')
		            ,name: 'tags'
		            ,width: 100
		        },{
		            xtype: 'datefield'
		            ,fieldLabel: _('message.sermon_date')
		            ,name: 'sermon_date'
		            ,width: 110
		        },{
		            xtype: 'sermonstatus-combo'
		            ,fieldLabel: _('message.label_active')
		            ,name: 'active'
		            ,width: 100
		        }]
		        },{
		        	title: 'Audio File'
	                ,border: false
	                ,defaults: { autoHeight: true }  
	                ,items: [{ 
		            	html: '<p>' + _('message.label_upload_audio') + '</p><br />'
					},{
			            fieldLabel: _('message.label_current_audio')
			            ,html: '<p id="audio_file"></p>'
			        },{
			            xtype: 'textfield'
			            ,inputType: 'file'
			            ,fieldLabel: _('message.label_upload_audio')
			            ,name: 'upload_audio'
			            ,width: 100
			        },{
			            xtype: 'checkbox'
			            ,fieldLabel: _('message.label_delete_audio')
			            ,boxLabel: 'Yes'
			            ,value: 'Yes'
			            ,name: 'delete_audio'
			            ,width: 100
		        	}]
		        },{
		        	title: 'Video File'
	                ,border: false
	                ,defaults: { autoHeight: true }  
	                ,items: [{ 
		            	html: '<p>' + _('message.label_upload_video') + '</p><br />'
					},{
			            fieldLabel: _('message.label_current_video')
			            ,html: '<p id="video_file"></p>'
			        },{
			            xtype: 'textfield'
			            ,inputType: 'file'
			            ,fieldLabel: _('message.label_upload_video')
			            ,name: 'upload_video'
			            ,width: 100
		        	},{
			            xtype: 'checkbox'
			            ,fieldLabel: _('message.label_delete_video')
			            ,boxLabel: 'Yes'
			            ,value: 'Yes'
			            ,name: 'delete_video'
			            ,width: 100
		        	}]
		        },{
		        	title: 'PDF File'
	                ,border: false
	                ,defaults: { autoHeight: true }  
	                ,items: [{ 
		            	html: '<p>' + _('message.label_upload_pdf') + '</p><br />'
					},{
			            fieldLabel: _('message.label_current_pdf')
			            ,html: '<p id="pdf_file"></p>'
			        },{
			            xtype: 'textfield'
			            ,inputType: 'file'
			            ,fieldLabel: _('message.label_upload_pdf')
			            ,name: 'upload_pdf'
			            ,width: 100
		        	},{
			            xtype: 'checkbox'
			            ,fieldLabel: _('message.label_delete_pdf')
			            ,boxLabel: 'Yes'
			            ,value: 'Yes'
			            ,name: 'delete_pdf'
			            ,width: 100
		        	}]
		        }]
        }]
    });
    Cmp.window.UpdateSermon.superclass.constructor.call(this,config);
};
Ext.extend(Cmp.window.UpdateSermon,MODx.Window);
Ext.reg('cmp-window-sermon-update',Cmp.window.UpdateSermon);

Cmp.window.CreateSermon = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('message.sermon_create')
        ,url: Cmp.config.connectorUrl
        ,baseParams: {
            action: 'mgr/sermon/create'
        }
        ,fileUpload:true
        ,fields: [{
        	xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,border: true
            ,deferredRender: false
            ,forceLayout: true
            ,defaults: {
                autoHeight: true
                ,layout: 'form'
                ,deferredRender: false
                ,forceLayout: true
            }
	        ,items: [{
	            title: 'Fields'
	            ,layout: 'form'
	            ,cls: 'modx-panel'
	            ,items:[{ 
		            html: _('message.sermon_create')+'<br />'
		        },{
		            xtype: 'hidden'
		            ,name: 'id'
		        },{
		            xtype: 'hidden'
		            ,name: 'group_id'
		        },{
		            xtype: 'textfield'
		            ,fieldLabel: _('message.sermon_title')
		            ,name: 'title'
		            ,width: 300
		        },{
		            xtype: 'textfield'
		            ,fieldLabel: _('message.sermon_speaker')
		            ,name: 'speaker'
		            ,width: 100
		        },{
		            xtype: 'textarea'
		            ,fieldLabel: _('message.sermon_description')
		            ,name: 'description'
		            ,width: 300
		        },{
		            xtype: 'textfield'
		            ,fieldLabel: _('message.sermon_tags')
		            ,name: 'tags'
		            ,width: 100
		        },{
		            xtype: 'datefield'
		            ,fieldLabel: _('message.sermon_date')
		            ,name: 'sermon_date'
		            ,width: 110
		        },{
		            xtype: 'sermonstatus-combo'
		            ,fieldLabel: _('message.label_active')
		            ,name: 'active'
		            ,width: 100
		        }]
		        },{
		        	title: 'Upload Files'
	                ,border: false
	                ,defaults: { autoHeight: true } 
	                ,items: [{
	                	html: '<p>' + _('message.sermon_updload_files') + '</p><br />'
	                },{
			            xtype: 'textfield'
			            ,inputType: 'file'
			            ,fieldLabel: _('message.label_upload_audio')
			            ,name: 'upload_audio'
			            ,width: 100
			        },{
			            xtype: 'textfield'
			            ,inputType: 'file'
			            ,fieldLabel: _('message.label_upload_video')
			            ,name: 'upload_video'
			            ,width: 100
		        	}]
		    }]
		}]
    });
    Cmp.window.CreateSermon.superclass.constructor.call(this,config);
};
Ext.extend(Cmp.window.CreateSermon,MODx.Window);
Ext.reg('cmp-window-sermon-create',Cmp.window.CreateSermon);