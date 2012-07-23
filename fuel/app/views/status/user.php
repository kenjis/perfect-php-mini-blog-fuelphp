<h2><?php echo $user['user_name']; ?></h2>

<?php if (!is_null($following)): ?>
<?php if ($following): ?>
<p>フォローしています</p>
<?php else: ?>
<form action="<?php echo Uri::base(); ?>follow" method="post">
    <input type="hidden" name="<?php echo Config::get('security.csrf_token_key');?>" value="<?php echo Security::fetch_token();?>" />
    <input type="hidden" name="following_name" value="<?php echo $user['user_name']; ?>" />

    <input type="submit" value="フォローする" />
</form>
<?php endif; ?>
<?php endif; ?>

<div id="statuses">
    <?php foreach ($statuses as $status): ?>
    <?php echo View::forge('status/status', array('status' => $status)); ?>
    <?php endforeach; ?>
</div>
