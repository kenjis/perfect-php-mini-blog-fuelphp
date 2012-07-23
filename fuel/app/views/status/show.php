<?php //$this->setLayoutVar('title', $status['user_name']) ?>

<?php echo View::forge('status/status', array('status' => $status)); ?>
