<?php
/**
 * WP_Framework_View Views Include Img
 *
 * @version 0.0.6
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $args */
/** @var string $id */
/** @var string $class */
/** @var string $href */
/** @var string $target */
/** @var string $contents */
/** @var array $attributes */
empty( $attributes ) and $attributes = [];
isset( $id ) and $attributes['id'] = $id;
isset( $class ) and $attributes['class'] = $class;
$attributes['href'] = $href;
isset( $target ) and $attributes['target'] = $target;
isset( $target ) and '_blank' === $target and $attributes['rel'] = 'noopener noreferrer';
! isset( $contents ) and $contents = '';
?>
<a <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> >
	<?php $instance->h( $contents, false, true, false ) ?>
</a>