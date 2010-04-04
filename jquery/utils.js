/*
*
* utils: useful js functions.
* @author Khramkov Ivan.
* 
*/

/*
*@function createJSONContainer
*@description creates div container for JSON, requested from server
*@return HTMLdiv
*/
function createJSONContainer() {
    var json = $('<div id = "json"></div>');
	$('body').append(json);
	return $('#json');
}
/*
*@function createJSONContainer
*@param HTMLDiv list
*@description cleares and hides list...
*/
function refreshList(list) {
    list.html('');
	list.hide();
}
/*
*@function updateField
*@param HTMLInputText field
*@param String value
*@description append value to at the end of field, replaced last unfinished  value...
*/
function updateField(field, value) {
    //if more than one items, replace last...
    if (field.value.indexOf(',') > -1) {
        val = field.value.split(',');
		val[val.length - 1] = value;
	}
	else {
	    val = value;
	}
	field.value = (field.value.indexOf(',') > -1)? val.join(',') : val;
	field.focus();
}
/*
*@function autoComplete
*@param string prefix
*@param String url
*@param HTMLDiv list
*@param HTMLInputText fieldToUpdate
*@description search at the url all values matched with prefix and returns list into the dest...
*/
function autoComplete(prefix, url, list, fieldToUpdate) {
	//If more, than one value in list...
	if (prefix.indexOf(',') > -1) {
	    prefix = prefix.split(',');
		//get last prefix in list...
		prefix = prefix[prefix.length - 1];
	}
	//prefix = prefix.replace(' ', '');
	//Minimal length required...
	if (prefix.length < 3) {
	    refreshList(list);
		return false;
	}
	//Block, which contains JSON code...
	var json = createJSONContainer();
	//Get values list...
	json.load(
	    url, 
        {prefix: prefix},
	    function () {
	        refreshList(list);
		    var values = eval('r = ' + json.html());
		    //array of values: {values: [{id: id, name: name}, ..]}
		    values = values.values;
		    //if no values returned...
		    if (!values.length) {
		        return false;
		    }
		    for (i in values) {
		        var value = $('<div class = "value_list_item">' + values[i].name + '</div>');
		        //If click item...
			    value.click(function() {updateField(fieldToUpdate, $(this).html()); $(this).parent().hide();});
			    list.append(value);
		    }
		    list.show();
			json.remove();
	    }
    );
}