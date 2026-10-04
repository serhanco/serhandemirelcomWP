<?php
/**
 * Document head and site navigation.
 *
 * @package serhandemirel
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'selection:bg-fuchsia-500 selection:text-white relative bg-[#050505]' ); ?>>
<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/nav' ); ?>
