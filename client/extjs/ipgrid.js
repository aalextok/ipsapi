Ext.require(['Ext.data.*', 'Ext.grid.*', 'Ext.form.*']);

//var url = 'http://localhost/ipsapi/web/';

//var url = 'http://cupbup.esy.es/ipsapi/web/';


Ext.Ajax.useDefaultXhrHeader = false;
Ext.Ajax.cors = true;

Ext.define('IpAddr', {
    extend: 'Ext.data.Model',
    fields: [ 'id', 'ip', 'comment']
});


Ext.onReady(function(){
    
    var updateForm = Ext.create('Ext.form.FormPanel', {
        url: url + 'update',
        renderTo: document.body,
        style: {
            marginLeft: 'auto',
            marginRight: 'auto',
            marginTop: '100px'
        },
        title: "",
        width: 400,
        frame: true,
        buttons: [{
            text: "Update IPs",
            handler: function(){
                var form = this.up('form').getForm();
                form.submit({
                    success: function(form, action) {
                       Ext.Msg.alert('Success', action.result.msg);
                       store.load();
                    },
                    failure: function(form, action) {
                        Ext.Msg.alert('Failed', JSON.parse(action.response.responseText).msg);
                    }
                });
            }
        }]
    });

    updateForm.show();

    var store = Ext.create('Ext.data.Store', {
        autoLoad: true,
        autoSync: true,
        model: 'IpAddr',
        proxy: {
            type: 'rest',
            url: url,
            reader: {
                type: 'json',
                root: 'data'
            },
            writer: {
                type: 'json'
            }
        }
    });
    
    var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        listeners: {
            cancelEdit: function(rowEditing, context) {
                // Canceling editing of a locally added, unsaved record: remove it
                if (context.record.phantom) {
                    store.remove(context.record);
                }
            }
        }
    });
    
    var grid = Ext.create('Ext.grid.Panel', {
        renderTo: document.body,
        style: {
            marginLeft: 'auto',
            marginRight: 'auto'
        },
        plugins: [rowEditing],
        width: 400,
        height: 300,
        frame: true,
        title: 'IP addresses',
        store: store,
        columns: [{
            text: 'ID',
            width: 60,
            sortable: true,
            dataIndex: 'id'
        }, {
            header: 'IP',
            width: 100,
            sortable: true,
            dataIndex: 'ip'
        }, {
            text: 'Comment',
            width: 220,
            sortable: false,
            dataIndex: 'comment',
            field: {
                xtype: 'textfield'
            }
        }]
    });
    grid.getSelectionModel().on('selectionchange', function(selModel, selections){
        grid.down('#delete').setDisabled(selections.length === 0);
    });
});