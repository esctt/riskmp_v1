<html>
<head>
<title>Upload Form</title>
</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('upload/do_upload');?>

<input title="" type="file" name="userfile" onchange="this.form.submit()"/>

<br /><br />

<!-- <input type="submit" value="upload" /> -->

</form>

</body>
</html>