# Add RichText Toolbar Button

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=4.7.0](https://img.shields.io/badge/WordPress-%3E%3D4.7.0-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/banner-772x250.png)

Gutenberg エディタのツールバーに文章修飾用のボタンを追加するプラグインです。

## スクリーンショット
### ツールバー

![ツールバー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902150444.png)

### 設定追加

![設定追加](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170345.png)

### 設定一覧

![設定一覧](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902150436.png)

### ダッシュボード

![ダッシュボード](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170343.png)

## 要件
- PHP 5.6 以上
- WordPress 4.7.0 以上

## 導入手順
1. [ここから](https://github.com/technote-space/add-richtext-toolbar-button/archive/master.zip) ZIPをダウンロード
2. wp-content/plugins に展開
3. 管理画面から有効化

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

## 設定
### タグ名
**span** や **div** などのタグを指定します。  
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

### 除外投稿タイプ
このプラグインの機能を使用しない投稿タイプを指定します。

### 優先度
値が小さいほど優先順位が高くなります。  
* 一覧で上に表示される
* CSSで後ろに出力される

## ダッシュボード
### 有効かどうか
これを外すと全てのボタンが無効になります。

### 文字色ボタンが有効かどうか
文字色を変更できるボタンを追加するかどうかを指定します。

![文字色ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170357.png)

## 文字色ボタンのアイコン
文字色を変更できるボタンのアイコンを指定します。

### 背景色ボタンが有効かどうか
背景色を変更できるボタンを追加するかどうかを指定します。

![背景色ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170358.png)

## 背景色ボタンのアイコン
背景色を変更できるボタンのアイコンを指定します。

### デフォルトアイコン
設定でアイコンを指定しなかった場合に使用されるアイコンです。

### デフォルトグループ
設定でグループ名を指定しなかった場合に使用されるグループ名です。

### テスト用文章
設定画面や一覧でテスト表示用に使用される文章を指定します。

## Author
[GitHub (technote-space)](https://github.com/technote-space)  
[homepage](https://technote.space)

## プラグイン作成用フレームワーク
[WP Content Framework](https://github.com/wp-content-framework/core)
