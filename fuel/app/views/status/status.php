<div class="status">
    <div class="status_content">
        <a href="<?php echo Uri::base(); ?>user/<?php echo $status['user_name']; ?>">
            <?php echo $status['user_name']; ?>
        </a>
        <?php echo $status['body']; ?>
    </div>
    <div>
        <a href="<?php echo Uri::base(); ?>user/<?php echo $status['user_name'];
        ?>/status/<?php echo $status['id']; ?>">
            <?php echo $status['created_at']; ?>
        </a>
    </div>
</div>
