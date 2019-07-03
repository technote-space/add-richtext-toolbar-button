=== Add RichText Toolbar Button ===
Contributors: technote0space
Tags: Gutenberg, rich text, Formatting, ツールバー, リッチテキスト
Requires at least: 5.0
Requires PHP: 5.6
Tested up to: 5.2
Stable tag: 1.2.0
Donate link: https://paypal.me/technote0space
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin makes it easy to add RichText toolbar button.

== Description ==

This plugin makes it easy to add RichText toolbar button.
[日本語の説明](https://technote.space/add-richtext-toolbar-button "Documentation in Japanese")
[GitHub (More details)](https://github.com/technote-space/add-richtext-toolbar-button)
[Issues (Reporting a new bug or feature request)](https://github.com/technote-space/add-richtext-toolbar-button/issues)

This plugin needs PHP5.6 or higher.

* You can add formatting buttons which have any tag name and class name to the rich text toolbar as many as you like.
* You can make some groups to add dropdown list instead of button.
* You can use not only inline text and background color panels but also inline font size panel.

== Installation ==

1. Upload the `add-richtext-toolbar-button` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently asked questions ==

== Screenshots ==

1. Gutenberg editor
2. Gutenberg editor (Toolbar buttons)
3. Gutenberg editor (Sidebar settings)
4. Add button setting
5. Button setting list
6. Dashboard

== Upgrade Notice ==

= 1.2.0 =
* Fontawesomeの追加を行わない設定を追加しました。[[デフォルトで false になるためアイコン等を使用している場合はダッシュボードから変更してください。]](color:red; font-weight:bold) [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/157)
* プリセットのアイコンのデザインを修正しました。 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/144)
* できるだけテーマスタイルを適用する機能を削除しました。 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/158)
* リファクタリング [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/156)
* 細かいバグの修正 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/141), [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/145)
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/164)

= 1.1.8 =
* 細かいバグの修正
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/139)

= 1.1.7 =
* リファクタリング
* 細かいバグの修正
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/135)

= 1.1.6 =
* ColorIndicatorを追加しました。 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/122)
* ツールバーボタンの挙動を修正しました。 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/123)
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/128)

= 1.1.5 =
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/121)

= 1.1.4 =
* 除外投稿タイプ設定の削除 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/118)
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/120)

= 1.1.3 =
* 細かいバグの修正
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/111)

= 1.1.2 =
* Gutenberg v5.3 への対応 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/101)
* テーマスタイルの読み込み改善 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/102)
* マルチサイトへの対応 [詳細](https://github.com/technote-space/add-richtext-toolbar-button/issues/109)
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/108)

= 1.1.1 =
* 細かいバグの修正
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/100)

