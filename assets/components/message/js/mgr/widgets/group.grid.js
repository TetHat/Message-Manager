/* make a local <select>/combo box */
Cmp.combo.FeedStatus = function(config) {
    config = config || {};
    Ext.applyIf(config,{
       //displayField: 'name'
        //,valueField: 'id'
        //,fields: ['id', 'name']
        store: ['approved','hidden','pending','auto_approved']
        //,url: Testapp.config.connectorUrl
        ,baseParams: { action: '' ,combo: true }
        //,mode: 'local'
        ,editable: false
    });
    Cmp.combo.FeedStatus.superclass.constructor.call(this,config);
};
//Ext.extend(MODx.combo.FeedStatus, MODx.combo.ComboBox);
Ext.extend(Cmp.combo.FeedStatus,MODx.combo.ComboBox);
Ext.reg('feedstatus-combo', Cmp.combo.FeedStatus);


/* YOU will need to edit this file with proper names, follow the cases(upper/lower) */
Cmp.grid.group = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'cmp-grid-group'
        ,url: Cmp.config.connectorUrl
        ,baseParams: { action: 'mgr/group/getList' }
        ,save_action: 'mgr/group/updateFromGrid'
        ,fields: ['id','name','description','create_date', 'default_group' ]
        ,paging: true
        ,autosave: true
        ,remoteSort: true
        ,anchor: '97%'
        ,autoExpandColumn: 'description'
        ,columns: [{
            header: _('id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 30
        },{
            header: _('message.group_name')
            ,dataIndex: 'name'
            ,sortable: true
            ,width: 45
            ,editor: { xtype: 'textfield' }
        },{
            header: _('message.group_description')
            ,dataIndex: 'description'
            ,sortable: true
            ,width: 65 
            ,editor: { xtype: 'textfield' }
        }/*,{
            header: _('message.group_file_height')
            ,dataIndex: 'file_height'
            ,sortable: false
            ,width: 20
            ,editor: { xtype: 'textfield' }
        }*/]
        ,tbar: [{
            xtype: 'textfield'
            ,id: 'cmp-search-filter'
            ,emptyText: _('message.group_search')
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
        },{
            text: _('message.group_create')
            ,handler: { xtype: 'cmp-window-group-create' ,blankValues: true }
        }]
    });
    Cmp.grid.group.superclass.constructor.call(this,config);
};

Ext.extend(Cmp.grid.group,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,getMenu: function() {
        var m = [{
            text: _('message.group_update')
            ,handler: this.updateFeed
        },'-',{
            text: _('message.group_remove')
            ,handler: this.removeFeed
        }];
        this.addContextMenuItem(m);
        
        return true;
    }
    ,updateFeed: function(btn,e) {
        console.log('Update');
        if (!this.updateFeedWindow) {
            this.updateFeedWindow = MODx.load({
                xtype: 'cmp-window-group-update'
                ,record: this.menu.record
                ,listeners: {
                    'success': {fn:this.refresh,scope:this}
                }
            });
        } else {
            this.updateFeedWindow.setValues(this.menu.record);
        }
        this.updateFeedWindow.show(e.target);
    }

    ,removeFeed: function() {
        MODx.msg.confirm({
            title: _('message.group_remove')
            ,text: _('message.group_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/group/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('cmp-grid-group',Cmp.grid.group);


Cmp.window.UpdateFeed = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('message.group_update')
        ,url: Cmp.config.connectorUrl
        ,baseParams: {
            action: 'mgr/group/update'
        }
        ,fields: [{ 
            html: _('message.group_update_desc')+'<br />'
        },{
            xtype: 'hidden'
            ,name: 'id'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('message.group_name')
            ,name: 'name'
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: _('message.group_description')
            ,name: 'description'
            ,width: 300
        }/*,{
            xtype: 'textfield'
            ,fieldLabel: _('message.group_file_height')
            ,name: 'file_height'
            ,width: 100
        }*/
        ]
    });
    Cmp.window.UpdateFeed.superclass.constructor.call(this,config);
};
Ext.extend(Cmp.window.UpdateFeed,MODx.Window);
Ext.reg('cmp-window-group-update',Cmp.window.UpdateFeed);

Cmp.window.CreateAlbum = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('message.group_create')
        ,url: Cmp.config.connectorUrl
        ,baseParams: {
            action: 'mgr/group/create'
        }
        ,fields: [
        { 
            html: _('message.group_create_desc')+'<br />'
        },{
            xtype: 'textfield'
            ,fieldLabel: _('message.group_name')
            ,name: 'name'
            ,value: ''
            ,requried: true
            ,width: 300
        },{
            xtype: 'textarea'
            ,fieldLabel: _('message.group_description')
            ,name: 'description'
            ,value: ''
            ,requried: true
            ,width: 300 
        }
        ]
    });
    Cmp.window.CreateAlbum.superclass.constructor.call(this,config);
};
Ext.extend(Cmp.window.CreateAlbum,MODx.Window);
Ext.reg('cmp-window-group-create',Cmp.window.CreateAlbum);
