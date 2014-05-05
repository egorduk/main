<?php echo $this->doctype(); ?>
<html>
 <head>
  <?php $this->headTitle()->setSeparator(' | ');?>
  <?php echo $this->headTitle('Тест'); ?>
  <?php 
	$this->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
	$this->headMeta()->appendName('keywords', 'демо, тест, ZF, фрэймворк, PHP');
	$this->headMeta()->appendName('description', 'Демо-сайт. Разработано с использованием Zend Framework');
	$this->headMeta()->appendName('copyright', '© 2009 2Developers.Net');
  ?>
  <?php echo $this->headMeta() ?>
  <?php echo $this->headLink()->appendStylesheet($this->baseUrl . 'design/css/main_style.css');?>
  <?php $this->headStyle(); ?>
  <?php echo $this->headScript(); ?>
</head>
<body>
    <div id="main">
        <div id="content">
			<?php echo $this->layout()->content; ?>
        </div>
    </div>
	<!--<div id="footer">
		<p> © 2013 </p>
	</div>-->
</body>
</html>

