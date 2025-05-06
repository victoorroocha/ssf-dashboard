/**
 * DevExtreme (renovation/ui/overlay.js)
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
exports.Overlay = Overlay;
exports.viewFunction = exports.OverlayProps = void 0;
var _widget = require("./common/widget");
var _overlay = _interopRequireDefault(require("../../ui/overlay"));
var _dom_component_wrapper = require("./common/dom_component_wrapper");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["rootElementRef"],
    _excluded2 = ["_checkParentVisibility", "_feedbackHideTimeout", "_feedbackShowTimeout", "accessKey", "activeStateEnabled", "activeStateUnit", "animation", "aria", "children", "className", "classes", "closeOnOutsideClick", "closeOnTargetScroll", "container", "contentTemplate", "disabled", "focusStateEnabled", "height", "hint", "hoverStateEnabled", "integrationOptions", "maxWidth", "name", "onActive", "onClick", "onContentReady", "onDimensionChanged", "onFocusIn", "onFocusOut", "onInactive", "onKeyDown", "onKeyboardHandled", "onVisibilityChange", "propagateOutsideClick", "rootElementRef", "rtlEnabled", "shading", "tabIndex", "templatesRenderAsynchronously", "visible", "width"];

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
        componentType: _overlay.default,
        componentProps: componentProps
    }, restAttributes))
};
exports.viewFunction = viewFunction;
var OverlayProps = _objectSpread(_objectSpread({}, _widget.WidgetProps), {}, {
    integrationOptions: {},
    templatesRenderAsynchronously: false,
    shading: true,
    closeOnOutsideClick: false,
    closeOnTargetScroll: false,
    animation: {
        type: "pop",
        duration: 300,
        to: {
            opacity: 0,
            scale: .55
        },
        from: {
            opacity: 1,
            scale: 1
        }
    },
    visible: false,
    propagateOutsideClick: true,
    _checkParentVisibility: false,
    rtlEnabled: false,
    contentTemplate: "content",
    maxWidth: null
});
exports.OverlayProps = OverlayProps;

function Overlay(props) {
    var __componentProps = (0, _hooks.useCallback)(function() {
        var _props$rootElementRef2;
        var _props$rootElementRef = _objectSpread(_objectSpread({}, props), {}, {
                rootElementRef: null === (_props$rootElementRef2 = props.rootElementRef) || void 0 === _props$rootElementRef2 ? void 0 : _props$rootElementRef2.current
            }),
            restProps = (_props$rootElementRef.rootElementRef, _objectWithoutProperties(_props$rootElementRef, _excluded));
        return restProps
    }, [props]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var _props$rootElementRef4;
        var _props$rootElementRef3 = _objectSpread(_objectSpread({}, props), {}, {
                rootElementRef: null === (_props$rootElementRef4 = props.rootElementRef) || void 0 === _props$rootElementRef4 ? void 0 : _props$rootElementRef4.current
            }),
            restProps = (_props$rootElementRef3._checkParentVisibility, _props$rootElementRef3._feedbackHideTimeout, _props$rootElementRef3._feedbackShowTimeout, _props$rootElementRef3.accessKey, _props$rootElementRef3.activeStateEnabled, _props$rootElementRef3.activeStateUnit, _props$rootElementRef3.animation, _props$rootElementRef3.aria, _props$rootElementRef3.children, _props$rootElementRef3.className, _props$rootElementRef3.classes, _props$rootElementRef3.closeOnOutsideClick, _props$rootElementRef3.closeOnTargetScroll, _props$rootElementRef3.container, _props$rootElementRef3.contentTemplate, _props$rootElementRef3.disabled, _props$rootElementRef3.focusStateEnabled, _props$rootElementRef3.height, _props$rootElementRef3.hint, _props$rootElementRef3.hoverStateEnabled, _props$rootElementRef3.integrationOptions, _props$rootElementRef3.maxWidth, _props$rootElementRef3.name, _props$rootElementRef3.onActive, _props$rootElementRef3.onClick, _props$rootElementRef3.onContentReady, _props$rootElementRef3.onDimensionChanged, _props$rootElementRef3.onFocusIn, _props$rootElementRef3.onFocusOut, _props$rootElementRef3.onInactive, _props$rootElementRef3.onKeyDown, _props$rootElementRef3.onKeyboardHandled, _props$rootElementRef3.onVisibilityChange, _props$rootElementRef3.propagateOutsideClick, _props$rootElementRef3.rootElementRef, _props$rootElementRef3.rtlEnabled, _props$rootElementRef3.shading, _props$rootElementRef3.tabIndex, _props$rootElementRef3.templatesRenderAsynchronously, _props$rootElementRef3.visible, _props$rootElementRef3.width, _objectWithoutProperties(_props$rootElementRef3, _excluded2));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread({}, props),
        componentProps: __componentProps(),
        restAttributes: __restAttributes()
    })
}
Overlay.defaultProps = _objectSpread({}, OverlayProps);
