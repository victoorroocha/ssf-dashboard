/**
 * DevExtreme (renovation/ui/data_grid/props.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.DataGridSummaryTotalItem = exports.DataGridSummaryGroupItem = exports.DataGridSummary = exports.DataGridStateStoring = exports.DataGridSorting = exports.DataGridSortByGroupSummaryInfoItem = exports.DataGridSelection = exports.DataGridSearchPanel = exports.DataGridScrolling = exports.DataGridRowDragging = exports.DataGridProps = exports.DataGridPaging = exports.DataGridPager = exports.DataGridMasterDetail = exports.DataGridLoadPanel = exports.DataGridKeyboardNavigation = exports.DataGridHeaderFilter = exports.DataGridGrouping = exports.DataGridGroupPanel = exports.DataGridFilterRow = exports.DataGridFilterPanel = exports.DataGridExport = exports.DataGridEditingTexts = exports.DataGridEditing = exports.DataGridColumnLookup = exports.DataGridColumnHeaderFilter = exports.DataGridColumnFixing = exports.DataGridColumnChooser = exports.DataGridColumnButton = exports.DataGridColumn = void 0;
var _widget = require("../common/widget");

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
var DataGridColumnButton = {};
exports.DataGridColumnButton = DataGridColumnButton;
var DataGridColumnHeaderFilter = {};
exports.DataGridColumnHeaderFilter = DataGridColumnHeaderFilter;
var DataGridColumnLookup = {};
exports.DataGridColumnLookup = DataGridColumnLookup;
var DataGridColumn = {};
exports.DataGridColumn = DataGridColumn;
var DataGridEditingTexts = {};
exports.DataGridEditingTexts = DataGridEditingTexts;
var DataGridEditing = {};
exports.DataGridEditing = DataGridEditing;
var DataGridScrolling = {};
exports.DataGridScrolling = DataGridScrolling;
var DataGridSelection = {};
exports.DataGridSelection = DataGridSelection;
var DataGridPaging = {};
exports.DataGridPaging = DataGridPaging;
var DataGridSortByGroupSummaryInfoItem = {};
exports.DataGridSortByGroupSummaryInfoItem = DataGridSortByGroupSummaryInfoItem;
var DataGridGroupPanel = {};
exports.DataGridGroupPanel = DataGridGroupPanel;
var DataGridGrouping = {};
exports.DataGridGrouping = DataGridGrouping;
var DataGridSummaryGroupItem = {};
exports.DataGridSummaryGroupItem = DataGridSummaryGroupItem;
var DataGridSummaryTotalItem = {};
exports.DataGridSummaryTotalItem = DataGridSummaryTotalItem;
var DataGridSummary = {};
exports.DataGridSummary = DataGridSummary;
var DataGridPager = {};
exports.DataGridPager = DataGridPager;
var DataGridMasterDetail = {};
exports.DataGridMasterDetail = DataGridMasterDetail;
var DataGridRowDragging = {};
exports.DataGridRowDragging = DataGridRowDragging;
var DataGridColumnChooser = {};
exports.DataGridColumnChooser = DataGridColumnChooser;
var DataGridColumnFixing = {};
exports.DataGridColumnFixing = DataGridColumnFixing;
var DataGridSearchPanel = {};
exports.DataGridSearchPanel = DataGridSearchPanel;
var DataGridSorting = {};
exports.DataGridSorting = DataGridSorting;
var DataGridStateStoring = {};
exports.DataGridStateStoring = DataGridStateStoring;
var DataGridFilterPanel = {};
exports.DataGridFilterPanel = DataGridFilterPanel;
var DataGridFilterRow = {};
exports.DataGridFilterRow = DataGridFilterRow;
var DataGridHeaderFilter = {};
exports.DataGridHeaderFilter = DataGridHeaderFilter;
var DataGridKeyboardNavigation = {};
exports.DataGridKeyboardNavigation = DataGridKeyboardNavigation;
var DataGridLoadPanel = {};
exports.DataGridLoadPanel = DataGridLoadPanel;
var DataGridExport = {};
exports.DataGridExport = DataGridExport;
var DataGridProps = _objectSpread(_objectSpread({}, _widget.WidgetProps), {}, {
    showColumnHeaders: true,
    defaultFilterValue: null,
    filterValueChange: function() {},
    defaultFocusedColumnIndex: -1,
    focusedColumnIndexChange: function() {},
    defaultFocusedRowIndex: -1,
    focusedRowIndexChange: function() {},
    defaultFocusedRowKey: null,
    focusedRowKeyChange: function() {},
    defaultSelectedRowKeys: [],
    selectedRowKeysChange: function() {},
    defaultSelectionFilter: [],
    selectionFilterChange: function() {}
});
exports.DataGridProps = DataGridProps;
