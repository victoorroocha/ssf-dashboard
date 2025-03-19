/**
 * DevExtreme (renovation/ui/scheduler/workspaces/base/time_panel/layout.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";

function _typeof(obj) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
        return typeof obj
    } : function(obj) {
        return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj
    }, _typeof(obj)
}
exports.TimePanelTableLayout = TimePanelTableLayout;
exports.viewFunction = exports.TimePanelTableLayoutProps = void 0;
var _row = require("../row");
var _cell = require("./cell");
var _cell2 = require("../cell");
var _utils = require("../../utils");
var _table = require("../table");
var _layout_props = require("../layout_props");
var _title = require("../date_table/all_day_panel/title");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["allDayPanelVisible", "className", "dataCellTemplate", "groupOrientation", "timeCellTemplate", "viewData"];

function _getRequireWildcardCache(nodeInterop) {
    if ("function" !== typeof WeakMap) {
        return null
    }
    var cacheBabelInterop = new WeakMap;
    var cacheNodeInterop = new WeakMap;
    return (_getRequireWildcardCache = function(nodeInterop) {
        return nodeInterop ? cacheNodeInterop : cacheBabelInterop
    })(nodeInterop)
}

function _interopRequireWildcard(obj, nodeInterop) {
    if (!nodeInterop && obj && obj.__esModule) {
        return obj
    }
    if (null === obj || "object" !== _typeof(obj) && "function" !== typeof obj) {
        return {
            "default": obj
        }
    }
    var cache = _getRequireWildcardCache(nodeInterop);
    if (cache && cache.has(obj)) {
        return cache.get(obj)
    }
    var newObj = {};
    var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor;
    for (var key in obj) {
        if ("default" !== key && Object.prototype.hasOwnProperty.call(obj, key)) {
            var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null;
            if (desc && (desc.get || desc.set)) {
                Object.defineProperty(newObj, key, desc)
            } else {
                newObj[key] = obj[key]
            }
        }
    }
    newObj.default = obj;
    if (cache) {
        cache.set(obj, newObj)
    }
    return newObj
}

function _objectWithoutProperties(source, excluded) {
    if (null == source) {
        return {}
    }
    var target = _objectWithoutPropertiesLoose(source, excluded);
    var key, i;
    if (Object.getOwnPropertySymbols) {
        var sourceSymbolKeys = Object.getOwnPropertySymbols(source);
        for (i = 0; i < sourceSymbolKeys.length; i++) {
            key = sourceSymbolKeys[i];
            if (excluded.indexOf(key) >= 0) {
                continue
            }
            if (!Object.prototype.propertyIsEnumerable.call(source, key)) {
                continue
            }
            target[key] = source[key]
        }
    }
    return target
}

function _objectWithoutPropertiesLoose(source, excluded) {
    if (null == source) {
        return {}
    }
    var target = {};
    var sourceKeys = Object.keys(source);
    var key, i;
    for (i = 0; i < sourceKeys.length; i++) {
        key = sourceKeys[i];
        if (excluded.indexOf(key) >= 0) {
            continue
        }
        target[key] = source[key]
    }
    return target
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

function _extends() {
    _extends = Object.assign ? Object.assign.bind() : function(target) {
        for (var i = 1; i < arguments.length; i++) {
            var source = arguments[i];
            for (var key in source) {
                if (Object.prototype.hasOwnProperty.call(source, key)) {
                    target[key] = source[key]
                }
            }
        }
        return target
    };
    return _extends.apply(this, arguments)
}
var viewFunction = function(_ref) {
    var bottomVirtualRowHeight = _ref.bottomVirtualRowHeight,
        isVerticalGrouping = _ref.isVerticalGroupOrientation,
        isVirtual = _ref.isVirtual,
        props = _ref.props,
        restAttributes = _ref.restAttributes,
        topVirtualRowHeight = _ref.topVirtualRowHeight;
    var timeCellTemplate = props.timeCellTemplate,
        viewData = props.viewData;
    return Preact.h(_table.Table, _extends({}, restAttributes, {
        isVirtual: isVirtual,
        topVirtualRowHeight: topVirtualRowHeight,
        bottomVirtualRowHeight: bottomVirtualRowHeight,
        virtualCellsCount: 1,
        className: "dx-scheduler-time-panel"
    }), viewData.groupedData.map(function(_ref2, index) {
        var dateTable = _ref2.dateTable,
            groupIndex = _ref2.groupIndex;
        return Preact.h(Preact.Fragment, {
            key: (0, _utils.getKeyByGroup)(groupIndex)
        }, (0, _utils.getIsGroupedAllDayPanel)(viewData, index) && Preact.h(_row.Row, null, Preact.h(_cell2.CellBase, {
            className: "dx-scheduler-time-panel-title-cell"
        }, Preact.h(_title.AllDayPanelTitle, null))), dateTable.map(function(cellsRow) {
            var cellCountInGroupRow = viewData.cellCountInGroupRow;
            var _cellsRow$ = cellsRow[0],
                groups = _cellsRow$.groups,
                cellIndex = _cellsRow$.index,
                isFirstGroupCell = _cellsRow$.isFirstGroupCell,
                isLastGroupCell = _cellsRow$.isLastGroupCell,
                key = _cellsRow$.key,
                startDate = _cellsRow$.startDate,
                text = _cellsRow$.text;
            return Preact.h(_row.Row, {
                className: "dx-scheduler-time-panel-row",
                key: key
            }, Preact.h(_cell.TimePanelCell, {
                startDate: startDate,
                text: text,
                groups: isVerticalGrouping ? groups : void 0,
                groupIndex: isVerticalGrouping ? groupIndex : void 0,
                isFirstGroupCell: isVerticalGrouping && isFirstGroupCell,
                isLastGroupCell: isVerticalGrouping && isLastGroupCell,
                index: Math.floor(cellIndex / cellCountInGroupRow),
                timeCellTemplate: timeCellTemplate
            }))
        }))
    }))
};
exports.viewFunction = viewFunction;
var TimePanelTableLayoutProps = _objectSpread(_objectSpread({}, _layout_props.LayoutProps), {}, {
    className: "",
    allDayPanelVisible: false
});
exports.TimePanelTableLayoutProps = TimePanelTableLayoutProps;
var getTemplate = function(TemplateProp) {
    return TemplateProp && (TemplateProp.defaultProps ? function(props) {
        return Preact.h(TemplateProp, _extends({}, props))
    } : TemplateProp)
};

function TimePanelTableLayout(props) {
    var __isVirtual = (0, _hooks.useCallback)(function() {
        var viewData = props.viewData;
        return !!viewData.isVirtual
    }, [props.viewData]);
    var __topVirtualRowHeight = (0, _hooks.useCallback)(function() {
        return props.viewData.topVirtualRowHeight || 0
    }, [props.viewData]);
    var __bottomVirtualRowHeight = (0, _hooks.useCallback)(function() {
        return props.viewData.bottomVirtualRowHeight || 0
    }, [props.viewData]);
    var __isVerticalGroupOrientation = (0, _hooks.useCallback)(function() {
        var groupOrientation = props.groupOrientation;
        return (0, _utils.isVerticalGroupOrientation)(groupOrientation)
    }, [props.groupOrientation]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var restProps = (props.allDayPanelVisible, props.className, props.dataCellTemplate, props.groupOrientation, props.timeCellTemplate, props.viewData, _objectWithoutProperties(props, _excluded));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread(_objectSpread({}, props), {}, {
            timeCellTemplate: getTemplate(props.timeCellTemplate),
            dataCellTemplate: getTemplate(props.dataCellTemplate)
        }),
        isVirtual: __isVirtual(),
        topVirtualRowHeight: __topVirtualRowHeight(),
        bottomVirtualRowHeight: __bottomVirtualRowHeight(),
        isVerticalGroupOrientation: __isVerticalGroupOrientation(),
        restAttributes: __restAttributes()
    })
}
TimePanelTableLayout.defaultProps = _objectSpread({}, TimePanelTableLayoutProps);
