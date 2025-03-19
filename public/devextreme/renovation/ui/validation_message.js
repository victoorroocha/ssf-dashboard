/**
 * DevExtreme (renovation/ui/validation_message.js)
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
exports.ValidationMessage = ValidationMessage;
exports.viewFunction = exports.ValidationMessageProps = void 0;
var _widget = require("./common/widget");
var _validation_message = _interopRequireDefault(require("../../ui/validation_message"));
var _dom_component_wrapper = require("./common/dom_component_wrapper");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["rootElementRef"],
    _excluded2 = ["_feedbackHideTimeout", "_feedbackShowTimeout", "accessKey", "activeStateEnabled", "activeStateUnit", "aria", "boundary", "children", "className", "classes", "container", "disabled", "focusStateEnabled", "height", "hint", "hoverStateEnabled", "mode", "name", "offset", "onActive", "onClick", "onContentReady", "onDimensionChanged", "onFocusIn", "onFocusOut", "onInactive", "onKeyDown", "onKeyboardHandled", "onVisibilityChange", "positionRequest", "rootElementRef", "rtlEnabled", "tabIndex", "target", "validationErrors", "visible", "width"];

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
    var componentProps = _ref.componentProps,
        rootElementRef = _ref.props.rootElementRef,
        restAttributes = _ref.restAttributes;
    return Preact.h(_dom_component_wrapper.DomComponentWrapper, _extends({
        rootElementRef: rootElementRef,
        componentType: _validation_message.default,
        componentProps: componentProps
    }, restAttributes))
};
exports.viewFunction = viewFunction;
var ValidationMessageProps = _objectSpread(_objectSpread({}, _widget.WidgetProps), {}, {
    mode: "auto",
    offset: {
        h: 0,
        v: 0
    }
});
exports.ValidationMessageProps = ValidationMessageProps;

function ValidationMessage(props) {
    var __componentProps = (0, _hooks.useCallback)(function() {
        var _props$boundary, _props$container, _props$target, _props$rootElementRef;
        var _props$boundary$conta = _objectSpread(_objectSpread({}, props), {}, {
                boundary: null === (_props$boundary = props.boundary) || void 0 === _props$boundary ? void 0 : _props$boundary.current,
                container: null === (_props$container = props.container) || void 0 === _props$container ? void 0 : _props$container.current,
                target: null === (_props$target = props.target) || void 0 === _props$target ? void 0 : _props$target.current,
                rootElementRef: null === (_props$rootElementRef = props.rootElementRef) || void 0 === _props$rootElementRef ? void 0 : _props$rootElementRef.current
            }),
            restProps = (_props$boundary$conta.rootElementRef, _objectWithoutProperties(_props$boundary$conta, _excluded));
        return restProps
    }, [props]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var _props$boundary2, _props$container2, _props$target2, _props$rootElementRef2;
        var _props$boundary$conta2 = _objectSpread(_objectSpread({}, props), {}, {
                boundary: null === (_props$boundary2 = props.boundary) || void 0 === _props$boundary2 ? void 0 : _props$boundary2.current,
                container: null === (_props$container2 = props.container) || void 0 === _props$container2 ? void 0 : _props$container2.current,
                target: null === (_props$target2 = props.target) || void 0 === _props$target2 ? void 0 : _props$target2.current,
                rootElementRef: null === (_props$rootElementRef2 = props.rootElementRef) || void 0 === _props$rootElementRef2 ? void 0 : _props$rootElementRef2.current
            }),
            restProps = (_props$boundary$conta2._feedbackHideTimeout, _props$boundary$conta2._feedbackShowTimeout, _props$boundary$conta2.accessKey, _props$boundary$conta2.activeStateEnabled, _props$boundary$conta2.activeStateUnit, _props$boundary$conta2.aria, _props$boundary$conta2.boundary, _props$boundary$conta2.children, _props$boundary$conta2.className, _props$boundary$conta2.classes, _props$boundary$conta2.container, _props$boundary$conta2.disabled, _props$boundary$conta2.focusStateEnabled, _props$boundary$conta2.height, _props$boundary$conta2.hint, _props$boundary$conta2.hoverStateEnabled, _props$boundary$conta2.mode, _props$boundary$conta2.name, _props$boundary$conta2.offset, _props$boundary$conta2.onActive, _props$boundary$conta2.onClick, _props$boundary$conta2.onContentReady, _props$boundary$conta2.onDimensionChanged, _props$boundary$conta2.onFocusIn, _props$boundary$conta2.onFocusOut, _props$boundary$conta2.onInactive, _props$boundary$conta2.onKeyDown, _props$boundary$conta2.onKeyboardHandled, _props$boundary$conta2.onVisibilityChange, _props$boundary$conta2.positionRequest, _props$boundary$conta2.rootElementRef, _props$boundary$conta2.rtlEnabled, _props$boundary$conta2.tabIndex, _props$boundary$conta2.target, _props$boundary$conta2.validationErrors, _props$boundary$conta2.visible, _props$boundary$conta2.width, _objectWithoutProperties(_props$boundary$conta2, _excluded2));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread({}, props),
        componentProps: __componentProps(),
        restAttributes: __restAttributes()
    })
}
ValidationMessage.defaultProps = _objectSpread({}, ValidationMessageProps);
