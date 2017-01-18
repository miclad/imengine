function doubleCombo(masterId,slaveId,url,options,slave_selected,linked){
	
	if(typeof masterId == "string")
		this.master=document.forms[0].elements[masterId];
	else
		this.master = masterId;
	this.slave=slaveId;
	this.options=options;
	this.ajaxHelper = new net.ContentLoader(this,url,"POST",options);
	this.initializeBehavior();
	this.optArray=new Array();
	this.parIndex="";
	this.slave_selected = slave_selected;
	this.linked = linked;
	
	
	
}

doubleCombo.prototype = {
	
	initializeBehavior: function(){
		var oThis = this;
		this.master.onchange = function(){oThis.masterComboChanged();}
	},
	
	masterComboChanged: function(){
		this.parIndex = this.master.options[this.master.selectedIndex].value;
		if(this.optArray["id"+this.parIndex]!=null){
			this.slave_selected = "";
			this.fillSlave();
		}
		else{
			this.ajaxHelper.sendRequest('id='+this.parIndex);
		}
	},
	
	ajaxUpdate: function(request){
		this.optArray["id"+this.parIndex] = this.createOptions(request.responseXML.documentElement);
		this.fillSlave();
	},
	
	fillSlave: function(){

				this.slave.length=0;
				for(var i=0;i<this.optArray["id"+this.parIndex].length;i++){	
					newOpt=document.createElement('option');
					newOpt.text=this.optArray["id"+this.parIndex][i]["text"];
					newOpt.value=this.optArray["id"+this.parIndex][i]["value"];
					if(this.slave_selected == newOpt.value)
						newOpt.selected = true;
					if(navigator.appName == "Microsoft Internet Explorer") 
						this.slave.add(newOpt);
					else
						this.slave.add(newOpt,null);
				}
				if(this.linked)
					this.linked.masterComboChanged();


	},
	
	createOptions: function(ajaxResponse){
		var newOptions = new Array();
		var entries = ajaxResponse.getElementsByTagName('entry');		
		for(var i=0; i<entries.length; i++){
			var text=this.getElementContent(entries[i],'optionText');
			var value=this.getElementContent(entries[i],'optionValue');
			newOptions.push({"text":text,"value":value});
		}
		return newOptions;
	},
	
	getElementContent: function(element,tagName){
		var childElement = element.getElementsByTagName(tagName)[0];
		return (childElement.text != undefined) ? childElement.text : childElement.textContent;
	}
}