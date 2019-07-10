//jquery ajax再封装
function xpost(type,url,data,datatype,su,er){
	$.ajax({
		type     : type,//向后台请求的方式，有post，get两种方法
		url      : url,//url填写的是请求的路径
		cache    : false,//缓存是否打开
		data     : data,
		dataType : datatype,//请求的数据类型
		success  : function(data) {su(data);},
		error    : function(XMLHttpRequest, textStatus, errorThrown) {er(XMLHttpRequest,textStatus,errorThrown);}
	});
}

function xpostForm(url,formname,su,er){
	var f    = $("#"+formname)[0];
	var data = new FormData(f);

	$.ajax({  
	    url         : url,
	    type        : 'POST',  
	    data        : data,
	    dataType    : 'json',//请求的数据类型
	    cache       : false,  
	    processData : false,  
	    contentType : false,
		success     : function(data) {su(data);},
		error       : function(XMLHttpRequest, textStatus, errorThrown) {er(XMLHttpRequest,textStatus,errorThrown);}
	});
}