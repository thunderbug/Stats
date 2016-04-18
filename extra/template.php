<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $this->title?></title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="css/layout.css">
	</head>
	<body>
		<div id="container">
			<?php 
				include 'html/header.php';
				include 'html/navbar.html';	
			?>
			<div id="content">
				<h1><?php echo $this->subtitle?></h1>
			</div>
			<?php include 'html/footer.html';?>
		</div>
	</body>
</html>
