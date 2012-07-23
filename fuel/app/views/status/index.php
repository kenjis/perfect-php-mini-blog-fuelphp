<?php //$this->setLayoutVar('title', 'ホーム') ?>

<h2>ホーム</h2>

<form action="<?php echo Uri::base(); ?>status/post" method="post">
    <input type="hidden" name="<?php echo Config::get('security.csrf_token_key');?>" value="<?php echo Security::fetch_token();?>" />

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php
    $data = array(
        'errors' => $errors,
    );
    echo View::forge('errors', $data);
    ?>
    <?php endif; ?>

    <textarea name="body" rows="2" cols="60"><?php echo $body; ?></textarea>
    <p>
        <input type="submit" value="発言" />
    </p>
</form>

<div id="statuses">
    <?php foreach ($statuses as $status): ?>
    <?php echo View::forge('status/status', array('status' => $status)); ?>
    <?php endforeach; ?>
</div>
