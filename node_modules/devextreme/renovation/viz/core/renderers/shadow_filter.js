/**
 * DevExtreme (renovation/viz/core/renderers/shadow_filter.js)
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
exports.ShadowFilter = ShadowFilter;
exports.viewFunction = exports.ShadowFilterProps = void 0;
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["blur", "color", "height", "id", "offsetX", "offsetY", "opacity", "width", "x", "y"];

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
var viewFunction = function(_ref) {
    var _ref$props = _ref.props,
        blur = _ref$props.blur,
        color = _ref$props.color,
        height = _ref$props.height,
        id = _ref$props.id,
        offsetX = _ref$props.offsetX,
        offsetY = _ref$props.offsetY,
        opacity = _ref$props.opacity,
        width = _ref$props.width,
        x = _ref$props.x,
        y = _ref$props.y;
    return Preact.h("filter", {
        id: id,
        x: x,
        y: y,
        width: width,
        height: height
    }, Preact.h("feGaussianBlur", {
        "in": "SourceGraphic",
        result: "gaussianBlurResult",
        stdDeviation: blur
    }), Preact.h("feOffset", {
        "in": "gaussianBlurResult",
        result: "offsetResult",
        dx: offsetX,
        dy: offsetY
    }), Preact.h("feFlood", {
        result: "floodResult",
        floodColor: color,
        floodOpacity: opacity
    }), Preact.h("feComposite", {
        "in": "floodResult",
        in2: "offsetResult",
        operator: "in",
        result: "compositeResult"
    }), Preact.h("feComposite", {
        "in": "SourceGraphic",
        in2: "compositeResult",
        operator: "over"
    }))
};
exports.viewFunction = viewFunction;
var ShadowFilterProps = {
    x: 0,
    y: 0,
    width: 0,
    height: 0,
    offsetX: 0,
    offsetY: 0,
    blur: 0,
    color: ""
};
exports.ShadowFilterProps = ShadowFilterProps;

function ShadowFilter(props) {
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var restProps = (props.blur, props.color, props.height, props.id, props.offsetX, props.offsetY, props.opacity, props.width, props.x, props.y, _objectWithoutProperties(props, _excluded));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread({}, props),
        restAttributes: __restAttributes()
    })
}
ShadowFilter.defaultProps = _objectSpread({}, ShadowFilterProps);
