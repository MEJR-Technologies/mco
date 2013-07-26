var changePasswordForm = new Ext.form.FormPanel({
  title:"",
  width:425,
  frame:true,
  items:[
	new Ext.form.TextField({
			id:'pwd',
			fieldLabel:"Password",
			name:'pwd',
			width:275,
			allowBlank:false,
			required:true,
			inputType:'password',			
			blankText:"Please a new password."
	}),
	new Ext.form.TextField({
				id:'pwd_confirm',
				fieldLabel:"Confirm Password",
				name:'pwd_confirm',
				width:275,
				allowBlank:false,
				required:true,
				inputType:'password',				
				blankText:"Please confirm your new password."
	}),
	{
		xtype:'hidden',
		name:'action',
		id:'action',
		value:'change'			
	}  
  ],
	buttons: [{
			text:"Cancel",
			handler: function () {
				changePasswordWindow.hide();
			}
		},{
			text:"Change Password",
			handler:function(){
			    changePasswordForm.getForm().submit({
				url:'/server/proxy/?proxy=password.php',
				success:function(f,a){
					Ext.Msg.alert("Confirm",a.result.message);
					changePasswordForm.getForm().reset();
					changePasswordWindow.hide();
					
					window.location='/?doLogout=true';
				},
				failure:function(f,a){
					Ext.Msg.alert("Error",a.result.message);
					changePasswordForm.getForm().reset();					
				}
			    });	    
			}
		}]
});

var changePasswordWindow = new Ext.Window({
	  title: 'My Church Office: Forgot Password',
	  layout: 'fit',                 
	  height: 150,
	  width: 425,                            
	  closable: true,
	  modal:true,
	  resizable: false,                              
	  draggable: false,
	  closeAction:'hide',
	  items: [changePasswordForm]                     
});

function changePassword(){
	changePasswordWindow.show();	
}