= 1.1.0 =
* いくつかのパフォーマンスの改善を行いました [詳細](https://github.com/wp-content-framework/core/issues/138)
* [すべての差分](https://github.com/technote-space/add-richtext-toolbar-button/pull/99)

== Changelog ==

= 1.2.0 (2019/7/4) =
* Deleted: [Setting to apply theme style](https://github.com/technote-space/add-richtext-toolbar-button/issues/158)
* Added: [Setting not to add fontawesome](https://github.com/technote-space/add-richtext-toolbar-button/issues/157)
* Improved: [Some refactorings](https://github.com/technote-space/add-richtext-toolbar-button/issues/156)
* Fixed: [Behavior of remove all format button](https://github.com/technote-space/add-richtext-toolbar-button/issues/145)
* Improved: [Preset design](https://github.com/technote-space/add-richtext-toolbar-button/issues/144)
* Improved: [Use library](https://github.com/technote-space/add-richtext-toolbar-button/issues/140)

= 1.1.8 (2019/6/2) =
* Fixed: [Warning while insert preset data](https://github.com/technote-space/add-richtext-toolbar-button/issues/137)

= 1.1.7 (2019/6/2) =
* Improved: [Refactoring](https://github.com/technote-space/add-richtext-toolbar-button/issues/133)
* Fixed: [Uninstall behavior](https://github.com/wp-content-framework/common/issues/107)

= 1.1.6 (2019/5/28) =
* Improved: [Add `ColorIndicator`](https://github.com/technote-space/add-richtext-toolbar-button/issues/122)
* Fixed: [Behavior of Toolbar button](https://github.com/technote-space/add-richtext-toolbar-button/issues/123)
* Added: [Stripe design preset](https://github.com/technote-space/add-richtext-toolbar-button/issues/129)

= 1.1.5 (2019/4/28) =
* Improved: [Consider incorrect argument](https://github.com/wp-content-framework/admin/issues/58)

= 1.1.4 (2019/4/23) =
* Changed: [Remove `exclude post types` setting](https://github.com/technote-space/add-richtext-toolbar-button/issues/118)
* Changed: trivial changes
* Tested: against 5.2

= 1.1.3 (2019/3/25) =
* Fixed: minor bug fixes

= 1.1.2 (2019/3/25) =
* Fixed: [For Gutenberg v5.3](https://github.com/technote-space/add-richtext-toolbar-button/issues/101)
* Improved: [Load theme styles](https://github.com/technote-space/add-richtext-toolbar-button/issues/102)
* Fixed: [For multisite](https://github.com/technote-space/add-richtext-toolbar-button/issues/109)

= 1.1.1 (2019/3/18) =
* Fixed: [Consider exclude_from_search status](https://github.com/wp-content-framework/custom_post/issues/76)

= 1.1.0 (2019/3/18) =
* Improved: [Performance issues](https://github.com/wp-content-framework/core/issues/138)
* Improved: [Dashboard page](https://github.com/wp-content-framework/admin/issues/20)
* Tested: against 5.1.1

= 1.0.16 (2019/3/7) =
* Fixed: [Not load unrelated json file](https://github.com/wp-content-framework/custom_post/issues/60)
* Fixed: [Regular expression of admin message](https://github.com/wp-content-framework/admin/issues/16)
* Improved: [for Gutenberg v5.2](https://github.com/technote-space/add-richtext-toolbar-button/issues/95)

= 1.0.15 (2019/3/4) =

* Added: [Export and import settings](https://github.com/technote-space/add-richtext-toolbar-button/issues/17)
* Improved: [Cache control of gutenberg's js file](https://github.com/technote-space/add-richtext-toolbar-button/issues/91)

= 1.0.14 (2019/2/28) =

* Improved: [Apply theme style on setting preview](https://github.com/technote-space/add-richtext-toolbar-button/issues/82)
* Improved: [Class name check](https://github.com/technote-space/add-richtext-toolbar-button/issues/87)

= 1.0.13 (2019/2/25) =

* Fixed: [minor bug fix](https://github.com/technote-space/add-richtext-toolbar-button/issues/73)

= 1.0.12 (2019/2/25) =

* Improved: [Multiple class name](https://github.com/technote-space/add-richtext-toolbar-button/issues/74)
* Added: [Button to remove all formatting](https://github.com/technote-space/add-richtext-toolbar-button/issues/77)
* Improved: [Not apply default style if setting style is empty](https://github.com/technote-space/add-richtext-toolbar-button/issues/78)

= 1.0.11 (2019/2/24) =

* Fixed: [minor bug fix](https://github.com/technote-space/add-richtext-toolbar-button/issues/72)

= 1.0.10 (2019/2/23) =

* Fixed: undefined index notice

= 1.0.9 (2019/2/23) =

* Fixed: [Fatal error](https://github.com/technote-space/add-richtext-toolbar-button/issues/69)

= 1.0.8 (2019/2/22) =

* Tested: Against WordPress v5.1.0

= 1.0.7 (2019/2/22) =

* Improved: [Not allow to save tagName:`div`](https://github.com/technote-space/add-richtext-toolbar-button/issues/60)
* Added: [ContrastChecker](https://github.com/technote-space/add-richtext-toolbar-button/issues/61)

= 1.0.6 (2019/2/20) =

* Deleted: Debug output

= 1.0.5 (2019/2/20) =

* Improved: [Reflect the `disable-custom-font-sizes` setting](https://github.com/technote-space/add-richtext-toolbar-button/issues/54)
* Fixed: [Validity of toolbar button preset settings](https://github.com/technote-space/add-richtext-toolbar-button/issues/52)

= 1.0.4 (2019/2/20) =

* Improved: [Reflect the `disable-custom-colors` setting](https://github.com/technote-space/add-richtext-toolbar-button/issues/46)

= 1.0.3 (2019/2/20) =

* Added: [Setting to hide button while style is valid](https://github.com/technote-space/add-richtext-toolbar-button/issues/28)
* Added: [Multiple line preview](https://github.com/technote-space/add-richtext-toolbar-button/issues/29)
* Added: [Line height preset](https://github.com/technote-space/add-richtext-toolbar-button/issues/30)
* Added: [Warning box preset](https://github.com/technote-space/add-richtext-toolbar-button/issues/33)
* Improved: [Behavior of input tag name in Japanese](https://github.com/technote-space/add-richtext-toolbar-button/issues/31)
* Improved: [Behavior of sidebar](https://github.com/technote-space/add-richtext-toolbar-button/issues/32)
* Improved: [Add link to setting list page after update setting](https://github.com/wp-content-framework/custom_post/issues/35)
* Improved: [Setting list order](https://github.com/wp-content-framework/custom_post/issues/36)

= 1.0.2 (2019/2/19) =

* Fixed: [minor bug fix](https://github.com/technote-space/add-richtext-toolbar-button/issues/25)

= 1.0.0 (2019/2/19) =

* First release

