/**
 * DevExtreme (renovation/ui/pager/utils/get_element_width.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.getElementMinWidth = getElementMinWidth;
exports.getElementStyle = getElementStyle;
exports.getElementWidth = getElementWidth;
var _get_computed_style = _interopRequireDefault(require("./get_computed_style"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}

function toNumber(attribute) {
    return attribute ? Number(attribute.replace("px", "")) : 0
}

function getElementStyle(name, element) {
    var computedStyle = (0, _get_computed_style.default)(element) || {};
    return toNumber(computedStyle[name])
}

function getElementWidth(element) {
    return getElementStyle("width", element)
}

function getElementMinWidth(element) {
    return getElementStyle("minWidth", element)
}
