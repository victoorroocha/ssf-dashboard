/**
 * DevExtreme (core/utils/window.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.hasWindow = exports.hasProperty = exports.getWindow = exports.getNavigator = exports.getCurrentScreenFactor = exports.defaultScreenFactorFunc = void 0;
var _dom_adapter = _interopRequireDefault(require("../dom_adapter"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}
var hasWindow = function() {
    return "undefined" !== typeof window
};
exports.hasWindow = hasWindow;
var windowObject = hasWindow() && window;
if (!windowObject) {
    windowObject = {};
    windowObject.window = windowObject
}
var getWindow = function() {
    return windowObject
};
exports.getWindow = getWindow;
var hasProperty = function(prop) {
    return hasWindow() && prop in windowObject
};
exports.hasProperty = hasProperty;
var defaultScreenFactorFunc = function(width) {
    if (width < 768) {
        return "xs"
    } else {
        if (width < 992) {
            return "sm"
        } else {
            if (width < 1200) {
                return "md"
            } else {
                return "lg"
            }
        }
    }
};
exports.defaultScreenFactorFunc = defaultScreenFactorFunc;
var getCurrentScreenFactor = function(screenFactorCallback) {
    var screenFactorFunc = screenFactorCallback || defaultScreenFactorFunc;
    var windowWidth = _dom_adapter.default.getDocumentElement().clientWidth;
    return screenFactorFunc(windowWidth)
};
exports.getCurrentScreenFactor = getCurrentScreenFactor;
var getNavigator = function() {
    return hasWindow() ? windowObject.navigator : {
        userAgent: ""
    }
};
exports.getNavigator = getNavigator;
