<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'ADD_RICHTEXT_TOOLBAR_BUTTON' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var array $settings */
/** @var string $wrap */
/** @var array|null $pre_style */
/** @var bool|null $is_editor */
if ( ! isset( $pre_style ) ) {
	$pre_style = [];
}
$style_content = '';
foreach ( $settings as $setting ) {
	if ( ! empty( $setting['styles'] ) || ! empty( $is_editor ) ) {
		$selector = ( ! empty( $wrap ) ? $wrap . ' ' : '' ) . $setting['selector'];
		if ( empty( $setting['styles'] ) ) {
			if ( ! empty( $pre_style ) ) {
				$style_content .= $selector . "{\n";
				foreach ( $pre_style as $style ) {
					$style_content .= "\t{$style}\n";
				}
				$style_content .= "}\n";
			}
		} else {
			$tmp = $selector;
			foreach ( $setting['styles'] as $pseudo => $styles ) {
				$selector = $tmp;
				if ( '' !== $pseudo ) {
					$selector .= ':' . $pseudo;
				}
				$style_content .= $selector . "{\n";
				foreach ( $pre_style as $style ) {
					$style_content .= "\t{$style}\n";
				}
				foreach ( $styles as $style ) {
					$style_content .= "\t{$style}\n";
				}
				$style_content .= "}\n";
			}
		}
	}
}
?>
<style>
	<?php $instance->h( $style_content, false, true, false ); ?>
</style>
