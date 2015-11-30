<html>
<head>
<title>Upload Form</title>
</head>
<body>

<h3>Your file was successfully uploaded!</h3>

<ul>
<?php foreach ($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<p><?php echo anchor('upload', 'Upload Another File!'); ?></p>
<img src="<?php echo base_url() . 'assets/images/uploads/' . $upload_data['file_name'];?>" width='500' height='300'/>
<p><?php echo $upload_data['file_name']; ?></p>

</body>
</html>