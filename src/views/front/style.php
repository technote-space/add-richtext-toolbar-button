<?php
/**
 * @version 1.0.13
 * @author technote-space
 * @since 1.0.0
 * @since 1.0.12 #74, #78
 * @since 1.0.13 #83
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $settings */
/** @var string $wrap */
/** @var array|null $pre_style */
/** @var bool|null $is_editor */
!isset($pre_style) and $pre_style = [];
?>
<style>
<?php foreach ($settings as $setting):?>
<?php if (!empty($setting['options']['styles']) || !empty($is_editor)):?>
<?php $selector = (!empty($wrap) ? $wrap . ' ' : '') . $setting['options']['selector'];?>
<?php if (empty($setting['options']['styles'])):?>
<?php $instance->h($selector);?> {
<?php foreach ($pre_style as $style):?>
    <?php $instance->h($style, false, true, false);?>

<?php endforeach;?>
}
<?php else:?>
<?php foreach ($setting['options']['styles'] as $pseudo => $styles):?>
<?php '' !== $pseudo and $selector .= ':' . $pseudo;?>
<?php $instance->h($selector);?> {
<?php foreach ($pre_style as $style):?>
    <?php $instance->h($style, false, true, false);?>

<?php endforeach;?>
<?php foreach ($styles as $style):?>
    <?php $instance->h($style, false, true, false);?>

<?php endforeach;?>
}
<?php endforeach;?>
<?php endif;?>
<?php endif;?>
<?php endforeach;?>
</style>