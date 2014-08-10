window.onload=function(){
	$(function() {
	    var index=0;
	    var nodes =false;
	    $("#file_upload").uploadify({
			          buttonText : '上传图片',
			       buttonImage : start_extend_path+'/Org/Uploadify/buttom2.png',
	                   			swf : start_extend_path+'/Org/Uploadify/uploadify.swf',
	                	 uploader : request_url,
	           		fileSizeLimit : 2048000,
	          	queueSizeLimit : 10,
			              	 width : 98,
			                height : 28,
	        onUploadSuccess  : function(file, data, response) {
	        	var data = eval("("+data+")");
		          if(!nodes){
		              var node = $('#file_upload-queue .uploadify-queue-item');
		              nodes = [];
		              node.each(function(){
		                  nodes.push(this);
		              });
		          }
		          var oBox = document.getElementById('box');
		          var oDiv = document.createElement('div');
		          oDiv.innerHTML = '<img src="'+root_path+data.path+'" style="display:block;float:left;width:150px;height:200px;"><input type="hidden" name="'+inputName+'" value="'+data.path+'">';
		          oBox.appendChild(oDiv);
		          oDiv.appendChild(nodes[index]);
		          oDiv.className = 'list';          
		          index++;
	        }

	    });
	}); 
}