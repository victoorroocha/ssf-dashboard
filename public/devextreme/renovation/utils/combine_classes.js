/**
 * DevExtreme (renovation/utils/combine_classes.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.combineClasses = combineClasses;

function combineClasses(classesMap) {
    return Object.keys(classesMap).filter(function(p) {
        return classesMap[p]
    }).join(" ")
}
