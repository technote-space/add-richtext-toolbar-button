<?php
/**
 * WP_Framework_Admin Views Admin Help Setting
 *
 * @version 0.0.1
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $prefix */
?>

<ol>
    <li>
        <h4>ヘルプの抑制設定の追加</h4>
        configs/config.php に以下の設定を追加します。
        <pre>'suppress_setting_help_contents' => true</pre>
    </li>
</ol>
