/**
 * DevExtreme (integration/knockout/utils.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.getClosestNodeWithContext = void 0;
var _knockout = _interopRequireDefault(require("knockout"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}
var getClosestNodeWithContext = function getClosestNodeWithContext(node) {
    var context = _knockout.default.contextFor(node);
    if (!context && node.parentNode) {
        return getClosestNodeWithContext(node.parentNode)
    }
    return node
};
exports.getClosestNodeWithContext = getClosestNodeWithContext;
