# Add RichText Toolbar Button

[![CI Status](https://github.com/technote-space/add-richtext-toolbar-button/workflows/CI/badge.svg)](https://github.com/technote-space/add-richtext-toolbar-button/actions)
[![Build Status](https://travis-ci.com/technote-space/add-richtext-toolbar-button.svg?branch=master)](https://travis-ci.com/technote-space/add-richtext-toolbar-button)
[![codecov](https://codecov.io/gh/technote-space/add-richtext-toolbar-button/branch/master/graph/badge.svg)](https://codecov.io/gh/technote-space/add-richtext-toolbar-button)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-space/add-richtext-toolbar-button/badge)](https://www.codefactor.io/repository/github/technote-space/add-richtext-toolbar-button)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=5.0](https://img.shields.io/badge/WordPress-%3E%3D5.0-brightgreen.svg)](https://wordpress.org/)

![Banner](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/banner-772x250.png)

*Read this in other languages: [English](README.md), [日本語](README.ja.md).*

This plugin makes it easy to add RichText toolbar button.

[Demonstration](https://technote-space.github.io/add-richtext-toolbar-button)

[Latest version](https://github.com/technote-space/add-richtext-toolbar-button/releases/latest/download/release.zip)

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**

- [Screenshots](#screenshots)
  - [Behavior](#behavior)
  - [Toolbar](#toolbar)
  - [Sidebar](#sidebar)
  - [Add setting](#add-setting)
  - [Setting list](#setting-list)
  - [Dashboard](#dashboard)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
  - [Add setting](#add-setting-1)
  - [Use button](#use-button)
  - [Use `Inline Text Settings`](#use-inline-text-settings)
- [Setting](#setting)
  - [Tag name](#tag-name)
  - [Class name](#class-name)
  - [Group name](#group-name)
  - [Icon](#icon)
  - [Style](#style)
    - [Preset](#preset)
  - [Validity of toolbar button](#validity-of-toolbar-button)
  - [Priority](#priority)
- [Dashboard](#dashboard-1)
  - [Validity](#validity)
  - [Validity of font color button](#validity-of-font-color-button)
  - [Font color button icon](#font-color-button-icon)
  - [Validity of background color button](#validity-of-background-color-button)
  - [Background color button icon](#background-color-button-icon)
  - [Validity of font size button](#validity-of-font-size-button)
  - [Font size button icon](#font-size-button-icon)
  - [Validity of remove formatting button](#validity-of-remove-formatting-button)
  - [Validity of fontawesome](#validity-of-fontawesome)
  - [Default icon](#default-icon)
  - [Default group](#default-group)
  - [Test phrase](#test-phrase)
- [Dependency](#dependency)
- [Author](#author)
- [Plugin framework](#plugin-framework)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Screenshots
### Behavior

![Behavior](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201903070308.gif)

### Toolbar

![Toolbar](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902150444.png)

### Sidebar

![Sidebar](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181831.png)

### Add setting

![Add setting](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902170345.png)

### Setting list

![Setting list](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902150436.png)

### Dashboard

![Dashboard](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181859.png)

## Requirements
- \>= PHP 5.6
- \>= WordPress 5.0

## Installation
1. Download latest version  
[release.zip](https://github.com/technote-space/add-richtext-toolbar-button/releases/latest/download/release.zip)
1. Install plugin
![install](https://raw.githubusercontent.com/technote-space/screenshots/master/misc/install-wp-plugin.png)
1. Activate plugin

## Usage
### Add setting
1. Go to "All Settings" from left side menu "Add RichText Toolbar Button" of admin page.
1. "Add New"
1. Input settings like name or styles.
1. Press "Publish" button.

### Use button
1. Go to editor page.
1. Select sentence which you want to add style.
1. Apply button.
![Button](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181846.png)

### Use `Inline Text Settings`
1. Go to editor page.
1. Select sentence which you want to add style.
1. Apply color and font size from sidebar on the right.
![Inline Text Settings](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181842.png)

## Setting
### Tag name
Specify the tag name.
You can use HTML tags like **span** or **cite**.  
If you do not input anything, **span** is used.

### Class name
Specify the class name.
An error will occur if you specify something that is used by another.  
If you do not input anything, **Unique name using post ID** is used.

### Group name
Specify the group name.  
If there are multiple buttons with the same group name, they will be gathered by DropDown.  

### Icon
Specify the icon.  
You can use **dashicon** or URL.

### Style
Specify the design to be applied in the following format.  
```
property: value
```
Pseudo classes like **before** and **after** are described by the following rules.
```
[Pseudo-classes] property: value
```  
ex.
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
#### Preset
Several design patterns are available.

### Validity of toolbar button
Specify whether to display on the toolbar of the Gutenberg editor.  
If you move setting to the trash or delete setting, the design itself will be invalid.  
This setting is useful when you want to keep the design applied.

### Priority
The lower the value, the higher the priority.

## Dashboard
### Validity
When this setting is off, all functions are disabled.

### Validity of font color button
Specify whether to add a button that can change the text color.

![font color button](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902170357.png)

### Font color button icon
Specify the icon of `font color button`

### Validity of background color button
Specify whether to add a button that can change the background color.

![background color button](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902170358.png)

### Background color button icon
Specify the icon of `background color button`

### Validity of font size button
Specify whether to add a button that can change the font size.

![font size button](https://raw.githubusercontent.com/technote-space/add-richtext-toolbar-button/images/assets/201902181852.png)

### Font size button icon
Specify the icon of `font size button`

### Validity of remove formatting button
Specify whether to add a button to remove all formatting in the sidebar.

### Validity of fontawesome
Specify whether to load Fontawesome.

### Default icon
Specify the default icon.

### Default group
Specify the default group.

### Test phrase
Specify the test phrase.

## Dependency
[Register Grouped Format Type](https://github.com/technote-space/register-grouped-format-type)

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

## Plugin framework
[WP Content Framework](https://github.com/wp-content-framework/core)
