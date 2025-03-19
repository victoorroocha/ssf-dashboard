/**
 * DevExtreme (renovation/ui/scheduler/workspaces/base/date_table/all_day_panel/layout.js)
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
exports.AllDayPanelLayout = AllDayPanelLayout;
exports.viewFunction = exports.AllDayPanelLayoutProps = void 0;
var _combine_classes = require("../../../../../../utils/combine_classes");
var _table = require("../../table");
var _table_body = require("./table_body");
var _layout_props = require("../../layout_props");
var _const = require("../../../const");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["className", "dataCellTemplate", "groupOrientation", "viewData", "visible"];

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
var viewFunction = function(viewModel) {
    return Preact.h("div", _extends({}, viewModel.restAttributes, {
        className: viewModel.classes
    }), viewModel.props.visible && Preact.h(_table.Table, {
        className: "dx-scheduler-all-day-table",
        height: viewModel.emptyTableHeight
    }, Preact.h(_table_body.AllDayPanelTableBody, {
        viewData: viewModel.allDayPanelData,
        dataCellTemplate: viewModel.props.dataCellTemplate
    })))
};
exports.viewFunction = viewFunction;
var AllDayPanelLayoutProps = _objectSpread(_objectSpread({}, _layout_props.LayoutProps), {}, {
    className: "",
    visible: true
});
exports.AllDayPanelLayoutProps = AllDayPanelLayoutProps;
var getTemplate = function(TemplateProp) {
    return TemplateProp && (TemplateProp.defaultProps ? function(props) {
        return Preact.h(TemplateProp, _extends({}, props))
    } : TemplateProp)
};

function AllDayPanelLayout(props) {
    var __allDayPanelData = (0, _hooks.useCallback)(function() {
        return props.viewData.groupedData[0].allDayPanel
    }, [props.viewData]);
    var __emptyTableHeight = (0, _hooks.useCallback)(function() {
        return __allDayPanelData() ? void 0 : _const.DefaultSizes.allDayPanelHeight
    }, [props.viewData]);
    var __classes = (0, _hooks.useCallback)(function() {
        return (0, _combine_classes.combineClasses)(_defineProperty({
            "dx-scheduler-all-day-panel": true,
            "dx-hidden": !props.visible
        }, props.className, !!props.className))
    }, [props.visible, props.className]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var restProps = (props.className, props.dataCellTemplate, props.groupOrientation, props.viewData, props.visible, _objectWithoutProperties(props, _excluded));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread(_objectSpread({}, props), {}, {
            dataCellTemplate: getTemplate(props.dataCellTemplate)
        }),
        allDayPanelData: __allDayPanelData(),
        emptyTableHeight: __emptyTableHeight(),
        classes: __classes(),
        restAttributes: __restAttributes()
    })
}
AllDayPanelLayout.defaultProps = _objectSpread({}, AllDayPanelLayoutProps);
