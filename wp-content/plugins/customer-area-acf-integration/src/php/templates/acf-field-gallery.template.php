<?php /**
 * Template version: 5.0.0
 * Template zone: frontend
 *
 * = 5.0.0 =-
 *  Update template for ACF v5.8.3+
 *
 */ ?>

<?php /** @var array $field */ ?>

<?php
if (isset($field['value']))
{
    $field_value = $field['value'];
    if ($field_value === false || $field_value === null)
    {
        $field_value = [];
    }
    else if (!is_array($field_value))
    {
        $field_value = [$field_value];
    }

    $size = 'wpca-thumb';
    $acf_gallery_template = apply_filters('cuar/templates/acf-gallery-type', 'grid');

    if ($acf_gallery_template === 'grid')
    { ?>

        <div class="cuar-readonly-field-gallery cuar-readonly-field-gallery-type-grid">
            <div class="row clearfix">
                <?php foreach ($field_value as $image):
                    if (is_array($image))
                    {
                        ?>
                        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="panel">
                                <div class="panel-body text-center pn">
                                    <a href="<?php echo esc_url($image['url']); ?>" class="thumbnail br-n mn">
                                        <?php if ($image['type'] === 'image') { ?>
                                            <img src="<?php echo esc_url($image['sizes'][$size]); ?>"
                                                 width="<?php echo esc_attr($image['sizes'][$size . '-width']); ?>"
                                                 height="<?php echo esc_attr($image['sizes'][$size . '-height']); ?>"/>
                                        <?php } else { ?>
                                            <img src="<?php echo esc_url($image['icon']); ?>" class="p10"/>
                                        <?php } ?>
                                    </a>
                                </div>
                                <div class="panel-footer">
                                    <strong class="clearfix"><?php echo $image['title']; ?></strong>
                                    <?php if ($image['caption'] !== $image['title']) { ?>
                                        <span class="wp-caption"><?php echo $image['caption']; ?></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    else if (is_string($image))
                    { ?>
                        <div class="panel col-sm-12">
                            <div class="panel-body">
                                <?php
                                $a = getimagesize($image);
                                $image_type = $a[2];

                                if (in_array($image_type,
                                    [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]))
                                { ?>
                                    <img src="<?php echo esc_url($image); ?>"/>
                                    <?php
                                }
                                else
                                { ?>
                                    <a href="<?php echo esc_url($image); ?>"><?php echo $image; ?></a>
                                <?php }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    else if (is_int($image))
                    { ?>
                        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="panel">
                                <div class="panel-body text-center pn">
                                    <?php
                                    $image_full = $image;
                                    $image = wp_get_attachment_image_src($image, $size);
                                    if (isset($image[0]))
                                    {
                                        $a = getimagesize($image[0]);
                                        $image_type = $a[2];

                                        if (in_array($image_type,
                                            [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]))
                                        {
                                            $image_full = wp_get_attachment_image_src($image_full, 'full');
                                            if (isset($image_full[0]))
                                            { ?>
                                                <a href="<?php echo esc_url($image_full[0]); ?>"
                                                   class="thumbnail br-n mn">
                                                    <img src="<?php echo esc_url($image[0]); ?>"/>
                                                </a>
                                            <?php }
                                        }
                                        else
                                        { ?>
                                            <a href="<?php echo esc_url($image[0]); ?>"><?php echo $image[0]; ?></a>
                                        <?php }
                                    }
                                    else
                                    {
                                        _e('No attachment image has been found', 'cuaracf');
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                endforeach; ?>
            </div>

        </div>
        <?php
    }
    else if ($acf_gallery_template === 'slider')
    {
        ?>

        <div class="cuar-readonly-field-gallery cuar-readonly-field-gallery-type-slider slick-slider">
            <?php
            $acf_slider_return_type = '';
            foreach ($field_value as $image):
                if (is_array($image))
                {
                    $acf_slider_return_type = 'array';
                    ?>
                    <div class="slick-slide p10">
                        <div class="panel">
                            <div class="panel-body text-center pn">
                                <a href="<?php echo esc_url($image['url']); ?>" class="thumbnail br-n mn">
                                    <?php if ($image['type'] === 'image') { ?>
                                        <img src="<?php echo esc_url($image['sizes'][$size]); ?>"
                                             width="<?php echo esc_attr($image['sizes'][$size . '-width']); ?>"
                                             height="<?php echo esc_attr($image['sizes'][$size . '-height']); ?>"/>
                                    <?php } else { ?>
                                        <img src="<?php echo esc_url($image['icon']); ?>" class="p10"/>
                                    <?php } ?>
                                </a>
                            </div>
                            <div class="panel-footer">
                                <strong class="clearfix"><?php echo $image['title']; ?></strong>
                                <?php if ($image['caption'] !== $image['title']) { ?>
                                    <span class="wp-caption"><?php echo $image['caption']; ?></span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                else if (is_string($image))
                {
                    $acf_slider_return_type = 'string'; ?>
                    <div class="slick-slide">
                        <div class="panel">
                            <div class="panel-body">
                                <?php
                                $a = getimagesize($image);
                                $image_type = $a[2];

                                if (in_array($image_type,
                                    [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]))
                                { ?>
                                    <img src="<?php echo esc_url($image); ?>"/>
                                    <?php
                                }
                                else
                                { ?>
                                    <a href="<?php echo esc_url($image); ?>"><?php echo $image; ?></a>
                                <?php }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                else if (is_int($image))
                {
                    $acf_slider_return_type = 'int'; ?>
                    <div class="slick-slide">
                        <div class="panel">
                            <div class="panel-body">
                                <?php
                                $image = wp_get_attachment_image_src($image, $size);
                                if (isset($image[0]))
                                {
                                    $a = getimagesize($image[0]);
                                    $image_type = $a[2];

                                    if (in_array($image_type,
                                        [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_BMP]))
                                    { ?>
                                        <img src="<?php echo esc_url($image[0]); ?>"/>
                                        <?php
                                    }
                                    else
                                    { ?>
                                        <a href="<?php echo esc_url($image[0]); ?>"><?php echo $image[0]; ?></a>
                                    <?php }
                                }
                                else
                                {
                                    _e('No attachment image has been found', 'cuaracf');
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            endforeach; ?>
        </div>

        <script type="text/javascript">
            <!--
            (function ($) {
                "use strict";
                $(document).ready(function ($) {
                    <?php if($acf_slider_return_type === 'array' || $acf_slider_return_type === 'int') { ?>
                    $('.cuar-readonly-field-gallery').slick({
                        dots          : true,
                        infinite      : false,
                        speed         : 300,
                        slidesToShow  : 4,
                        slidesToScroll: 4,
                        responsive    : [
                            {
                                breakpoint: 1024,
                                settings  : {
                                    slidesToShow  : 3,
                                    slidesToScroll: 3,
                                    infinite      : true,
                                    dots          : true
                                }
                            }, {
                                breakpoint: 600,
                                settings  : {
                                    slidesToShow  : 2,
                                    slidesToScroll: 2
                                }
                            }, {
                                breakpoint: 480,
                                settings  : {
                                    slidesToShow  : 1,
                                    slidesToScroll: 1
                                }
                            }
                        ]
                    });
                    <?php } else if ($acf_slider_return_type === 'string') { ?>
                    $('.cuar-readonly-field-gallery').slick({
                        dots          : true,
                        infinite      : false,
                        speed         : 300,
                        slidesToShow  : 1,
                        slidesToScroll: 1
                    });
                    <?php } ?>
                });
            })(jQuery);
            //-->
        </script>
        <?php

    }
}
?>


