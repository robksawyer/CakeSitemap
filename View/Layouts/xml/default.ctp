<?php 
echo $this->Html->meta('Content-type' => 'text/xml'); 
echo $this->Html->meta('charset' => 'utf-8');
echo $this->fetch('content');
?>