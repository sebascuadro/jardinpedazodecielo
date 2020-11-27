<?php /** Template version: 1.0.0 */

/*
 * Variables that will be replaced when sending the notification
 *
 * %%MAIL_CONTENT%% - content of the email
 */

/** @var string $header_image_url */
/** @var string $main_heading */
/** @var string $email_content */

?><?php

if (!empty($main_heading)) {
    echo $main_heading . "\n\n\n";
}

echo $email_content;
echo "\n\n";
echo get_bloginfo( 'name' ) . ' - ' . home_url();