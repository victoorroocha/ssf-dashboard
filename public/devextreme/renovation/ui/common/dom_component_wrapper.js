/**
 * DevExtreme (renovation/ui/common/dom_component_wrapper.js)
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
exports.viewFunction = exports.DomComponentWrapperProps = exports.DomComponentWrapper = void 0;
var _config_context = require("./config_context");
var _render_template = require("../../utils/render_template");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _compat = require("preact/compat");
var _excluded = ["itemTemplate", "valueChange"],
    _excluded2 = ["componentProps", "componentType", "rootElementRef"];

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
    var className = _ref.props.componentProps.className,
        restAttributes = _ref.restAttributes,
        widgetRef = _ref.widgetRef;
    return Preact.h("div", _extends({
        ref: widgetRef,
        className: className
    }, restAttributes))
};
exports.viewFunction = viewFunction;
var DomComponentWrapperProps = {};
exports.DomComponentWrapperProps = DomComponentWrapperProps;
var DomComponentWrapper = (0, _compat.forwardRef)(function(props, ref) {
    var __widgetRef = (0, _hooks.useRef)();
    var __instance = (0, _hooks.useRef)();
    var config = (0, _hooks.useContext)(_config_context.ConfigContext);
    var __properties = (0, _hooks.useCallback)(function() {
        var _props$componentProps = props.componentProps,
            itemTemplate = _props$componentProps.itemTemplate,
            valueChange = _props$componentProps.valueChange,
            restProps = _objectWithoutProperties(_props$componentProps, _excluded);
        var properties = _objectSpread({
            rtlEnabled: (null === config || void 0 === config ? void 0 : config.rtlEnabled) || false
        }, restProps);
        if (valueChange) {
            properties.onValueChanged = function(_ref2) {
                var value = _ref2.value;
                return valueChange(value)
            }
        }
        if (itemTemplate) {
            properties.itemTemplate = function(item, index, container) {
                (0, _render_template.renderTemplate)(itemTemplate, {
                    item: item,
                    index: index,
                    container: container
                }, container)
            }
        }
        return properties
    }, [props.componentProps, config]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var _props$rootElementRef2;
        var _props$rootElementRef = _objectSpread(_objectSpread({}, props), {}, {
                rootElementRef: null === (_props$rootElementRef2 = props.rootElementRef) || void 0 === _props$rootElementRef2 ? void 0 : _props$rootElementRef2.current
            }),
            restProps = (_props$rootElementRef.componentProps, _props$rootElementRef.componentType, _props$rootElementRef.rootElementRef, _objectWithoutProperties(_props$rootElementRef, _excluded2));
        return restProps
    }, [props]);
    var __getInstance = (0, _hooks.useCallback)(function() {
        return __instance.current
    }, []);
    (0, _hooks.useEffect)(function() {
        var _getInstance;
        null === (_getInstance = __getInstance()) || void 0 === _getInstance ? void 0 : _getInstance.option(__properties())
    }, [props.componentProps, config]);
    (0, _hooks.useEffect)(function() {
        var componentInstance = new props.componentType(__widgetRef.current, __properties());
        __instance.current = componentInstance;
        return function() {
            componentInstance.dispose();
            __instance.current = null
        }
    }, []);
    (0, _hooks.useEffect)(function() {
        if (props.rootElementRef) {
            props.rootElementRef.current = __widgetRef.current
        }
    }, []);
    (0, _hooks.useImperativeHandle)(ref, function() {
        return {
            getInstance: __getInstance
        }
    }, [__getInstance]);
    return viewFunction({
        props: _objectSpread({}, props),
        widgetRef: __widgetRef,
        instance: __instance,
        config: config,
        properties: __properties(),
        restAttributes: __restAttributes()
    })
});
exports.DomComponentWrapper = DomComponentWrapper;
DomComponentWrapper.defaultProps = _objectSpread({}, DomComponentWrapperProps);
