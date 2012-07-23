<h2>アカウント</h2>
<p>
    ユーザID:
    <a href="<?php echo Uri::base() ?>user/<?php echo $user['user_name']; ?>">
        <strong><?php echo $user['user_name']; ?></strong>
    </a>
</p>

<ul>
    <li>
        <a href="<?php echo Uri::base(); ?>">ホーム</a>
    </li>
    <li>
        <a href="<?php echo Uri::base(); ?>account/signout">ログアウト</a>
    </li>
</ul>

<h3>フォロー中</h3>

<?php if (count($followings) > 0): ?>
<ul>
    <?php foreach ($followings as $following): ?>
    <li>
        <a href="<?php echo Uri::base(); ?>user/<?php echo $following['user_name']; ?>">
            <?php echo $following['user_name']; ?>
        </a>
    </li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
