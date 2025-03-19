/**
 * DevExtreme (renovation/preact_wrapper/utils.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.removeDifferentElements = void 0;
var _iterator = require("../../core/utils/iterator");
var removeDifferentElements = function($children, $newChildren) {
    (0, _iterator.each)($newChildren, function(__, element) {
        var hasComponent = false;
        (0, _iterator.each)($children, function(_, oldElement) {
            if (element === oldElement) {
                hasComponent = true
            }
        });
        if (!hasComponent && element.parentNode) {
            element.parentNode.removeChild(element)
        }
    })
};
exports.removeDifferentElements = removeDifferentElements;
