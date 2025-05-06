/**
 * DevExtreme (renovation/ui/scheduler/workspaces/utils.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.isVerticalGroupOrientation = exports.getKeyByGroup = exports.getKeyByDateAndGroup = exports.getIsGroupedAllDayPanel = exports.getGroupCellClasses = exports.addHeightToStyle = void 0;
var _combine_classes = require("../../../utils/combine_classes");
var _consts = require("../consts");

function _typeof(obj) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
        return typeof obj
    } : function(obj) {
        return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj
    }, _typeof(obj)
}

function ownKeys(object, enumerableOnly) {
    var keys = Object.keys(object);
    if (Object.getOwnPropertySymbols) {
        var symbols = Object.getOwnPropertySymbols(object);
        enumerableOnly && (symbols = symbols.filter(function(sym) {
            return Object.getOwnPropertyDescriptor(object, sym).enumerable
        })), keys.push.apply(keys, symbols)
    }
    return keys
}

function _objectSpread(target) {
    for (var i = 1; i < arguments.length; i++) {
        var source = null != arguments[i] ? arguments[i] : {};
        i % 2 ? ownKeys(Object(source), !0).forEach(function(key) {
            _defineProperty(target, key, source[key])
        }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function(key) {
            Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key))
        })
    }
    return target
}

function _defineProperty(obj, key, value) {
    key = _toPropertyKey(key);
    if (key in obj) {
        Object.defineProperty(obj, key, {
            value: value,
            enumerable: true,
            configurable: true,
            writable: true
        })
    } else {
        obj[key] = value
    }
    return obj
}

function _toPropertyKey(arg) {
    var key = _toPrimitive(arg, "string");
    return "symbol" === _typeof(key) ? key : String(key)
}

function _toPrimitive(input, hint) {
    if ("object" !== _typeof(input) || null === input) {
        return input
    }
    var prim = input[Symbol.toPrimitive];
    if (void 0 !== prim) {
        var res = prim.call(input, hint || "default");
        if ("object" !== _typeof(res)) {
            return res
        }
        throw new TypeError("@@toPrimitive must return a primitive value.")
    }
    return ("string" === hint ? String : Number)(input)
}
var getKeyByDateAndGroup = function(date, groupIndex) {
    var key = date.getTime();
    if (!groupIndex) {
        return key.toString()
    }
    return (key + groupIndex).toString()
};
exports.getKeyByDateAndGroup = getKeyByDateAndGroup;
var getKeyByGroup = function(groupIndex) {
    return groupIndex.toString()
};
exports.getKeyByGroup = getKeyByGroup;
var addHeightToStyle = function(height, style) {
    var nextStyle = style || {};
    return _objectSpread(_objectSpread({}, nextStyle), {}, {
        height: height ? "".concat(height, "px") : nextStyle.height
    })
};
exports.addHeightToStyle = addHeightToStyle;
var getGroupCellClasses = function() {
    var isFirstGroupCell = arguments.length > 0 && void 0 !== arguments[0] ? arguments[0] : false;
    var isLastGroupCell = arguments.length > 1 && void 0 !== arguments[1] ? arguments[1] : false;
    var className = arguments.length > 2 && void 0 !== arguments[2] ? arguments[2] : "";
    return (0, _combine_classes.combineClasses)(_defineProperty({
        "dx-scheduler-first-group-cell": isFirstGroupCell,
        "dx-scheduler-last-group-cell": isLastGroupCell
    }, className, true))
};
exports.getGroupCellClasses = getGroupCellClasses;
var getIsGroupedAllDayPanel = function(viewData, index) {
    var _groupData$allDayPane;
    var groupedData = viewData.groupedData;
    var groupData = groupedData[index];
    var isAllDayPanel = !!(null !== groupData && void 0 !== groupData && null !== (_groupData$allDayPane = groupData.allDayPanel) && void 0 !== _groupData$allDayPane && _groupData$allDayPane.length);
    var isGroupedAllDayPanel = !!(null !== groupData && void 0 !== groupData && groupData.isGroupedAllDayPanel);
    return isAllDayPanel && isGroupedAllDayPanel
};
exports.getIsGroupedAllDayPanel = getIsGroupedAllDayPanel;
var isVerticalGroupOrientation = function(groupOrientation) {
    return groupOrientation === _consts.VERTICAL_GROUP_ORIENTATION
};
exports.isVerticalGroupOrientation = isVerticalGroupOrientation;
