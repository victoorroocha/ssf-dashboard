/**
 * DevExtreme (renovation/ui/scheduler/appointment_tooltip/appointment_list.js)
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
exports.AppointmentList = AppointmentList;
exports.viewFunction = exports.AppointmentListProps = void 0;
var _noop = _interopRequireDefault(require("../../../utils/noop"));
var _list = require("../../list");
var _item_layout = require("./item_layout");
var _get_current_appointment = _interopRequireDefault(require("./utils/get_current_appointment"));
var _default_functions = require("./utils/default_functions");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["appointments", "checkAndDeleteAppointment", "focusStateEnabled", "getSingleAppointmentData", "getTextAndFormatDate", "isEditingAllowed", "itemContentTemplate", "onHide", "showAppointmentPopup", "target"];

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

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
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
    return Preact.h(_list.List, _extends({
        itemTemplate: function(_ref) {
            var index = _ref.index,
                item = _ref.item;
            return Preact.h(_item_layout.TooltipItemLayout, {
                item: item,
                index: index,
                onDelete: viewModel.props.checkAndDeleteAppointment,
                onHide: viewModel.props.onHide,
                itemContentTemplate: viewModel.props.itemContentTemplate,
                getTextAndFormatDate: viewModel.props.getTextAndFormatDate,
                singleAppointment: viewModel.props.getSingleAppointmentData(item.data, viewModel.props.target),
                showDeleteButton: viewModel.props.isEditingAllowed && !item.data.disabled
            })
        },
        dataSource: viewModel.props.appointments,
        focusStateEnabled: viewModel.props.focusStateEnabled,
        onItemClick: viewModel.onItemClick
    }, viewModel.restAttributes))
};
exports.viewFunction = viewFunction;
var AppointmentListProps = {
    isEditingAllowed: true,
    focusStateEnabled: false,
    showAppointmentPopup: _noop.default,
    onHide: _noop.default,
    checkAndDeleteAppointment: _noop.default,
    getTextAndFormatDate: _default_functions.defaultGetTextAndFormatDate,
    getSingleAppointmentData: _default_functions.defaultGetSingleAppointment
};
exports.AppointmentListProps = AppointmentListProps;
var getTemplate = function(TemplateProp) {
    return TemplateProp && (TemplateProp.defaultProps ? function(props) {
        return Preact.h(TemplateProp, _extends({}, props))
    } : TemplateProp)
};

function AppointmentList(props) {
    var __onItemClick = (0, _hooks.useCallback)(function() {
        return function(_ref2) {
            var itemData = _ref2.itemData;
            var showAppointmentPopup = props.showAppointmentPopup;
            showAppointmentPopup(itemData.data, false, (0, _get_current_appointment.default)(itemData))
        }
    }, [props.showAppointmentPopup]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var restProps = (props.appointments, props.checkAndDeleteAppointment, props.focusStateEnabled, props.getSingleAppointmentData, props.getTextAndFormatDate, props.isEditingAllowed, props.itemContentTemplate, props.onHide, props.showAppointmentPopup, props.target, _objectWithoutProperties(props, _excluded));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread(_objectSpread({}, props), {}, {
            itemContentTemplate: getTemplate(props.itemContentTemplate)
        }),
        onItemClick: __onItemClick(),
        restAttributes: __restAttributes()
    })
}
AppointmentList.defaultProps = _objectSpread({}, AppointmentListProps);
