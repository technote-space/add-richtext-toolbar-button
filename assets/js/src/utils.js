import { Helpers, Components, RichText } from './wrapper';

const { getRemoveFormatButton } = RichText;
const { getTranslator }         = Helpers;
const { Icon }                  = Components;

const PREFIX            = 'arbt--';
const getName           = name => PREFIX + name;
const getIcon           = params => icon => Icon({ icon: icon, defaultIcon: params.defaultIcon });
const INSPECTOR_GROUP   = getName('inspector');
const TOOL_BUTTON_GROUP = getName('tool-button');

/**
 * @param {{isValidContrastChecker, isValidRemoveFormatting}} params params
 * @returns {array} group setting
 */
export const getDefaultButtonGroupSetting = params => {
  const translate = getTranslator(params);
  return [
    INSPECTOR_GROUP,
    {
      toolbarGroup: INSPECTOR_GROUP,
      inspectorSettings: {
        title: translate('Inline Text Settings'),
        initialOpen: true,
      },
      useContrastChecker: params.isValidContrastChecker,
      additionalInspectors: params.isValidRemoveFormatting ? [getRemoveFormatButton(translate('Remove All formatting'))] : [],
    },
  ];
};

/**
 * @param {{defaultButtons:{name, title, icon, style, className, isValid}, defaultIcon}} params params
 * @param {string} key key
 * @returns {array} color button setting
 */
export const getColorButtonSetting = (params, key) => {
  const setting = params.defaultButtons[ key ];
  return [
    getName(setting.name),
    setting.title,
    getIcon(params)(setting.icon),
    setting.style,
    {
      group: INSPECTOR_GROUP,
      className: setting.className,
      createDisabled: !setting.isValid,
    },
  ];
};

/**
 * @param {{defaultButtons:{name, title, icon, className, isValid}, defaultIcon}} params params
 * @param {string} key key
 * @returns {array} font size button setting
 */
export const getFontSizeButtonSetting = (params, key) => {
  const setting = params.defaultButtons[ key ];
  return [
    getName(setting.name),
    setting.title,
    getIcon(params)(setting.icon),
    {
      group: INSPECTOR_GROUP,
      className: setting.className,
      createDisabled: !setting.isValid,
    },
  ];
};

/**
 * @param {{settings:{name, title, className, tagName, isValid, groupName, icon}[], defaultIcon}} params params
 * @returns {{settings: Array, groups}} settings
 */
export const getSettings = params => {
  const groups   = {};
  const settings = [];
  Object.keys(params.settings).forEach(key => {
    const setting = params.settings[ key ];
    const group   = getName('item--' + setting.groupName);
    if (!(group in groups)) {
      groups[ group ] = {
        toolbarGroup: TOOL_BUTTON_GROUP,
        icon: getIcon(params)(setting.icon),
        label: setting.groupName,
        className: 'arbt-button',
        menuClassName: 'arbt-menu',
      };
    }
    settings.push([
      group,
      setting.name,
      getIcon(params)(setting.icon),
      {
        title: setting.title,
        className: setting.className,
        tagName: setting.tagName,
        createDisabled: !setting.isValid,
      },
    ]);
  });
  return { groups, settings };
};
