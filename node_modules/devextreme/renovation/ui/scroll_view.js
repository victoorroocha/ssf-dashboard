/**
 * DevExtreme (renovation/ui/scroll_view.js)
 * Version: 20.2.13
 * Build date: Fri Apr 07 2023
 *
 * Copyright (c) 2012 - 2023 Developer Express Inc. ALL RIGHTS RESERVED
 * Read about DevExtreme licensing here: https://js.devexpress.com/Licensing/
 */
"use strict";
exports.ScrollViewProps = exports.ScrollView = void 0;
exports.defaultOptions = defaultOptions;
exports.viewFunction = exports.getRelativeLocation = exports.ensureLocation = void 0;
var _subscribe_to_event = require("../utils/subscribe_to_event");
var _type = require("../../core/utils/type");
var _widget = require("./common/widget");
var _base_props = _interopRequireDefault(require("../utils/base_props"));
var _combine_classes = require("../utils/combine_classes");
var _utils = require("../../core/options/utils");
var Preact = _interopRequireWildcard(require("preact"));
var _hooks = require("preact/hooks");
var _compat = require("preact/compat");
var _excluded = ["children", "direction", "disabled", "height", "onScroll", "rtlEnabled", "width"];

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

function _typeof(obj) {
    "@babel/helpers - typeof";
    return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
        return typeof obj
    } : function(obj) {
        return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj
    }, _typeof(obj)
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
var DIRECTION_VERTICAL = "vertical";
var DIRECTION_HORIZONTAL = "horizontal";
var DIRECTION_BOTH = "both";
var SCROLLABLE_CONTENT_CLASS = "dx-scrollable-content";
var ensureLocation = function(location) {
    if ((0, _type.isNumeric)(location)) {
        return {
            left: location,
            top: location
        }
    }
    return _objectSpread({
        top: 0,
        left: 0
    }, location)
};
exports.ensureLocation = ensureLocation;
var getRelativeLocation = function(element) {
    var result = {
        top: 0,
        left: 0
    };
    var targetElement = element;
    while (!targetElement.matches(".".concat(SCROLLABLE_CONTENT_CLASS))) {
        result.top += targetElement.offsetTop;
        result.left += targetElement.offsetLeft;
        targetElement = targetElement.offsetParent
    }
    return result
};
exports.getRelativeLocation = getRelativeLocation;
var viewFunction = function(_ref) {
    var containerRef = _ref.containerRef,
        contentRef = _ref.contentRef,
        cssClasses = _ref.cssClasses,
        _ref$props = _ref.props,
        children = _ref$props.children,
        disabled = _ref$props.disabled,
        height = _ref$props.height,
        rtlEnabled = _ref$props.rtlEnabled,
        width = _ref$props.width,
        restAttributes = _ref.restAttributes;
    return Preact.h(_widget.Widget, _extends({
        classes: cssClasses,
        disabled: disabled,
        rtlEnabled: rtlEnabled,
        height: height,
        width: width
    }, restAttributes), Preact.h("div", {
        className: "dx-scrollable-wrapper"
    }, Preact.h("div", {
        className: "dx-scrollable-container",
        ref: containerRef
    }, Preact.h("div", {
        className: SCROLLABLE_CONTENT_CLASS,
        ref: contentRef
    }, children))))
};
exports.viewFunction = viewFunction;
var ScrollViewProps = {
    direction: DIRECTION_VERTICAL
};
exports.ScrollViewProps = ScrollViewProps;
var ScrollViewPropsType = {
    direction: ScrollViewProps.direction,
    disabled: _base_props.default.disabled
};
var ScrollView = (0, _compat.forwardRef)(function(props, ref) {
    var __contentRef = (0, _hooks.useRef)();
    var __containerRef = (0, _hooks.useRef)();
    var __getBoundaryProps = (0, _hooks.useCallback)(function() {
        var _scrollOffset = __scrollOffset(),
            left = _scrollOffset.left,
            top = _scrollOffset.top;
        var _containerRef$curren = __containerRef.current,
            clientHeight = _containerRef$curren.clientHeight,
            clientWidth = _containerRef$curren.clientWidth,
            scrollHeight = _containerRef$curren.scrollHeight,
            scrollWidth = _containerRef$curren.scrollWidth;
        var boundaryProps = {};
        if (__isDirection(DIRECTION_HORIZONTAL) || __isDirection(DIRECTION_BOTH)) {
            boundaryProps.reachedLeft = left <= 0;
            boundaryProps.reachedRight = Math.round(left) >= scrollWidth - clientWidth
        }
        if (__isDirection(DIRECTION_VERTICAL) || __isDirection(DIRECTION_BOTH)) {
            boundaryProps.reachedTop = top <= 0;
            boundaryProps.reachedBottom = top >= scrollHeight - clientHeight
        }
        return boundaryProps
    }, [props.direction]);
    var __isDirection = (0, _hooks.useCallback)(function(direction) {
        var currentDirection = props.direction;
        if (direction === DIRECTION_VERTICAL) {
            return currentDirection !== DIRECTION_HORIZONTAL
        }
        if (direction === DIRECTION_HORIZONTAL) {
            return currentDirection !== DIRECTION_VERTICAL
        }
        return currentDirection === direction
    }, [props.direction]);
    var __cssClasses = (0, _hooks.useCallback)(function() {
        var _classesMap;
        var direction = props.direction;
        var classesMap = (_classesMap = {
            "dx-scrollview": true,
            "dx-scrollable": true
        }, _defineProperty(_classesMap, "dx-scrollable-".concat(direction), true), _defineProperty(_classesMap, "dx-scrollable-native", true), _defineProperty(_classesMap, "dx-scrollable-native-generic", true), _classesMap);
        return (0, _combine_classes.combineClasses)(classesMap)
    }, [props.direction]);
    var __getScrollBarSize = (0, _hooks.useCallback)(function(dimension) {
        return __containerRef.current["offset".concat(dimension)] - __containerRef.current["client".concat(dimension)]
    }, []);
    var __getScrollLocation = (0, _hooks.useCallback)(function(element, scrollOffset, direction) {
        var dimension = direction === DIRECTION_VERTICAL ? "Height" : "Width";
        var prop = direction === DIRECTION_VERTICAL ? "top" : "left";
        var relativeLocation = getRelativeLocation(element)[prop];
        var scrollBarSize = __getScrollBarSize(dimension);
        var containerSize = __containerRef.current["offset".concat(dimension)];
        var elementOffset = element["offset".concat(dimension)];
        var scrollOffsetBegin = scrollOffset[prop];
        var scrollOffsetEnd = scrollOffset[direction === DIRECTION_VERTICAL ? "bottom" : "right"];
        var offset = __scrollOffset()[prop];
        if (relativeLocation < offset + scrollOffsetBegin) {
            if (elementOffset < containerSize - scrollOffsetBegin - scrollOffsetEnd) {
                return relativeLocation - scrollOffsetBegin
            }
            return relativeLocation + elementOffset - containerSize + scrollOffsetEnd + scrollBarSize
        }
        if (relativeLocation + elementOffset >= offset + containerSize - scrollOffsetEnd - scrollBarSize) {
            if (elementOffset < containerSize - scrollOffsetBegin - scrollOffsetEnd) {
                return relativeLocation + elementOffset + scrollBarSize - containerSize + scrollOffsetEnd
            }
            return relativeLocation - scrollOffsetBegin
        }
        return offset
    }, []);
    var __restAttributes = (0, _hooks.useCallback)(function() {
        var restProps = (props.children, props.direction, props.disabled, props.height, props.onScroll, props.rtlEnabled, props.width, _objectWithoutProperties(props, _excluded));
        return restProps
    }, [props]);
    var __content = (0, _hooks.useCallback)(function() {
        return __contentRef.current
    }, []);
    var __scrollBy = (0, _hooks.useCallback)(function(distance) {
        var location = ensureLocation(distance);
        if (__isDirection(DIRECTION_VERTICAL)) {
            __containerRef.current.scrollTop = Math.round(__scrollOffset().top + location.top)
        }
        if (__isDirection(DIRECTION_HORIZONTAL)) {
            __containerRef.current.scrollLeft = Math.round(__scrollOffset().left + location.left)
        }
    }, [props.direction]);
    var __scrollTo = (0, _hooks.useCallback)(function(targetLocation) {
        var location = ensureLocation(targetLocation);
        __scrollBy({
            left: location.left - __scrollOffset().left,
            top: location.top - __scrollOffset().top
        })
    }, [props.direction]);
    var __scrollToElement = (0, _hooks.useCallback)(function(element, offset) {
        if (element.closest(".".concat(SCROLLABLE_CONTENT_CLASS))) {
            var scrollOffset = _objectSpread({
                top: 0,
                left: 0,
                right: 0,
                bottom: 0
            }, offset);
            __scrollTo({
                top: __getScrollLocation(element, scrollOffset, DIRECTION_VERTICAL),
                left: __getScrollLocation(element, scrollOffset, DIRECTION_HORIZONTAL)
            })
        }
    }, [props.direction]);
    var __scrollHeight = (0, _hooks.useCallback)(function() {
        return __content().offsetHeight
    }, []);
    var __scrollWidth = (0, _hooks.useCallback)(function() {
        return __content().offsetWidth
    }, []);
    var __scrollOffset = (0, _hooks.useCallback)(function() {
        return {
            left: __containerRef.current.scrollLeft,
            top: __containerRef.current.scrollTop
        }
    }, []);
    var __scrollTop = (0, _hooks.useCallback)(function() {
        return __scrollOffset().top
    }, []);
    var __scrollLeft = (0, _hooks.useCallback)(function() {
        return __scrollOffset().left
    }, []);
    var __clientHeight = (0, _hooks.useCallback)(function() {
        return __containerRef.current.clientHeight
    }, []);
    var __clientWidth = (0, _hooks.useCallback)(function() {
        return __containerRef.current.clientWidth
    }, []);
    (0, _hooks.useEffect)(function() {
        return (0, _subscribe_to_event.subscribeToScrollEvent)(__containerRef.current, function(event) {
            var _props$onScroll;
            return null === (_props$onScroll = props.onScroll) || void 0 === _props$onScroll ? void 0 : _props$onScroll.call(props, _objectSpread({
                event: event,
                scrollOffset: __scrollOffset()
            }, __getBoundaryProps()))
        })
    }, [props.onScroll, props.direction]);
    (0, _hooks.useImperativeHandle)(ref, function() {
        return {
            content: __content,
            scrollBy: __scrollBy,
            scrollTo: __scrollTo,
            scrollToElement: __scrollToElement,
            scrollHeight: __scrollHeight,
            scrollWidth: __scrollWidth,
            scrollOffset: __scrollOffset,
            scrollTop: __scrollTop,
            scrollLeft: __scrollLeft,
            clientHeight: __clientHeight,
            clientWidth: __clientWidth
        }
    }, [__content, __scrollBy, __scrollTo, __scrollToElement, __scrollHeight, __scrollWidth, __scrollOffset, __scrollTop, __scrollLeft, __clientHeight, __clientWidth]);
    return viewFunction({
        props: _objectSpread({}, props),
        contentRef: __contentRef,
        containerRef: __containerRef,
        cssClasses: __cssClasses(),
        restAttributes: __restAttributes()
    })
});
exports.ScrollView = ScrollView;

function __createDefaultProps() {
    return _objectSpread({}, ScrollViewPropsType)
}
ScrollView.defaultProps = __createDefaultProps();
var __defaultOptionRules = [];

function defaultOptions(rule) {
    __defaultOptionRules.push(rule);
    ScrollView.defaultProps = _objectSpread(_objectSpread({}, __createDefaultProps()), (0, _utils.convertRulesToOptions)(__defaultOptionRules))
}
