<?php /** Template version: 1.0.0 */ ?>

<?php
$user_count = count($users);
$max_shown = 50;
if ($user_count > 0)
{
    $i = 0;
    $tokens = array();
    foreach ($users as $u)
    {
        $tokens[] = sprintf('<a href="%4$s" class="cuar-user" title="%3$s - %1$s">%2$s</a>',
            $u->user_login, $u->display_name, $u->ID, admin_url('user-edit.php?user_id=' . $u->ID));
        if ($i > $max_shown) break;
        ++$i;
    }
}
?>

<div class="cuar-sg-preview">
    <?php if ($user_count == 0) : ?>
        <p><?php _e('No users match the criterias for this group', 'cuarsg'); ?></p>
    <?php else : ?>
        <p>
            <?php printf(__('%d user(s) match the criterias for this group. ', 'cuarsg'), $user_count); ?>
        </p>

        <div class="cuar-user-list">
            <?php
            echo implode(', ', $tokens);
            if ($user_count > $max_shown)
            {
                printf(__('... and %1$s more', 'cuarsg'), $user_count - $max_shown);
            }
            ?>
        </div>
    <?php endif; ?>
</div>