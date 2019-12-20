# Add RichText Toolbar Button

[![CI Status](https://github.com/technote-space/add-richtext-toolbar-button/workflows/CI/badge.svg)](https://github.com/technote-space/add-richtext-toolbar-button/actions)
[![Build Status](https://travis-ci.com/technote-space/add-richtext-toolbar-button.svg?branch=master)](https://travis-ci.com/technote-space/add-richtext-toolbar-button)
[![codecov](https://codecov.io/gh/technote-space/add-richtext-toolbar-button/branch/master/graph/badge.svg)](https://codecov.io/gh/technote-space/add-richtext-toolbar-button)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-space/add-richtext-toolbar-button/badge)](https://www.codefactor.io/repository/github/technote-space/add-richtext-toolbar-button)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=5.0](https://img.shields.io/badge/WordPress-%3E%3D5.0-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/banner-772x250.png)

*Read this in other languages: [English](README.md), [日本語](README.ja.md).*

Gutenberg エディタのツールバーに文章修飾用のボタンを追加するプラグインです。

[デモ](https://technote-space.github.io/add-richtext-toolbar-button)

[最新バージョン](https://github.com/technote-space/add-richtext-toolbar-button/releases/latest/download/release.zip)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [スクリーンショット](#%E3%82%B9%E3%82%AF%E3%83%AA%E3%83%BC%E3%83%B3%E3%82%B7%E3%83%A7%E3%83%83%E3%83%88)
  - [動作](#%E5%8B%95%E4%BD%9C)
  - [ツールバー](#%E3%83%84%E3%83%BC%E3%83%AB%E3%83%90%E3%83%BC)
  - [サイドバー](#%E3%82%B5%E3%82%A4%E3%83%89%E3%83%90%E3%83%BC)
  - [設定追加](#%E8%A8%AD%E5%AE%9A%E8%BF%BD%E5%8A%A0)
  - [設定一覧](#%E8%A8%AD%E5%AE%9A%E4%B8%80%E8%A6%A7)
  - [ダッシュボード](#%E3%83%80%E3%83%83%E3%82%B7%E3%83%A5%E3%83%9C%E3%83%BC%E3%83%89)
- [要件](#%E8%A6%81%E4%BB%B6)
- [導入手順](#%E5%B0%8E%E5%85%A5%E6%89%8B%E9%A0%86)
- [使用方法](#%E4%BD%BF%E7%94%A8%E6%96%B9%E6%B3%95)
  - [設定の追加](#%E8%A8%AD%E5%AE%9A%E3%81%AE%E8%BF%BD%E5%8A%A0)
  - [ボタンの利用](#%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%AE%E5%88%A9%E7%94%A8)
  - [インラインテキスト設定の利用](#%E3%82%A4%E3%83%B3%E3%83%A9%E3%82%A4%E3%83%B3%E3%83%86%E3%82%AD%E3%82%B9%E3%83%88%E8%A8%AD%E5%AE%9A%E3%81%AE%E5%88%A9%E7%94%A8)
- [設定](#%E8%A8%AD%E5%AE%9A)
  - [タグ名](#%E3%82%BF%E3%82%B0%E5%90%8D)
  - [クラス名](#%E3%82%AF%E3%83%A9%E3%82%B9%E5%90%8D)
  - [グループ名](#%E3%82%B0%E3%83%AB%E3%83%BC%E3%83%97%E5%90%8D)
  - [アイコン](#%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B3)
  - [スタイル](#%E3%82%B9%E3%82%BF%E3%82%A4%E3%83%AB)
    - [プリセット](#%E3%83%97%E3%83%AA%E3%82%BB%E3%83%83%E3%83%88)
  - [ツールバーボタンが有効かどうか](#%E3%83%84%E3%83%BC%E3%83%AB%E3%83%90%E3%83%BC%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%8C%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [優先度](#%E5%84%AA%E5%85%88%E5%BA%A6)
- [ダッシュボード](#%E3%83%80%E3%83%83%E3%82%B7%E3%83%A5%E3%83%9C%E3%83%BC%E3%83%89-1)
  - [有効かどうか](#%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [文字色ボタンが有効かどうか](#%E6%96%87%E5%AD%97%E8%89%B2%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%8C%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [文字色ボタンのアイコン](#%E6%96%87%E5%AD%97%E8%89%B2%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%AE%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B3)
  - [背景色ボタンが有効かどうか](#%E8%83%8C%E6%99%AF%E8%89%B2%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%8C%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [背景色ボタンのアイコン](#%E8%83%8C%E6%99%AF%E8%89%B2%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%AE%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B3)
  - [文字サイズボタンが有効かどうか](#%E6%96%87%E5%AD%97%E3%82%B5%E3%82%A4%E3%82%BA%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%8C%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [文字サイズボタンのアイコン](#%E6%96%87%E5%AD%97%E3%82%B5%E3%82%A4%E3%82%BA%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%AE%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B3)
  - [全てのフォーマットを外すボタンが有効かどうか](#%E5%85%A8%E3%81%A6%E3%81%AE%E3%83%95%E3%82%A9%E3%83%BC%E3%83%9E%E3%83%83%E3%83%88%E3%82%92%E5%A4%96%E3%81%99%E3%83%9C%E3%82%BF%E3%83%B3%E3%81%8C%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [Fontawesomeが有効かどうか](#fontawesome%E3%81%8C%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [デフォルトアイコン](#%E3%83%87%E3%83%95%E3%82%A9%E3%83%AB%E3%83%88%E3%82%A2%E3%82%A4%E3%82%B3%E3%83%B3)
  - [デフォルトグループ](#%E3%83%87%E3%83%95%E3%82%A9%E3%83%AB%E3%83%88%E3%82%B0%E3%83%AB%E3%83%BC%E3%83%97)
  - [テスト用文章](#%E3%83%86%E3%82%B9%E3%83%88%E7%94%A8%E6%96%87%E7%AB%A0)
- [カラーパレットやフォントサイズ一覧の変更](#%E3%82%AB%E3%83%A9%E3%83%BC%E3%83%91%E3%83%AC%E3%83%83%E3%83%88%E3%82%84%E3%83%95%E3%82%A9%E3%83%B3%E3%83%88%E3%82%B5%E3%82%A4%E3%82%BA%E4%B8%80%E8%A6%A7%E3%81%AE%E5%A4%89%E6%9B%B4)
- [プラグイン独自のフィルタ](#%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E7%8B%AC%E8%87%AA%E3%81%AE%E3%83%95%E3%82%A3%E3%83%AB%E3%82%BF)
- [Dependency](#dependency)
- [Author](#author)
- [プラグイン作成用フレームワーク](#%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E4%BD%9C%E6%88%90%E7%94%A8%E3%83%95%E3%83%AC%E3%83%BC%E3%83%A0%E3%83%AF%E3%83%BC%E3%82%AF)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## スクリーンショット
### 動作

![動作](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201903070308.gif)

### ツールバー

![ツールバー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902150444.png)

### サイドバー

![サイドバー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181831.png)

### 設定追加

![設定追加](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902170345.png)

### 設定一覧

![設定一覧](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902150436.png)

### ダッシュボード

![ダッシュボード](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181859.png)

## 要件
- PHP 5.6 以上
- WordPress 5.0 以上

## 導入手順
1. 最新版をGitHubからダウンロード  
[release.zip](https://github.com/technote-space/add-richtext-toolbar-button/releases/latest/download/release.zip)
2. 「プラグインのアップロード」からインストール
![install](https://raw.githubusercontent.com/technote-space/screenshots/master/misc/install-wp-plugin.png)
3. プラグインを有効化

## 使用方法
### 設定の追加
1. 管理画面左メニューから「Add RichText Toolbar Button」⇒「設定管理」に移動
2. 「新規追加」
3. 設定名やスタイル等の情報を入力
4. 「公開」ボタンを押下

### ボタンの利用
1. 記事の投稿画面に移動（Gutenbergエディタ）
2. 修飾したい文字列を選択
3. 適用するボタンを押下
![ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181846.png)

### インラインテキスト設定の利用
1. 記事の投稿画面に移動（Gutenbergエディタ）
2. 修飾したい文字列を選択
3. 右側のサイドバーから適用する色やサイズを選択
![インラインテキスト設定](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181842.png)
* サイドバーが見つからない場合  
右上の歯車マークを押してサイドバーを表示
![設定](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181841.png)

## 設定
### タグ名
**span** や **cite** などのタグを指定します。  
何も指定しない場合は **span** が使用されます。

### クラス名
クラス名を指定します。  
他のボタンで使用されているものを指定するとエラーになります。  
何も指定しない場合は **投稿IDを使用した被らないもの** が使用されます。

### グループ名
同じグループ名のボタンが複数あった場合にドロップダウンでまとまります。  
多くのボタンを追加する場合に横に広がりすぎるのを防ぐことができます。  
何も指定しない場合はドロップダウンになりません。

### アイコン
ボタンに表示するアイコンを指定します。  
**dashicon** や URL等の画像を指定することができます。

### スタイル
適用するデザインを指定します。  
セレクタを除いた部分を記述します。  
```
プロパティ: 値
```
**before** や **after** のような疑似クラスは次のルールで記述します。  
```
[疑似クラス] プロパティ: 値
```  
例：
```
display: block;
padding: 10px;
background: #f0f9ff;
border: 1px solid #acf;
[before] font-family: "Font Awesome 5 Free";
[before] content: "\f06a";
[before] font-size: 1.2em;
[before] font-weight: 900;
[before] padding-right: .2em;
[before] margin-right: .2em;
[before] color: #9cf;
[before] border-right: 1px solid #acf;
```
#### プリセット
いくつかのデザインパターンを用意しています。  
ボタンを押すことでスタイルのテキストエリアに挿入されます。

### ツールバーボタンが有効かどうか
Gutenbergエディタのツールバーに表示するかどうかを指定します。  
ゴミ箱に移動したり削除したりするとデザイン自体が無効になってしまいますが、  
この設定によって『デザインは適用される』けれども『ツールバーには表示されない』が実現可能です。  
新しくボタンを追加して過去に追加したものが不要になった場合に、デザインの適用を残しておきたい場合に有効です。

### 優先度
値が小さいほど優先順位が高くなります。  
* 一覧で上に表示される (**ツールバーボタンが有効かどうか** が無効の場合を除く)
* CSSで後ろに出力される
* ツールバーで前のほうに配置される

## ダッシュボード
### 有効かどうか
これを外すと全ての機能が無効になります。

### 文字色ボタンが有効かどうか
文字色を変更できるボタンを追加するかどうかを指定します。

![文字色ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902170357.png)

[インラインテキスト設定の利用](#インラインテキスト設定の利用) からは常に使用できるため、ツールバーにもボタンが必要な場合に有効にします。

### 文字色ボタンのアイコン
文字色を変更できるボタンのアイコンを指定します。

### 背景色ボタンが有効かどうか
背景色を変更できるボタンを追加するかどうかを指定します。

![背景色ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902170358.png)

[インラインテキスト設定の利用](#インラインテキスト設定の利用) からは常に使用できるため、ツールバーにもボタンが必要な場合に有効にします。

### 背景色ボタンのアイコン
背景色を変更できるボタンのアイコンを指定します。

### 文字サイズボタンが有効かどうか
文字サイズを変更できるボタンを追加するかどうかを指定します。

![文字サイズボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181852.png)

[インラインテキスト設定の利用](#インラインテキスト設定の利用) からは常に使用できるため、ツールバーにもボタンが必要な場合に有効にします。

### 文字サイズボタンのアイコン
文字サイズを変更できるボタンのアイコンを指定します。

### 全てのフォーマットを外すボタンが有効かどうか
サイドバーに全てのフォーマットを外すボタンを追加するかどうかを指定します。

### Fontawesomeが有効かどうか
Fontawesomeを読み込むかどうかを指定します。  
v1.2 以降はこの設定をONにしない限り読みこみません。  
またOFFの場合はFontawesomeが必要な[プリセット](#プリセット)は表示されません。

### デフォルトアイコン
設定でアイコンを指定しなかった場合に使用されるアイコンです。

### デフォルトグループ
設定でグループ名を指定しなかった場合に使用されるグループ名です。

### テスト用文章
設定画面や一覧でテスト表示用に使用される文章を指定します。

## カラーパレットやフォントサイズ一覧の変更
add_theme_support の設定で変更することが可能です。  
参考ページ：  
[WordPress：Gutenbergで文字サイズと色設定（文字色・背景色）をカスタマイズする方法](https://www.nxworld.net/wordpress/wp-gutenberg-custom-font-sizes-and-color-palette.html)

## プラグイン独自のフィルタ
いくつかの値は上書きすることが可能です。  

|フィルタ名|説明|
|---|---|
|artb/get_setting_presets|設定のプリセット|
|artb/default_class_name|デフォルトのクラス名|
|artb/get_groups|グループ一覧|
|artb/get_preset|スタイルのプリセット|
|artb/editor_wrap_selector|エディタ用にラップするセレクタ|
|artb/cache_front_file_base_name|フロント用 出力ファイル名|
|artb/cache_editor_file_base_name|エディタ(ツールボタン)用 出力ファイル名|
|artb/pre_style_for_front|フロント用 自動適用スタイル|
|artb/pre_style_for_editor|エディタ(ツールボタン)用 自動適用スタイル|

例： クラス名が設定されていない場合にデフォルトで設定されるクラス名の上書き
```
add_filter( 'artb/default_class_name', function ( $value, $post_id ) {
	return 'test-' . $post_id;
}, 10, 2 );
```

## Dependency
[Register Grouped Format Type](https://github.com/technote-space/register-grouped-format-type)

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

## プラグイン作成用フレームワーク
[WP Content Framework](https://github.com/wp-content-framework/core)
