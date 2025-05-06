/**
 * DevExtreme (ui/scheduler/workspaces/utils/week.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.getIntervalDuration = void 0;
var _date = _interopRequireDefault(require("../../../../core/utils/date"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}
var getIntervalDuration = function(intervalCount) {
    return 7 * _date.default.dateToMilliseconds("day") * intervalCount
};
exports.getIntervalDuration = getIntervalDuration;
