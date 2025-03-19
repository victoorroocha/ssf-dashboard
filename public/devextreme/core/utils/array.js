/**
 * DevExtreme (core/utils/array.js)
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
exports.wrapToArray = exports.uniqueValues = exports.removeDuplicates = exports.normalizeIndexes = exports.merge = exports.isEmpty = exports.intersection = exports.inArray = exports.groupBy = exports.find = void 0;
var _type = require("./type");
var _iterator = require("./iterator");
var _object = require("./object");
var _config = _interopRequireDefault(require("../config"));
var _browser = _interopRequireDefault(require("./browser"));

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

function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread()
}

function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
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

function _iterableToArray(iter) {
    if ("undefined" !== typeof Symbol && null != iter[Symbol.iterator] || null != iter["@@iterator"]) {
        return Array.from(iter)
    }
}

function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) {
        return _arrayLikeToArray(arr)
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
var isIE11 = _browser.default.msie && parseInt(_browser.default.version) <= 11;
var isEmpty = function(entity) {
    return Array.isArray(entity) && !entity.length
};
exports.isEmpty = isEmpty;
var wrapToArray = function(entity) {
    return Array.isArray(entity) ? entity : [entity]
};
exports.wrapToArray = wrapToArray;
var inArray = function(value, object) {
    if (!object) {
        return -1
    }
    var array = Array.isArray(object) ? object : object.toArray();
    return array.indexOf(value)
};
exports.inArray = inArray;
var intersection = function(a, b) {
    if (!Array.isArray(a) || 0 === a.length || !Array.isArray(b) || 0 === b.length) {
        return []
    }
    var result = [];
    (0, _iterator.each)(a, function(_, value) {
        var index = inArray(value, b);
        if (index !== -1) {
            result.push(value)
        }
    });
    return result
};
exports.intersection = intersection;
var uniqueValues = function(data) {
    if (isIE11) {
        return data.filter(function(item, position) {
            return data.indexOf(item) === position
        })
    }
    return _toConsumableArray(new Set(data))
};
exports.uniqueValues = uniqueValues;
var removeDuplicates = function(from, what) {
    if (!Array.isArray(from) || 0 === from.length) {
        return []
    }
    var result = from.slice();
    if (!Array.isArray(what) || 0 === what.length) {
        return result
    }(0, _iterator.each)(what, function(_, value) {
        var index = inArray(value, result);
        result.splice(index, 1)
    });
    return result
};
exports.removeDuplicates = removeDuplicates;
var normalizeIndexes = function(items, indexParameterName, currentItem, needIndexCallback) {
    var indexedItems = {};
    var parameterIndex = 0;
    var useLegacyVisibleIndex = (0, _config.default)().useLegacyVisibleIndex;
    (0, _iterator.each)(items, function(index, item) {
        index = item[indexParameterName];
        if (index >= 0) {
            indexedItems[index] = indexedItems[index] || [];
            if (item === currentItem) {
                indexedItems[index].unshift(item)
            } else {
                indexedItems[index].push(item)
            }
        } else {
            item[indexParameterName] = void 0
        }
    });
    if (!useLegacyVisibleIndex) {
        (0, _iterator.each)(items, function() {
            if (!(0, _type.isDefined)(this[indexParameterName]) && (!needIndexCallback || needIndexCallback(this))) {
                while (indexedItems[parameterIndex]) {
                    parameterIndex++
                }
                indexedItems[parameterIndex] = [this];
                parameterIndex++
            }
        })
    }
    parameterIndex = 0;
    (0, _object.orderEach)(indexedItems, function(index, items) {
        (0, _iterator.each)(items, function() {
            if (index >= 0) {
                this[indexParameterName] = parameterIndex++
            }
        })
    });
    if (useLegacyVisibleIndex) {
        (0, _iterator.each)(items, function() {
            if (!(0, _type.isDefined)(this[indexParameterName]) && (!needIndexCallback || needIndexCallback(this))) {
                this[indexParameterName] = parameterIndex++
            }
        })
    }
    return parameterIndex
};
exports.normalizeIndexes = normalizeIndexes;
var merge = function(array1, array2) {
    for (var i = 0; i < array2.length; i++) {
        array1[array1.length] = array2[i]
    }
    return array1
};
exports.merge = merge;
var find = function(array, condition) {
    for (var i = 0; i < array.length; i++) {
        if (condition(array[i])) {
            return array[i]
        }
    }
};
exports.find = find;
var groupBy = function(array, cb) {
    return array.reduce(function(result, item) {
        return _objectSpread(_objectSpread({}, result), {}, _defineProperty({}, cb(item), [].concat(_toConsumableArray(result[cb(item)] || []), [item])))
    }, {})
};
exports.groupBy = groupBy;
