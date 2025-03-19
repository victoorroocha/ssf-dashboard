/**
 * DevExtreme (renovation/ui/pager/page_size/small.js)
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
exports.PageSizeSmall = PageSizeSmall;
exports.viewFunction = exports.PageSizeSmallProps = void 0;
var _select_box = require("../../select_box");
var _calculate_values_fitted_width = require("../utils/calculate_values_fitted_width");
var _get_element_width = require("../utils/get_element_width");
var _pager_props = _interopRequireDefault(require("../common/pager_props"));
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _excluded = ["defaultPageSize", "pageSize", "pageSizeChange", "pageSizes", "parentRef"];

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

function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest()
}

function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
}

function _unsupportedIterableToArray(o, minLen) {
    if (!o) {
        return
    }
    if ("string" === typeof o) {
        return _arrayLikeToArray(o, minLen)
    }
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if ("Object" === n && o.constructor) {
        n = o.constructor.name
    }
    if ("Map" === n || "Set" === n) {
        return Array.from(o)
    }
    if ("Arguments" === n || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) {
        return _arrayLikeToArray(o, minLen)
    }
}

function _arrayLikeToArray(arr, len) {
    if (null == len || len > arr.length) {
        len = arr.length
    }
    for (var i = 0, arr2 = new Array(len); i < len; i++) {
        arr2[i] = arr[i]
    }
    return arr2
}

function _iterableToArrayLimit(arr, i) {
    var _i = null == arr ? null : "undefined" != typeof Symbol && arr[Symbol.iterator] || arr["@@iterator"];
    if (null != _i) {
        var _s, _e, _x, _r, _arr = [],
            _n = !0,
            _d = !1;
        try {
            if (_x = (_i = _i.call(arr)).next, 0 === i) {
                if (Object(_i) !== _i) {
                    return
                }
                _n = !1
            } else {
                for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0) {}
            }
        } catch (err) {
            _d = !0, _e = err
        } finally {
            try {
                if (!_n && null != _i.return && (_r = _i.return(), Object(_r) !== _r)) {
                    return
                }
            } finally {
                if (_d) {
                    throw _e
                }
            }
        }
        return _arr
    }
}

function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) {
        return arr
    }
}
var viewFunction = function(_ref) {
    var _ref$props = _ref.props,
        pageSize = _ref$props.pageSize,
        pageSizeChange = _ref$props.pageSizeChange,
        pageSizes = _ref$props.pageSizes,
        width = _ref.width;
    return Preact.h(_select_box.SelectBox, {
        displayExpr: "text",
        valueExpr: "value",
        dataSource: pageSizes,
        value: pageSize,
        valueChange: pageSizeChange,
        width: width
    })
};
exports.viewFunction = viewFunction;
var PageSizeSmallProps = {};
exports.PageSizeSmallProps = PageSizeSmallProps;
var PageSizeSmallPropsType = {
    defaultPageSize: _pager_props.default.pageSize
};

function PageSizeSmall(props) {
    var _useState = (0, _hooks.useState)(function() {
            return void 0 !== props.pageSize ? props.pageSize : props.defaultPageSize
        }),
        _useState2 = _slicedToArray(_useState, 2),
        __state_pageSize = _useState2[0];
    _useState2[1];
    var _useState3 = (0, _hooks.useState)(10),
        _useState4 = _slicedToArray(_useState3, 2),
        __state_minWidth = _useState4[0],
        __state_setMinWidth = _useState4[1];
    var __width = (0, _hooks.useCallback)(function() {
        return (0, _calculate_values_fitted_width.calculateValuesFittedWidth)(__state_minWidth, props.pageSizes.map(function(p) {
            return p.value
        }))
    }, [__state_minWidth, props.pageSizes]);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var _props$pageSize$paren = _objectSpread(_objectSpread({}, props), {}, {
                pageSize: void 0 !== props.pageSize ? props.pageSize : __state_pageSize,
                parentRef: props.parentRef.current
            }),
            restProps = (_props$pageSize$paren.defaultPageSize, _props$pageSize$paren.pageSize, _props$pageSize$paren.pageSizeChange, _props$pageSize$paren.pageSizes, _props$pageSize$paren.parentRef, _objectWithoutProperties(_props$pageSize$paren, _excluded));
        return restProps
    }, [props, __state_pageSize]);
    (0, _hooks.useEffect)(function() {
        __state_setMinWidth(function(__state_minWidth) {
            return (0, _get_element_width.getElementMinWidth)(props.parentRef.current) || __state_minWidth
        })
    }, [__state_minWidth, props.pageSize, __state_pageSize, props.pageSizeChange, props.pageSizes, props.defaultPageSize]);
    return viewFunction({
        props: _objectSpread(_objectSpread({}, props), {}, {
            pageSize: void 0 !== props.pageSize ? props.pageSize : __state_pageSize
        }),
        width: __width(),
        restAttributes: __restAttributes()
    })
}
PageSizeSmall.defaultProps = _objectSpread({}, PageSizeSmallPropsType);
