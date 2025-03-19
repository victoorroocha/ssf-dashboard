/**
 * DevExtreme (renovation/ui/button.j.js)
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
exports.default = void 0;
var _component_registrator = _interopRequireDefault(require("../../core/component_registrator"));
var _button = _interopRequireDefault(require("../preact_wrapper/button"));
var _button2 = require("./button");

function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        "default": obj
    }
}

function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) {
            descriptor.writable = true
        }
        Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor)
    }
}

function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) {
        _defineProperties(Constructor.prototype, protoProps)
    }
    if (staticProps) {
        _defineProperties(Constructor, staticProps)
    }
    Object.defineProperty(Constructor, "prototype", {
        writable: false
    });
    return Constructor
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

function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    _setPrototypeOf(subClass, superClass)
}

function _setPrototypeOf(o, p) {
    _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function(o, p) {
        o.__proto__ = p;
        return o
    };
    return _setPrototypeOf(o, p)
}
var Button = function(_BaseComponent) {
    _inheritsLoose(Button, _BaseComponent);

    function Button() {
        return _BaseComponent.apply(this, arguments) || this
    }
    var _proto = Button.prototype;
    _proto.getProps = function() {
        var props = _BaseComponent.prototype.getProps.call(this);
        props.template = this._createTemplateComponent(props, props.template);
        props.onKeyDown = this._wrapKeyDownHandler(props.onKeyDown);
        return props
    };
    _proto.focus = function() {
        return this.viewRef.focus()
    };
    _proto._getActionConfigs = function() {
        return {
            onClick: {
                excludeValidators: ["readOnly"]
            },
            onSubmit: {},
            onContentReady: {
                excludeValidators: ["disabled", "readOnly"]
            }
        }
    };
    _createClass(Button, [{
        key: "_propsInfo",
        get: function() {
            return {
                twoWay: [],
                allowNull: ["accessKey"],
                elements: []
            }
        }
    }, {
        key: "_viewComponent",
        get: function() {
            return _button2.Button
        }
    }]);
    return Button
}(_button.default);
exports.default = Button;
(0, _component_registrator.default)("dxButton", Button);
module.exports = exports.default;
