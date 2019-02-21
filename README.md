# Add RichText Toolbar Button

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=4.7.0](https://img.shields.io/badge/WordPress-%3E%3D4.7.0-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/banner-772x250.png)

Gutenberg エディタのツールバーに文章修飾用のボタンを追加するプラグインです。

[WordPress公式ディレクトリ](https://ja.wordpress.org/plugins/add-richtext-toolbar-button/)

## スクリーンショット
### ツールバー

![ツールバー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902150444.png)

### サイドバー

![サイドバー](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902181831.png)

### 設定追加

![設定追加](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170345.png)

### 設定一覧

![設定一覧](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902150436.png)

### ダッシュボード

![ダッシュボード](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902181859.png)

## 要件
- PHP 5.6 以上
- WordPress 4.7.0 以上

## 導入手順
1. 管理画面のプラグインから「新規追加」  
![手順1](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902182243.png)  
2. 「RichText」で検索し「今すぐインストール」を押下  
![手順2](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902182300.png)
3. 「有効化」  

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
![ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902181846.png)

### インラインテキスト設定の利用
1. 記事の投稿画面に移動（Gutenbergエディタ）
2. 修飾したい文字列を選択
3. 右側のサイドバーから適用する色やサイズを選択
![インラインテキスト設定](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902181842.png)
* サイドバーが見つからない場合  
右上の歯車マークを押してサイドバーを表示
![設定](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902181841.png)

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

### 除外投稿タイプ
このプラグインの機能を使用しない投稿タイプを指定します。

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

![文字色ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170357.png)

[インラインテキスト設定の利用](#インラインテキスト設定の利用) からは常に使用できるため、ツールバーにもボタンが必要な場合に有効にします。

### 文字色ボタンのアイコン
文字色を変更できるボタンのアイコンを指定します。

### 背景色ボタンが有効かどうか
背景色を変更できるボタンを追加するかどうかを指定します。

![背景色ボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902170358.png)

[インラインテキスト設定の利用](#インラインテキスト設定の利用) からは常に使用できるため、ツールバーにもボタンが必要な場合に有効にします。

### 背景色ボタンのアイコン
背景色を変更できるボタンのアイコンを指定します。

### 文字サイズボタンが有効かどうか
文字サイズを変更できるボタンを追加するかどうかを指定します。

![文字サイズボタン](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/.github/images/201902181852.png)

[インラインテキスト設定の利用](#インラインテキスト設定の利用) からは常に使用できるため、ツールバーにもボタンが必要な場合に有効にします。

### 文字サイズボタンのアイコン
文字サイズを変更できるボタンのアイコンを指定します。

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
