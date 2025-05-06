/**
 * DevExtreme (ui/pivot_grid/utils/get_scrollbar_width.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.getScrollbarWidth = getScrollbarWidth;

function getScrollbarWidth(containerElement) {
    return containerElement.offsetWidth - containerElement.clientWidth
}
