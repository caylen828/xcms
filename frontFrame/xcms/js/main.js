/*3.用正则表达式实现html转码*/
function htmlEncode(str){  
     var s = "";
     if(str.length == 0) return "";
     s = str.replace(/&/g,"&amp;");
     s = s.replace(/</g,"&lt;");
     s = s.replace(/>/g,"&gt;");
     s = s.replace(/ /g,"&nbsp;");
     s = s.replace(/\'/g,"&#39;");
     s = s.replace(/\"/g,"&quot;");
     return s;  
}
/*4.用正则表达式实现html解码*/
function htmlDecode(str){  
     var s = "";
     if(str.length == 0) return "";
     s = str.replace(/&amp;/g,"&");
     s = s.replace(/&lt;/g,"<");
     s = s.replace(/&gt;/g,">");
     s = s.replace(/&nbsp;/g," ");
     s = s.replace(/&#39;/g,"\'");
     s = s.replace(/&quot;/g,"\"");
     return s;  
}

$(function () { $("[data-toggle='tooltip']").tooltip(); });

toastr.options.positionClass = 'toast-top-center';
toastr.options.timeOut=5000;
function subLogin()  //用户登录操作
{
  var $ab=$('#mybutton').button('loading');  //设定按钮的加载状态

  xpost("post","api/login.php",$("#loginForm").serialize(),"json",
    function(data){
      //success
      /*
      bootbox.alert("数据反馈: " + data.data.admin_account+" "+data.data.admin_password+" "+ data.code+" "+ data.msg+" "+ data.color, 
      function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
      */
      if(data.code>0){
        toastr.success('登录成功，开始进入管理界面。');
        setTimeout(function () { window.open("index.php","_self"); },1000);
      }else{
        bootbox.alert("登录失败！"+data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}

function unLogin(type)  //退出登录操作
{
  if (type==''){
    var $ab=$('#loginOutId').button('loading');  //设定按钮的加载状态
  }else{
    var $ab=$('#loginOutId2').button('loading');  //设定按钮的加载状态
  }
  
  xpost("post","api/loginOut.php",{},"json",
    function(data){
      //success
      if(data.code>0){
        toastr.success('退出登录成功，返回主界面。');
        setTimeout(function () { window.open("login.html","_self"); },1000);
      }else{
        bootbox.alert("退出登录失败！"+data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}

//模态框显示
function showModal(modalId)
{
  $('#'+modalId).modal('show');
}

//模态框关闭
function hideModal(modalId)
{
  $('#'+modalId).modal('hide');
}

//添加管理员
function adminAdd()  //管理员添加提交
{
  var $ab=$('#adminAddButton').button('loading');  //设定按钮的加载状态
  
  xpostForm("api/admin.php","adminAddForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('管理员添加成功，重新加载页面。');
        setTimeout(function () { $ab.button('reset'); },1000);
        adminAddForm.reset();
        setTimeout(function () {location.reload();},1000);
      }else{
        bootbox.alert(data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}

//修改管理员资料
function adminModify()  //管理员编辑和删除
{
  if (document.getElementById('modifyop').value=='delete')
  {
    //询问是否真的要删除操作
    bootbox.confirm({ 
      size: "small",
      message: "你确定要删除此管理员账号么？删除将无法恢复！",
      callback: function(result){ 
        if (result) adminModifySub();
      }
    })
  }else adminModifySub();

  function adminModifySub()
  {
    var $ab=$('#adminModifyButton').button('loading');  //设定按钮的加载状态
    
    xpostForm("api/admin.php","adminModifyForm",
      function(data){
        //success
        if(data.code>0){
          toastr.success('管理员资料编辑成功，重新加载页面。');
          setTimeout(function () { $ab.button('reset'); },1000);
          adminModifyForm.reset();
          setTimeout(function () {location.reload();},1000);
        }else{
          bootbox.alert(data.msg, function(){
            setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
          });
        }
      },
      function(XMLHttpRequest,textStatus,errorThrown){
        //error
        bootbox.alert("接口调用错误，操作失败！", function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    );
  }

  return false;
}

//管理员权限设定
function adminAudit()  //权限添加设定
{
  var $ab=$('#adminAuditButton').button('loading');  //设定按钮的加载状态
  
  xpostForm("api/admin.php","adminAuditForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('权限设定成功，重新加载页面。');
        setTimeout(function () { $ab.button('reset'); },1000);
        adminAuditForm.reset();
        setTimeout(function () {location.reload();},1000);
      }else{
        bootbox.alert(data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}

function changeModifyOp(ch){
  var op=document.getElementById('modifyop');
  if (ch) op.value="delete";
  else op.value="modify";
}

//初始化编辑管理员的模态框数据
function getModifyAdminData(aid){
  var $ab=$('#adminModifyButton').button('loading');  //设定按钮的加载状态
  xpost("post","api/admin.php",{'aid':aid,'op':'get'},"json",
    function(data){
      //success
      if(data.code>0){
        //toastr.success('数据获取成功，显示到界面。');
        
        //下面处理，将数据显示到界面中
        
        var form1=document.getElementById('adminModifyForm');

        //重置信息
        form1.reset();
        document.getElementById('avataImage').src="";
        //重置结束

        //给表单元素赋值
        var inputs=form1.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) 
        {
          if (inputs[i].name=='a_account') inputs[i].value=data.data.a_account;
          if (inputs[i].name=='aid')       inputs[i].value=aid;
          if (inputs[i].name=='op')        inputs[i].value="modify";
          if (inputs[i].name=='a_name')    inputs[i].value=data.data.a_name;
          if (inputs[i].name=='a_memo')    inputs[i].value=data.data.a_memo;
          if (inputs[i].name=='a_type')
          {
            if (inputs[i].value==data.data.a_type) inputs[i].checked=true;
            else  inputs[i].checked=false;
          } 

          if (inputs[i].name=='a_status')
          {
            if (inputs[i].value==data.data.a_status) inputs[i].checked=true;
            else  inputs[i].checked=false;
          } 
        }
        if (data.data.a_avata!='') document.getElementById('avataImage').src=document.getElementById('avataImage').dataset.url+data.data.a_avata;
        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);

      }else{
        bootbox.alert("获取数据错误，无法继续！"+data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}

//添加网站栏目
function channelAdd()  //管理员添加提交
{
  var $ab=$('#channelAddButton').button('loading');  //设定按钮的加载状态
  
  xpostForm("api/channel.php","channelAddForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('栏目添加成功，重新加载页面。');
        setTimeout(function () { $ab.button('reset'); },1000);
        channelAddForm.reset();
        setTimeout(function () {location.reload();},1000);
      }else{
        bootbox.alert(data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}



//初始化编辑栏目的模态框数据
function getModifyChannelData(chid){
  var $ab=$('#channelModifyButton').button('loading');  //设定按钮的加载状态
  xpost("post","api/channel.php",{'chid':chid,'op':'get'},"json",
    function(data){
      //success
      if(data.code>0){
        //toastr.success('数据获取成功，显示到界面。');
        
        //下面处理，将数据显示到界面中
        
        var form1=document.getElementById('channelModifyForm');

        //重置信息
        form1.reset();
        //重置结束

        //给表单元素赋值
        var inputs=form1.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) 
        {
          if (inputs[i].name=='ch_name')       inputs[i].value=data.data.ch_name;
          if (inputs[i].name=='chid')          inputs[i].value=chid;
          if (inputs[i].name=='op')            inputs[i].value="modify";
          if (inputs[i].name=='ch_order')      inputs[i].value=data.data.ch_order;
          if (inputs[i].name=='ch_memo')       inputs[i].value=data.data.ch_memo;
          if (inputs[i].name=='ch_ext')        inputs[i].value=data.data.ch_ext;
          if (inputs[i].name=='ch_audit')
          {
            if (inputs[i].value==data.data.ch_audit) inputs[i].checked=true;
            else  inputs[i].checked=false;
          } 
        }

        var selects=form1.getElementsByTagName("select");
        if (selects[0].name=='ch_father_id')
        {
            for(var j=0;j<selects[0].length;j++)
            {
              if (selects[0].options[j].value==data.data.ch_father_id) selects[0].options[j].selected=true;
              else  selects[0].options[j].selected=false;
            }
        }

        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);

      }else{
        bootbox.alert("获取数据错误，无法继续！"+data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}



//修改网站栏目资料
function channelModify()  //管理员编辑和删除
{
  if (document.getElementById('modifyop').value=='delete')
  {
    //询问是否真的要删除操作
    bootbox.confirm({ 
      message: "你确定要删除此网站栏目么？删除将无法恢复！如果栏目下有资料或子栏目将无法删除。",
      callback: function(result){ 
        if (result) channelModifySub();
      }
    })
  }else channelModifySub();

  function channelModifySub()
  {
    var $ab=$('#channelModifyButton').button('loading');  //设定按钮的加载状态
    
    xpostForm("api/channel.php","channelModifyForm",
      function(data){
        //success
        if(data.code>0){
          toastr.success('网站栏目资料编辑成功，重新加载页面。');
          setTimeout(function () { $ab.button('reset'); },1000);
          channelModifyForm.reset();
          setTimeout(function () {location.reload();},1000);
        }else{
          bootbox.alert(data.msg, function(){
            setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
          });
        }
      },
      function(XMLHttpRequest,textStatus,errorThrown){
        //error
        bootbox.alert("接口调用错误，操作失败！", function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    );
  }

  return false;
}

function checkAll(cname)  //反向选择checkbox
{
  var inputs=document.getElementsByTagName("input");
  for (var i = 0; i < inputs.length; i++) 
  {
    if (inputs[i].name==cname)
    {
      if (inputs[i].checked==true) inputs[i].checked=false;
      else inputs[i].checked=true;
    }
  }
}


function getAuditAdminData(aid)  //获取权限资料
{
  var $ab=$('#adminAuditButton').button('loading');  //设定按钮的加载状态
  xpost("post","api/admin.php",{'aid':aid,'op':'getAudits'},"json",
    function(data){
      //success
      if(data.code>0){
        //toastr.success('数据获取成功，显示到界面。');
        
        //下面处理，将数据显示到界面中
        
        var form1=document.getElementById('adminAuditForm');

        //重置信息
        form1.reset();
        //重置结束

        //判断某个v是否存在于某个字串str中
        function isExists(str,v){
          if (str=='') return false;
          var a=str.split(",");
          for(var i=0;i<a.length;i++){
            if (a[i]==v) return true;
          }
          return false;
        }

        //给表单元素赋值
        var inputs=form1.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) 
        {
          if (inputs[i].name=='oneself'){
            if (data.data.a_audits.oneself==1) inputs[i].checked=true;
            else  inputs[i].checked=false;
          }
          if (inputs[i].name=='aid')  inputs[i].value=aid;

          if (inputs[i].name=='chid_a[]'){
            if (data.data.a_type==3){
              inputs[i].disabled=true;
              document.getElementById("allAudit").onclick=function (){alert('非审核员无法设定审核权限！');};
            }else{
              inputs[i].disabled=false;
              document.getElementById("allAudit").onclick=function (){checkAll('chid_a[]');};
              if (isExists(data.data.a_audits.a,inputs[i].value)) inputs[i].checked=true;
            }
          }

          if (inputs[i].name=='chid_r[]'){
            if (isExists(data.data.a_audits.r,inputs[i].value)) inputs[i].checked=true;
          }

          if (inputs[i].name=='chid_w[]'){
            if (isExists(data.data.a_audits.w,inputs[i].value)) inputs[i].checked=true;
          }

          if (inputs[i].name=='chid_d[]'){
            if (isExists(data.data.a_audits.d,inputs[i].value)) inputs[i].checked=true;
          }
        }

        document.getElementById("show_a_account").innerHTML=data.data.a_account+" ("+data.data.a_name+")";
        document.getElementById("show_a_type").innerHTML=data.data.a_type_name;

        //如果是普通管理员可以操作”只允许操作自己数据“，但是审核操作将被隐藏。如果是审核员”只允许操作自己的数据“没有，审核项可以操作。
        if (data.data.a_type==3 || data.data.a_type==2){
          document.getElementById('opMyData').style.display="block";
        }else{
          document.getElementById('opMyData').style.display="none";
        }

        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);

      }else{
        bootbox.alert("获取数据错误，无法继续！"+data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );

  return false;
}


function ck(id){
  document.getElementById('chidr'+id).click();
  document.getElementById('chidw'+id).click();
  document.getElementById('chidd'+id).click();
}



//添加网站栏目资料
function contentAdd()
{
  var $ab=$('#contentAddButton').button('loading');  //设定按钮的加载状态
  editor.sync(); //编辑器必须要同步数据后，才能正常获取数据。
  xpostForm("api/content.php","contentAddForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('资料添加成功，重新加载页面。');
        setTimeout(function () { $ab.button('reset'); },1000);
        document.getElementById('resetButton').click();
        setTimeout(function () {location.reload();},1000);
      }else{
        alert(data.msg);
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
    }
  );

  return false;
}

//编辑网站栏目资料
function contentModify()
{
  var $ab=$('#contentModifyButton').button('loading');  //设定按钮的加载状态
  editor2.sync(); //编辑器必须要同步数据后，才能正常获取数据。
  xpostForm("api/content.php","contentModifyForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('资料编辑成功，重新加载页面。');
        setTimeout(function () { $ab.button('reset'); },1000);
        //document.getElementById('resetButton2').click();
        setTimeout(function () {location.reload();},1000);
      }else{
        alert(data.msg);
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
    }
  );

  return false;
}

function contentDelete(cid,title)  //资料删除 but是当前按钮
{
  
  bootbox.confirm("确认要删除 \""+title+"\" 么？删除将无法恢复。",function(result){
    if (result){

      xpost("post","api/content.php",{'cid':cid,'op':'delete'},"json",
        function(data){
          //success
          if(data.code>0){
            toastr.success('删除成功，重新加载页面。');
            setTimeout(function () {location.reload();},1000);
          }else{
            bootbox.alert("接口调用错误，操作失败！"+data.msg, function(){});
          }
        },
        function(XMLHttpRequest,textStatus,errorThrown){
          //error
          bootbox.alert("接口调用错误，操作失败！", function(){});
        }
      );

    }

  });

  return true;
}

//添加附件
function addAttachments(){
  document.getElementById('attachments').innerHTML=document.getElementById('attachments').innerHTML+document.getElementById('attachment_sample').innerHTML;
}

function resetAttachments(){
  document.getElementById('attachments').innerHTML='';
}

//添加附件
function addAttachments2(){
  document.getElementById('attachments2').innerHTML=document.getElementById('attachments2').innerHTML+document.getElementById('attachment_sample').innerHTML;
}

function resetAttachments2(){
  document.getElementById('attachments2').innerHTML='';
}


//初始化编辑资料的模态框数据
function getModifyContentData(cid){
  var $ab=$('#contentModifyButton').button('loading');  //设定按钮的加载状态
  var form1=document.getElementById('contentModifyForm');
  //var reset1=document.getElementById('resetButton2');
  var attachmentsOld=document.getElementById('attachmentsOld');

  //重置信息
  document.getElementById('c_pic_image').src="";
  document.getElementById('downloadPic').href="#";
  attachmentsOld.innerHTML='';
  //reset1.click();
  //重置结束

  xpost("post","api/content.php",{'cid':cid,'op':'get'},"json",
    function(data){
      //success
      if(data.code>0){
        
        //给表单元素赋值
        var inputs=form1.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) 
        {
          if (inputs[i].name=='c_title')   inputs[i].value=data.data.c_title;
          if (inputs[i].name=='cid')       inputs[i].value=cid;
          if (inputs[i].name=='op')        inputs[i].value="modify";
          if (inputs[i].name=='c_title2')  inputs[i].value=data.data.c_title2;
          if (inputs[i].name=='c_author')  inputs[i].value=data.data.c_author;
          if (inputs[i].name=='c_source')  inputs[i].value=data.data.c_source;
          if (inputs[i].name=='c_order')   inputs[i].value=data.data.c_order;
          if (inputs[i].name=='c_pic_url') inputs[i].value=data.data.c_pic_url;
        }

        var selects=form1.getElementsByTagName("select");
        if (selects[0].name=='c_level')
        {
            for(var j=0;j<selects[0].length;j++)
            {
              if (selects[0].options[j].value==data.data.c_level) selects[0].options[j].selected=true;
              else  selects[0].options[j].selected=false;
            }
        }

        var textareas=form1.getElementsByTagName("textarea");
        for (var i = 0; i < textareas.length; i++) 
        {
          if (textareas[i].name=='c_summary')   textareas[i].value=htmlDecode(data.data.c_summary);
          if (textareas[i].name=='c_detail2') {
            //textareas[i].value=data.data.c_detail;
            editor2.html('');
            editor2.insertHtml(htmlDecode(data.data.c_detail));
          }
        }

        if (data.data.c_pic!='') {
          document.getElementById('c_pic_image').src=document.getElementById('c_pic_image').dataset.url+data.data.c_pic;
          document.getElementById('downloadPic').href=document.getElementById('c_pic_image').src;  
        }

        //历史附件处理
        for (var i = 0; i < data.data.attachments.length; i++) 
        {
          attachmentsOld.innerHTML=attachmentsOld.innerHTML+'<div id="attachment_'+data.data.attachments[i].caid+'">'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">附件文件 <a href="'+data.data.attachments[i].url+'" target="_blank"> 下载 </a></label> '+
          '    <input type="text" class="form-control" name="ca_title_old[]" value="'+data.data.attachments[i].ca_title+'">'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">附件相关网址</label> '+
          '    <input type="text" class="form-control" name="ca_detail_url_old[]" placeholder="用于图片，如：http://www.xxx.com" value="'+data.data.attachments[i].ca_detail_url+'">'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">排序 <span class="xcms-font-red">*</span></label> '+
          '    <input type="number" class="form-control" name="ca_order_old[]" value="'+data.data.attachments[i].ca_order+'" placeholder="排序值，从大到小排序，值必须大于0" title="排序值，从大到小排序，值必须大于0">'+
          '  </div>'+
          '  <div class="form-group col-xs-4">'+
          '    <label class="xcms-font-size13">附件详细介绍</label> '+
          '    <textarea class="form-control" rows="1" name="ca_content_old[]">'+data.data.attachments[i].ca_content+'</textarea>'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">操作 </label> '+
          '    <br /><input type="button" value="保存" class="btn btn-default" id="save_'+data.data.attachments[i].caid+'" onclick="attachmentModify('+data.data.attachments[i].caid+')" data-loading-text="保存中..." autocomplete=“off”>'+
          '    <input type="button" value="删除" id="delete_'+data.data.attachments[i].caid+'" onclick="attachmentDelete('+data.data.attachments[i].caid+');" class="btn btn-default xcms-margin-left10" data-loading-text="删除中..." autocomplete=“off”>'+
          '  </div>'+
          '</div>';
        }

        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);


      }else{
        alert("获取数据错误，无法继续！"+data.msg);
        setTimeout(function () { $ab.button('reset'); },200);
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);
    }
  );

  return false;
}


//初始化显示资料的模态框数据
function getShowContentData(cid){
  var $ab=$('#closeButtonShowContent').button('loading');  //设定按钮的加载状态
  var form1=document.getElementById('contentShowForm');
  var attachmentsOld=document.getElementById('attachmentsOld2');

  //重置信息
  form1.reset();
  document.getElementById('downloadPic2').href="#";
  document.getElementById('c_pic_image2').src="";
  //attachmentsOld.innerHTML="";
  //重置结束

  xpost("post","api/content.php",{'cid':cid,'op':'get'},"json",
    function(data){
      //success
      if(data.code>0){
        
        //给表单元素赋值
        var inputs=form1.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) 
        {
          if (inputs[i].name=='c_title')   inputs[i].value=data.data.c_title;
          if (inputs[i].name=='cid')       inputs[i].value=cid;
          if (inputs[i].name=='op')        inputs[i].value="show";
          if (inputs[i].name=='c_title2')  inputs[i].value=data.data.c_title2;
          if (inputs[i].name=='c_author')  inputs[i].value=data.data.c_author;
          if (inputs[i].name=='c_source')  inputs[i].value=data.data.c_source;
          if (inputs[i].name=='c_order')   inputs[i].value=data.data.c_order;
          if (inputs[i].name=='c_pic_url') inputs[i].value=data.data.c_pic_url;
        }

        var selects=form1.getElementsByTagName("select");
        if (selects[0].name=='c_level')
        {
            for(var j=0;j<selects[0].length;j++)
            {
              if (selects[0].options[j].value==data.data.c_level) selects[0].options[j].selected=true;
              else  selects[0].options[j].selected=false;
            }
        }

        var textareas=form1.getElementsByTagName("textarea");
        for (var i = 0; i < textareas.length; i++) 
        {
          if (textareas[i].name=='c_summary')   textareas[i].value=data.data.c_summary;
          if (textareas[i].name=='c_detail3') {
            //textareas[i].value=data.data.c_detail;
            editor3.html('');
            editor3.insertHtml(htmlDecode(data.data.c_detail));
          }
        }

        if (data.data.c_pic!='') {
          document.getElementById('c_pic_image2').src=document.getElementById('c_pic_image2').dataset.url+data.data.c_pic;
          document.getElementById('downloadPic2').href=document.getElementById('c_pic_image2').src;  
        }

        //历史附件处理
        attachmentsOld.innerHTML="";
        for (var i = 0; i < data.data.attachments.length; i++) 
        {
          attachmentsOld.innerHTML=attachmentsOld.innerHTML+'<div id="attachment_'+data.data.attachments[i].caid+'">'+
          '  <div class="form-group col-xs-3">'+
          '    <label class="xcms-font-size13">附件文件 <a href="'+data.data.attachments[i].url+'" target="_blank"> 下载 </a></label> '+
          '    <input type="text" class="form-control" name="ca_title_old[]" disabled="disabled" value="'+data.data.attachments[i].ca_title+'">'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">附件相关网址</label> '+
          '    <input type="text" class="form-control" name="ca_detail_url_old[]" disabled="disabled" placeholder="用于图片，如：http://www.xxx.com" value="'+data.data.attachments[i].ca_detail_url+'">'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">排序 <span class="xcms-font-red">*</span></label> '+
          '    <input type="number" class="form-control" name="ca_order_old[]" disabled="disabled" value="'+data.data.attachments[i].ca_order+'" placeholder="排序值，从大到小排序，值必须大于0" title="排序值，从大到小排序，值必须大于0">'+
          '  </div>'+
          '  <div class="form-group col-xs-5">'+
          '    <label class="xcms-font-size13">附件详细介绍</label> '+
          '    <textarea class="form-control" rows="1" name="ca_content_old[]" disabled="disabled">'+data.data.attachments[i].ca_content+'</textarea>'+
          '  </div>'+
          '</div>';
        }

        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);


      }else{
        alert("获取数据错误，无法继续！"+data.msg);
        setTimeout(function () { $ab.button('reset'); },200);
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);
    }
  );

  return false;
}

//内容页，显示历史审核记录
function getContentAuditHistory(cid){
  var $ab=$('#closeButtonAudit').button('loading');  //设定按钮的加载状态
  var auditHistory=document.getElementById('showAuditHistory2');
  var cTitle=document.getElementById('c_title_history');
  auditHistory.innerHTML='';
  cTitle.innerHTML='';

  xpost("post","api/content.php",{'cid':cid,'op':'getAuditHistory'},"json",
    function(data){
      //success
      if(data.code>0){

        cTitle.innerHTML=data.data.c_title;
        
        //输出历史审核记录
        for(i=0;i<data.data.audits.length;i++)
        {
          var a=data.data.audits[i];
          var au_result="审核不通过";
          var au_color="danger";
          var au_font_color="xcms-font-red";
          if (a.au_result==1){
            au_result="审核通过";
            au_color="primary";
            au_font_color="";
          }
          if (a.au_memo=='') a.au_memo='【未曾备注】';
          auditHistory.innerHTML=auditHistory.innerHTML+
          '<div class="panel box box-'+au_color+'">'+
          '  <div class="box-header with-border">'+
          '    <h4 class="box-title">'+
          '      <a data-toggle="collapse" data-parent="#accordion" href="#collapse'+i+'" class="collapsed '+au_font_color+'" aria-expanded="false">'+
          '        '+a.au_dt_add+' '+au_result+
          '      </a>'+
          '    </h4>'+
          '    <span class="xcms-font-size14 xcms_right">审核员姓名：'+a.a_name+'  |  审核员账号：'+a.a_account+'</span>'+
          '  </div>'+
          '  <div id="collapse'+i+'" class="panel-collapse collapse in" aria-expanded="false" style="">'+
          '    <div class="box-body">'+htmlDecode(a.au_memo)+'</div>'+
          '  </div>'+
          '</div>';
        }

        if (data.data.audits.length==0) auditHistory.innerHTML="<center><h4>本资料没有审核历史记录！</h4></center>";

        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);


      }else{
        alert("获取数据错误，无法继续！"+data.msg);
        setTimeout(function () { $ab.button('reset'); },200);
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);
    }
  );

  return false;


}

//获取要审核的content数据
function getAuditData(cid,ch_name){

  var $ab=$('#subButton').button('loading');  //设定按钮的加载状态
  var form1=document.getElementById('contentForm');
  var form2=document.getElementById('auditForm');
  var attachmentsOld=document.getElementById('attachmentsOld');
  var auditHistory=document.getElementById('showAuditHistory');

  //重置信息
  form1.reset();
  form2.reset();
  document.getElementById('downloadPic').href="#";
  document.getElementById('c_pic_image').src="";
  document.getElementById('ch_name').innerHTML=" | "+ch_name;
  document.getElementById('mainTab').click();  //将tab切换回第一个
  auditHistory.innerHTML='';
  editor.html('');
  editor2.html('');
  //attachmentsOld.innerHTML="";
  //重置结束

  xpost("post","api/audit.php",{'cid':cid,'op':'get'},"json",
    function(data){
      //success
      if(data.code>0){
        
        //给表单元素赋值
        var inputs=form1.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) 
        {
          if (inputs[i].name=='c_title')   inputs[i].value=data.data.c_title;
          if (inputs[i].name=='c_title2')  inputs[i].value=data.data.c_title2;
          if (inputs[i].name=='c_author')  inputs[i].value=data.data.c_author;
          if (inputs[i].name=='c_source')  inputs[i].value=data.data.c_source;
          if (inputs[i].name=='c_order')   inputs[i].value=data.data.c_order;
          if (inputs[i].name=='c_pic_url') inputs[i].value=data.data.c_pic_url;
        }

        document.getElementById('cid').value=cid;

        var selects=form1.getElementsByTagName("select");
        if (selects[0].name=='c_level')
        {
            for(var j=0;j<selects[0].length;j++)
            {
              if (selects[0].options[j].value==data.data.c_level) selects[0].options[j].selected=true;
              else  selects[0].options[j].selected=false;
            }
        }

        document.getElementById('c_summary').value=data.data.c_summary;
        editor.insertHtml(htmlDecode(data.data.c_detail));

        if (data.data.c_pic!='') {
          document.getElementById('c_pic_image').src=document.getElementById('c_pic_image').dataset.url+data.data.c_pic;
          document.getElementById('downloadPic').href=document.getElementById('c_pic_image').src;  
        }

        //历史附件处理
        attachmentsOld.innerHTML="";
        for (var i = 0; i < data.data.attachments.length; i++) 
        {
          attachmentsOld.innerHTML=attachmentsOld.innerHTML+'<div id="attachment_'+data.data.attachments[i].caid+'">'+
          '  <div class="form-group col-xs-3">'+
          '    <label class="xcms-font-size13">附件文件 <a href="'+data.data.attachments[i].url+'" target="_blank"> 下载 </a></label> '+
          '    <input type="text" class="form-control" name="ca_title_old[]" disabled="disabled" value="'+data.data.attachments[i].ca_title+'">'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">附件相关网址</label> '+
          '    <input type="text" class="form-control" name="ca_detail_url_old[]" disabled="disabled" placeholder="用于图片，如：http://www.xxx.com" value="'+data.data.attachments[i].ca_detail_url+'">'+
          '  </div>'+
          '  <div class="form-group col-xs-2">'+
          '    <label class="xcms-font-size13">排序 <span class="xcms-font-red">*</span></label> '+
          '    <input type="number" class="form-control" name="ca_order_old[]" disabled="disabled" value="'+data.data.attachments[i].ca_order+'" placeholder="排序值，从大到小排序，值必须大于0" title="排序值，从大到小排序，值必须大于0">'+
          '  </div>'+
          '  <div class="form-group col-xs-5">'+
          '    <label class="xcms-font-size13">附件详细介绍</label> '+
          '    <textarea class="form-control" rows="1" name="ca_content_old[]" disabled="disabled">'+data.data.attachments[i].ca_content+'</textarea>'+
          '  </div>'+
          '</div>';
        }

        //输出历史审核记录
        for(i=0;i<data.data.audits.length;i++)
        {
          var a=data.data.audits[i];
          var au_result="审核不通过";
          var au_color="danger";
          var au_font_color="xcms-font-red";
          if (a.au_result==1){
            au_result="审核通过";
            au_color="primary";
            au_font_color="";
          }
          if (a.au_memo=='') a.au_memo='【未曾备注】';
          auditHistory.innerHTML=auditHistory.innerHTML+
          '<div class="panel box box-'+au_color+'">'+
          '  <div class="box-header with-border">'+
          '    <h4 class="box-title">'+
          '      <a data-toggle="collapse" data-parent="#accordion" href="#collapse'+i+'" class="collapsed '+au_font_color+'" aria-expanded="false">'+
          '        '+a.au_dt_add+' '+au_result+
          '      </a>'+
          '    </h4>'+
          '    <span class="xcms-font-size14 xcms_right">审核员姓名：'+a.a_name+'  |  审核员账号：'+a.a_account+'</span>'+
          '  </div>'+
          '  <div id="collapse'+i+'" class="panel-collapse collapse in" aria-expanded="false" style="">'+
          '    <div class="box-body">'+htmlDecode(a.au_memo)+'</div>'+
          '  </div>'+
          '</div>';
        }

        //赋值结束

        setTimeout(function () { $ab.button('reset'); },200);


      }else{
        alert("获取数据错误，无法继续！"+data.msg);
        setTimeout(function () { $ab.button('reset'); },200);
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);
    }
  );

  return false;

}


//审核结果提交
function subAudit()
{
  var $ab=$('#subButton').button('loading');  //设定按钮的加载状态
  editor2.sync(); //编辑器必须要同步数据后，才能正常获取数据。
  xpostForm("api/audit.php","auditForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('审核完成，重新加载页面。');
        setTimeout(function () { $ab.button('reset'); },1000);
        document.getElementById('resetButton').click();
        setTimeout(function () {location.reload();},1000);
      }else{
        alert(data.msg);
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
    }
  );

  return false;
}


//编辑附件
function attachmentModify(caid){
  var data={};
  data.caid=caid;
  var att=document.getElementById('attachment_'+caid);
  var inputs=att.getElementsByTagName("input");
  for (var i = 0; i < inputs.length; i++) 
  {
    if (inputs[i].name=='ca_detail_url_old[]')  data.ca_detail_url=inputs[i].value;
    if (inputs[i].name=='ca_order_old[]')       data.ca_order=inputs[i].value;
    if (inputs[i].name=='ca_title_old[]')       data.ca_title=inputs[i].value;
  }
  var textareas=att.getElementsByTagName("textarea");
  for (var i = 0; i < textareas.length; i++) 
  {
    if (textareas[i].name=='ca_content_old[]')  data.ca_content=textareas[i].value;
  }

  data.op="attachmentModify";

  //console.log(data);

  var $ab=$('#save_'+caid).button('loading');  //设定按钮的加载状态
  xpost("post","api/content.php",data,"json",
    function(data){
      //success
      if(data.code>0){
        toastr.success('附件数据保存成功。');
        setTimeout(function () { $ab.button('reset'); },200);

      }else{
        alert("获取数据错误，无法继续！"+data.msg);
        setTimeout(function () { $ab.button('reset'); },200);
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      alert("接口调用错误，操作失败！");
      setTimeout(function () { $ab.button('reset'); },200);
    }
  );

  return true;
}

//删除附件
function attachmentDelete(caid){
  var data={};
  var att=document.getElementById('attachment_'+caid);

  data.caid=caid;
  data.op="attachmentDelete";

  //console.log(data);

  if (confirm("请确认是否要删除本附件，删除将无法恢复。"))
  {
    var $ab=$('#delete_'+caid).button('loading');  //设定按钮的加载状态
    xpost("post","api/content.php",data,"json",
      function(data){
        //success
        if(data.code>0){
          setTimeout(function () { $ab.button('reset'); },200);
          att.style.display="none";
          toastr.success('附件数据删除成功。');

        }else{
          alert("获取数据错误，无法继续！"+data.msg);
          setTimeout(function () { $ab.button('reset'); },200);
        }
      },
      function(XMLHttpRequest,textStatus,errorThrown){
        //error
        alert("接口调用错误，操作失败！");
        setTimeout(function () { $ab.button('reset'); },200);
      }
    );
  }

  return true;
}

//修改个人资料 - profile
function profileModify()
{
  var $ab=$('#profileSub').button('loading');  //设定按钮的加载状态
  
  xpostForm("api/profile.php","profileModifyForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('个人资料修改成功，重新加载页面。');
        setTimeout(function () {location.reload();},1000);
      }else{
        bootbox.alert(data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );
  return false;
}

//修改个人的密码 - profile
function modifyPassword()
{
  var $ab=$('#modifyPasswordButton').button('loading');  //设定按钮的加载状态
  
  xpostForm("api/profile.php","modifyPasswordForm",
    function(data){
      //success
      if(data.code>0){
        toastr.success('密码修改成功，重新加载页面。');
        setTimeout(function () {location.reload();},1000);
      }else{
        bootbox.alert(data.msg, function(){
          setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
        });
      }
    },
    function(XMLHttpRequest,textStatus,errorThrown){
      //error
      bootbox.alert("接口调用错误，操作失败！", function(){
        setTimeout(function () { $ab.button('reset'); },200);  //取消按钮的加载状态
      });
    }
  );
  return false;
}

