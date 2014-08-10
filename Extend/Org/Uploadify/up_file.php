	
	<link rel="stylesheet" type="text/css" href="<?php echo __ROOT__;?>/uploadify/uploadify.css">
	<script type="text/javascript" src="<?php echo __ROOT__;?>/uploadify/jquery.uploadify-3.1.min.js"></script>		
	<script type="text/javascript">
		$(function() {
    		$("#file_upload").uploadify({
				"buttonText" : "上传文件",
				"buttonImage" : "<?php echo __ROOT__;?>/uploadify/asd_07.png",
       	 		"swf"             : "<?php echo __ROOT__;?>/uploadify/uploadify.swf",
        		"uploader"        : "<?php echo __ROOT__;?>/uploadify/uploadify.php",
				"width"    : 100,
				"height":30,
        		"fileSizeLimit" : "2000",
        		"queueSizeLimit" : 10,				
       	 		"onUploadSuccess" : function(file, data, response) {
					var data = eval("("+data+")");
					if(data.state === true){
						upSuccess(data.path,file.name);	
					}
        		},
        		'onUploadError':function(file, errorCode, errorMsg, errorString){
        			alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
        		} 		
    		})
		})
		
		</script>
		<input type="file" name="file_upload" id="file_upload" />