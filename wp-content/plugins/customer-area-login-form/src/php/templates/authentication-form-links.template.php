<?php /** Template version: 1.0.0
 *
 * -= 1.0.0 =-
 * - First version
 */ ?>

<?php /** @var array $form_links */ ?>

<?php if ( !empty($form_links)) : ?>
    <div class="cuar-form-links text-center mt-lg">
        <?php
        $count = count($form_links);
        $i = 0;
        foreach ($form_links as $id => $link) : ?>

            <a href="<?php echo esc_attr($link['url']); ?>" class="cuar-<?php echo esc_attr($id); ?>-link"><?php echo $link['label']; ?></a>

            <?php if ($i < $count-1)
            {
                echo '<span class="cuar-separator">&bull;</span>';
                $i++;
            } ?>

        <?php endforeach; ?>
    </div>
<?php endif; ?>