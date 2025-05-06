/**
 * DevExtreme (ui/scheduler/workspaces/utils/month.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.getViewStartByOptions = void 0;
var _base = require("./base");
var getViewStartByOptions = function(startDate, currentDate, intervalCount, startViewDate) {
    if (!startDate) {
        return new Date(currentDate)
    } else {
        var _startDate = new Date(startViewDate);
        var validStartViewDate = new Date(startViewDate);
        var diff = _startDate.getTime() <= currentDate.getTime() ? 1 : -1;
        var endDate = new Date(new Date(validStartViewDate.setMonth(validStartViewDate.getMonth() + diff * intervalCount)));
        while (!(0, _base.isDateInRange)(currentDate, _startDate, endDate, diff)) {
            _startDate = new Date(endDate);
            if (diff > 0) {
                _startDate.setDate(1)
            }
            endDate = new Date(new Date(endDate.setMonth(endDate.getMonth() + diff * intervalCount)))
        }
        return diff > 0 ? _startDate : endDate
    }
};
exports.getViewStartByOptions = getViewStartByOptions;
