/**
 * DevExtreme (exporter/exceljs/export_merged_ranges_manager.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.MergedRangesManager = void 0;

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
var MergedRangesManager = function() {
    function MergedRangesManager(dataProvider, helpers, mergeRowFieldValues, mergeColumnFieldValues) {
        this.mergedCells = [];
        this.mergedRanges = [];
        this.dataProvider = dataProvider;
        this.helpers = helpers;
        this.mergeRowFieldValues = mergeRowFieldValues;
        this.mergeColumnFieldValues = mergeColumnFieldValues
    }
    var _proto = MergedRangesManager.prototype;
    _proto.updateMergedRanges = function(excelCell, rowIndex, cellIndex) {
        if (this.helpers._isHeaderCell(this.dataProvider, rowIndex, cellIndex)) {
            if (!this.isCellInMergedRanges(rowIndex, cellIndex)) {
                var _this$dataProvider$ge = this.dataProvider.getCellMerging(rowIndex, cellIndex),
                    rowspan = _this$dataProvider$ge.rowspan,
                    colspan = _this$dataProvider$ge.colspan;
                var isMasterCellOfMergedRange = colspan || rowspan;
                if (isMasterCellOfMergedRange) {
                    var allowToMergeRange = this.helpers._allowToMergeRange(this.dataProvider, rowIndex, cellIndex, rowspan, colspan, this.mergeRowFieldValues, this.mergeColumnFieldValues);
                    this.updateMergedCells(excelCell, rowIndex, cellIndex, rowspan, colspan, allowToMergeRange);
                    if (allowToMergeRange) {
                        this.mergedRanges.push(_objectSpread({
                            masterCell: excelCell
                        }, {
                            rowspan: rowspan,
                            colspan: colspan
                        }))
                    }
                }
            }
        }
    };
    _proto.isCellInMergedRanges = function(rowIndex, cellIndex) {
        return this.mergedCells[rowIndex] && this.mergedCells[rowIndex][cellIndex]
    };
    _proto.findMergedCellInfo = function(rowIndex, cellIndex) {
        if (this.helpers._isHeaderCell(this.dataProvider, rowIndex, cellIndex)) {
            if (this.isCellInMergedRanges(rowIndex, cellIndex)) {
                return this.mergedCells[rowIndex][cellIndex]
            }
        }
    };
    _proto.updateMergedCells = function(excelCell, rowIndex, cellIndex, rowspan, colspan, allowToMergeRange) {
        for (var i = rowIndex; i <= rowIndex + rowspan; i++) {
            for (var j = cellIndex; j <= cellIndex + colspan; j++) {
                if (!this.mergedCells[i]) {
                    this.mergedCells[i] = []
                }
                this.mergedCells[i][j] = {
                    masterCell: excelCell,
                    unmerged: !allowToMergeRange
                }
            }
        }
    };
    _proto.applyMergedRages = function(worksheet) {
        this.mergedRanges.forEach(function(range) {
            var startRowIndex = range.masterCell.fullAddress.row;
            var startColumnIndex = range.masterCell.fullAddress.col;
            var endRowIndex = startRowIndex + range.rowspan;
            var endColumnIndex = startColumnIndex + range.colspan;
            worksheet.mergeCells(startRowIndex, startColumnIndex, endRowIndex, endColumnIndex)
        })
    };
    return MergedRangesManager
}();
exports.MergedRangesManager = MergedRangesManager;
