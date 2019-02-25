=== Add RichText Toolbar Button ===
Contributors: technote0space
Tags: Gutenberg, rich text, Formatting, ツールバー, リッチテキスト
Requires at least: 5.0.3
Tested up to: 5.1.0
Requires PHP: 5.6
Stable tag: 1.0.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin makes it easy to add RichText toolbar button.

== Description ==

This plugin makes it easy to add RichText toolbar button.
[日本語の説明](https://technote.space/add-richtext-toolbar-button "Documentation in Japanese")
[GitHub (More details)](https://github.com/technote-space/add-richtext-toolbar-button)
[Issues (Reporting a new bug or feature request)](https://github.com/technote-space/add-richtext-toolbar-button/issues)

This plugin needs PHP5.6 or higher.

* Any tag name and class name formatting button can be added to the rich text toolbar.
* You can make some groups to add dropdown list instead of button.
* You can use not only inline text and background color panel but also inline font size panel.

== Installation ==

1. Upload the `add-richtext-toolbar-button` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently asked questions ==

== Screenshots ==

1. Gutenberg editor (Toolbar buttons)
2. Gutenberg editor (Sidebar settings)
3. Add button setting
4. Button setting list
5. Dashboard

== Upgrade Notice ==

= 1.0.12 =
* 複数のクラスの設定に対応しました（スペース区切りで複数のクラス名を指定できます）
* 　すでに投稿で使用している場合に クラス名 や タグ名 の設定を変更しても投稿に付与したクラス名などは変わりません。
* 　その影響で『エディタ』では 他のフォーマット(文字色など)が適用されているような挙動 になったりします。
* 　不要になった設定は『ツールバーボタンが有効かどうか』を外してそのまま残しておくことをお勧めします。

= 1.0.9 =
* Fatal error が起こる場合があるのを修正しました。

= 1.0.7 =
* `ContrastChecker` の導入(使用するには『詳細設定』から設定値を `true` にする必要があります)

= 1.0.5 =
* `disable-custom-font-sizes` の設定が反映されるように改善

= 1.0.4 =
* `disable-custom-colors` の設定が反映されるように改善

= 1.0.3 =
* ツールバーボタンが有効かどうかの設定追加(デザインは適用させたままボタンを消すことが可能)
* サイドバーの動作改善

== Changelog ==

= 1.0.12 (2019/2/24) =

* Improved: [Multiple class name](https://github.com/technote-space/add-richtext-toolbar-button/issues/74)

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

