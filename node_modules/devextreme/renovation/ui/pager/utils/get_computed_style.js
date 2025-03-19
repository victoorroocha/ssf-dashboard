/**
 * DevExtreme (renovation/ui/pager/utils/get_computed_style.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.default = getElementComputedStyle;

function getElementComputedStyle(el) {
    return el ? window.getComputedStyle && window.getComputedStyle(el) : null
}
module.exports = exports.default;
