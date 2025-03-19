/**
 * DevExtreme (ui/scheduler/workspaces/utils/work_week.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.isDataOnWeekend = exports.getWeekendsCount = exports.getFirstViewDate = exports.getFirstDayOfWeek = void 0;
var _date = _interopRequireDefault(require("../../../../core/utils/date"));

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}
var MONDAY_INDEX = 1;
var SATURDAY_INDEX = 6;
var SUNDAY_INDEX = 0;
var isDataOnWeekend = function(date) {
    var day = date.getDay();
    return day === SATURDAY_INDEX || day === SUNDAY_INDEX
};
exports.isDataOnWeekend = isDataOnWeekend;
var getFirstDayOfWeek = function(firstDayOfWeekOption) {
    return firstDayOfWeekOption || MONDAY_INDEX
};
exports.getFirstDayOfWeek = getFirstDayOfWeek;
var getWeekendsCount = function(days) {
    return 2 * Math.floor(days / 7)
};
exports.getWeekendsCount = getWeekendsCount;
var getFirstViewDate = function(viewStart, firstDayOfWeek) {
    var firstViewDate = _date.default.getFirstWeekDate(viewStart, firstDayOfWeek);
    return _date.default.normalizeDateByWeek(firstViewDate, viewStart)
};
exports.getFirstViewDate = getFirstViewDate;
