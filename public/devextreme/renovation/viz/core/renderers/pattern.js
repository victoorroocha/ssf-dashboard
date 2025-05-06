/**
 * DevExtreme (renovation/viz/core/renderers/pattern.js)
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
exports.SvgPattern = SvgPattern;
exports.viewFunction = exports.SvgPatternProps = void 0;
var _svg_rect = require("./svg_rect");
var _svg_path = require("./svg_path");
var _utils = require("../../../../viz/core/utils");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["color", "hatching", "id"];

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
    var d = _ref.d,
        _ref$props = _ref.props,
        color = _ref$props.color,
        hatching = _ref$props.hatching,
        id = _ref$props.id,
        step = _ref.step;
    return Preact.h("pattern", {
        id: id,
        width: step,
        height: step,
        patternUnits: "userSpaceOnUse"
    }, Preact.h(_svg_rect.RectSvgElement, {
        x: 0,
        y: 0,
        width: step,
        height: step,
        fill: color,
        opacity: null === hatching || void 0 === hatching ? void 0 : hatching.opacity
    }), Preact.h(_svg_path.PathSvgElement, {
        d: d,
        strokeWidth: (null === hatching || void 0 === hatching ? void 0 : hatching.width) || 1,
        stroke: color
    }))
};
exports.viewFunction = viewFunction;
var SvgPatternProps = {
    color: ""
};
exports.SvgPatternProps = SvgPatternProps;

function SvgPattern(props) {
    var __step = (0, _hooks.useCallback)(function() {
        var _props$hatching;
        return (null === (_props$hatching = props.hatching) || void 0 === _props$hatching ? void 0 : _props$hatching.step) || 6
    }, [props.hatching]);
    var __d = (0, _hooks.useCallback)(function() {
        var _props$hatching2;
        var stepTo2 = __step() / 2;
        var stepBy15 = 1.5 * __step();
        return "right" === (0, _utils.normalizeEnum)(null === (_props$hatching2 = props.hatching) || void 0 === _props$hatching2 ? void 0 : _props$hatching2.direction) ? "M ".concat(stepTo2, " ").concat(-stepTo2, " L ").concat(-stepTo2, " ").concat(stepTo2, " M 0 ").concat(__step(), " L ").concat(__step(), " 0 M ").concat(stepBy15, " ").concat(stepTo2, " L ").concat(stepTo2, " ").concat(stepBy15) : "M 0 0 L ".concat(__step(), " ").concat(__step(), " M ").concat(-stepTo2, " ").concat(stepTo2, " L ").concat(stepTo2, " ").concat(stepBy15, " M ").concat(stepTo2, " ").concat(-stepTo2, " L ").concat(stepBy15, " ").concat(stepTo2)
    }, [props.hatching]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var restProps = (props.color, props.hatching, props.id, _objectWithoutProperties(props, _excluded));
        return restProps
    }, [props]);
    return viewFunction({
        props: _objectSpread({}, props),
        step: __step(),
        d: __d(),
        restAttributes: __restAttributes()
    })
}
SvgPattern.defaultProps = _objectSpread({}, SvgPatternProps);
