/**
 * DevExtreme (core/element.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.getPublicElement = getPublicElement;
exports.setPublicElementWrapper = setPublicElementWrapper;
var strategy = function(element) {
    return element && element.get(0)
};

function getPublicElement(element) {
    return strategy(element)
}

function setPublicElementWrapper(newStrategy) {
    strategy = newStrategy
}
